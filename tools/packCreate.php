<?php
include "../incl/lib/connection.php";
require "../incl/lib/generatePass.php";
require "../incl/lib/exploitPatch.php";
require "../incl/lib/mainLib.php";
$gs = new mainLib();
$ep = new exploitPatch();
if(!empty($_POST["userName"]) AND !empty($_POST["password"]) AND !empty($_POST["packName"]) AND !empty($_POST["levels"]) AND !empty($_POST["stars"]) AND !empty($_POST["coins"]) AND !empty($_POST["color"])){
	$userName = $ep->remove($_POST["userName"]);
	$password = $ep->remove($_POST["password"]);
	$packName = $ep->remove($_POST["packName"]);
	$levels = $ep->remove($_POST["levels"]);
	$stars = $ep->remove($_POST["stars"]);
	$coins = $ep->remove($_POST["coins"]);
	$color = $ep->remove($_POST["color"]);
	$generatePass = new generatePass();
	$pass = $generatePass->isValidUsrname($userName, $password);
	if ($pass == 1) {
		$query = $db->prepare("SELECT accountID FROM accounts WHERE userName=:userName");	
		$query->execute([':userName' => $userName]);
		$accountID = $query->fetchColumn();
		if($gs->checkPermission($accountID, "toolPackcreate") == false){
			echo "У вас недостаточно прав";
		}else{
			if(!is_numeric($stars) OR !is_numeric($coins) OR $stars > 10 OR $coins > 2){
				exit("Неверное значение звёзд или монет");
			}
			if(strlen($color) != 6){
				exit("Неизвестный цвет");
			}
			$rgb = hexdec(substr($color,0,2)).
				",".hexdec(substr($color,2,2)).
				",".hexdec(substr($color,4,2));
			$lvlsarray = explode(",", $levels);
			foreach($lvlsarray AS &$level){
				if(!is_numeric($level)){
					exit("ID уровня не является числом");
				}
				$query = $db->prepare("SELECT levelName FROM levels WHERE levelID=:levelID");	
				$query->execute([':levelID' => $level]);
				if($query->rowCount() == 0){
					exit("Уровень #$level не существует");
				}
				$levelName = $query->fetchColumn();
				$levelstring .= $levelName . ", ";
			}
			$levelstring = substr($levelstring,0,-2);
			$diff = 0;
			$diffname = "Auto";
			switch($stars){
				case 1:
					$diffname = "Auto";
					$diff = 0;
					break;
				case 2:
					$diffname = "Easy";
					$diff = 1;
					break;
				case 3:
					$diffname = "Normal";
					$diff = 2;
					break;
				case 4:
				case 5:
					$diffname = "Hard";
					$diff = 3;
					break;
				case 6:
				case 7:
					$diffname = "Harder";
					$diff = 4;
					break;
				case 8:
				case 9:
					$diffname = "Insane";
					$diff = 5;
					break;
				case 10:
					$diffname = "Demon";
					$diff = 6;
					break;
			}
			echo "Мап-пак успешно был создан";
			$query = $db->prepare("INSERT INTO mappacks     (name, levels, stars, coins, difficulty, rgbcolors)
													VALUES (:name,:levels,:stars,:coins,:difficulty,:rgbcolors)");
			$query->execute([':name' => $packName, ':levels' => $levels, ':stars' => $stars, ':coins' => $coins, ':difficulty' => $diff, ':rgbcolors' => $rgb]);
			$query = $db->prepare("INSERT INTO modactions  (type, value, timestamp, account, value2, value3, value4, value7) 
													VALUES ('11',:value,:timestamp,:account,:levels, :stars, :coins, :rgb)");
			$query->execute([':value' => $packName, ':timestamp' => time(), ':account' => $accountID, ':levels' => $levels, ':stars' => $stars, ':coins' => $coins, ':rgb' => $rgb]);
		}
	}else{
		echo "Ошибка входа в аккаунт";
	}
}else{
	echo 'Данные не переданы';
}
?>