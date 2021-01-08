<?php
function chkarray($source){
	if($source == ""){
		$target = "0";
	}else{
		$target = $source;
	}
	return $target;
}
//error_reporting(0);
include "../incl/lib/connection.php";
require "../incl/lib/XORCipher.php";
$config = json_decode(file_get_contents("../config/config.json", true), true);
if(!$config["canReupload"]) {
	exit("В конфигурации сервера отключена возможность перевыложить уровень");
}
$xc = new XORCipher();
if(!empty($_POST["levelid"])){
	$levelID = $_POST["levelid"];
	$levelID = preg_replace("/[^0-9]/", '', $levelID);
	$url = $_POST["server"];
	$post = ['gameVersion' => '21', 'binaryVersion' => '33', 'gdw' => '0', 'levelID' => $levelID, 'secret' => 'Wmfd2893gb7', 'inc' => '1', 'extras' => '0'];
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	$result = curl_exec($ch);
	curl_close($ch);
	if($result == "" OR $result == "-1" OR $result == "No no no"){
		if($result==""){
			echo "Невозможно подключиться к указанному серверу";
		}else if($result=="-1"){
			echo "Указан несуществующий уровень";
		}else{
			echo "Неизвестная ошибка";
		}
		echo "Ошибка #$result";
	}else{
		$level = explode('#', $result)[0];
		$resultarray = explode(':', $level);
		$levelarray = array();
		$x = 1;
		foreach($resultarray as &$value){
			if ($x % 2 == 0) {
				$levelarray["a$arname"] = $value;
			}else{
				$arname = $value;
			}
			$x++;
		}
		//echo $result;
		if($_POST["debug"] == 1){
			echo $result;
			var_dump($levelarray);
		}
		if($levelarray["a4"] == ""){
			echo "Ошибка #".htmlspecialchars($result,ENT_QUOTES);
		}
		$uploadDate = time();
		//old levelString
		$levelString = chkarray($levelarray["a4"]);
		$gameVersion = chkarray($levelarray["a13"]);
		if(substr($levelString,0,2) == 'eJ'){
			$levelString = str_replace("_","/",$levelString);
			$levelString = str_replace("-","+",$levelString);
			$levelString = gzuncompress(base64_decode($levelString));
			if($gameVersion > 18){
				$gameVersion = 18;
			}
		}
		//check if exists
		$query = $db->prepare("SELECT count(*) FROM levels WHERE originalReup = :lvl OR original = :lvl");
		$query->execute([':lvl' => $levelarray["a1"]]);
		if($query->fetchColumn() == 0){
			$parsedurl = parse_url($url);
			if($parsedurl["host"] == $_SERVER['SERVER_NAME']){
				exit("Нельзя перенести уровень с серверов GDWave");
			}
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
				$hostname = $_SERVER['HTTP_CLIENT_IP'];
			} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$hostname = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} else {
				$hostname = $_SERVER['REMOTE_ADDR'];
			}
			//values
			$twoPlayer = chkarray($levelarray["a31"]);
			$songID = chkarray($levelarray["a35"]);
			$coins = chkarray($levelarray["a37"]);
			$reqstar = chkarray($levelarray["a39"]);
			$extraString = chkarray($levelarray["a36"]);
			$starStars = chkarray($levelarray["a18"]);
			$isLDM = chkarray($levelarray["a40"]);
			$password = chkarray($xc->cipher(base64_decode($levelarray["a27"]),26364));
			$starCoins = 0;
			$starDiff = 0;
			$starDemon = 0;
			$starAuto = 0;
			if($parsedurl["host"] == "www.boomlings.com"){
				if($starStars != 0){
					$starCoins = chkarray($levelarray["a38"]);
					$starDiff = chkarray($levelarray["a9"]);
					$starDemon = chkarray($levelarray["a17"]);
					$starAuto = chkarray($levelarray["a25"]);
				}
			}else{
				$starStars = 0;
			}
			$targetUserID = chkarray($levelarray["a6"]);
			//linkacc
			$query = $db->prepare("SELECT accountID, userID FROM links WHERE targetUserID=:target");
			$query->execute([':target' => $targetUserID]);
			if($query->rowCount() == 0){
				$userID = 388;
				$extID = 263;
			}else{
				$userInfo = $query->fetchAll()[0];
				$userID = $userInfo["userID"];
				$extID = $userInfo["accountID"];
			}
			//query
			$query = $db->prepare("INSERT INTO levels (levelName, gameVersion, binaryVersion, userName, levelDesc, levelVersion, levelLength, audioTrack, auto, password, original, twoPlayer, songID, objects, coins, requestedStars, extraString, levelString, levelInfo, secret, uploadDate, updateDate, originalReup, userID, extID, unlisted, hostname, starStars, starCoins, starDifficulty, starDemon, starAuto, isLDM)
												VALUES (:name ,:gameVersion, '27', 'Reupload', :desc, :version, :length, :audiotrack, '0', :password, :originalReup, :twoPlayer, :songID, '0', :coins, :reqstar, :extraString, :levelString, '0', '0', '$uploadDate', '$uploadDate', :originalReup, :userID, :extID, '0', :hostname, :starStars, :starCoins, :starDifficulty, :starDemon, :starAuto, :isLDM)");
			$query->execute([':password' => $password, ':starDemon' => $starDemon, ':starAuto' => $starAuto, ':gameVersion' => $gameVersion, ':name' => $levelarray["a2"], ':desc' => $levelarray["a3"], ':version' => $levelarray["a5"], ':length' => $levelarray["a15"], ':audiotrack' => $levelarray["a12"], ':twoPlayer' => $twoPlayer, ':songID' => $songID, ':coins' => $coins, ':reqstar' => $reqstar, ':extraString' => $extraString, ':levelString' => "", ':originalReup' => $levelarray["a1"], ':hostname' => $hostname, ':starStars' => $starStars, ':starCoins' => $starCoins, ':starDifficulty' => $starDiff, ':userID' => $userID, ':extID' => $extID, ':isLDM' => $isLDM]);
			$levelID = $db->lastInsertId();
			file_put_contents("../data/levels/$levelID",$levelString);
			echo "Уровень выложен с ID = $levelID";
		}else{
			echo "Этот уровень уже был выложен";
		}
	}
}else{
	echo 'Данные не переданы';
}
?>