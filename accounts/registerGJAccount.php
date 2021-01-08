<?php
include "../incl/lib/connection.php";
require_once "../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
if($_POST["userName"] != ""){
	//here im getting all the data
	$userName = $ep->remove($_POST["userName"]);
	$password = $ep->remove($_POST["password"]);
	$email = $ep->remove($_POST["email"]);
	$secret = "";
	//checking if name is taken
	$query2 = $db->prepare("SELECT count(*) FROM accounts WHERE userName LIKE :userName");
	$query2->execute([':userName' => $userName]);
	$regusrs = $query2->fetchColumn();
	if ($regusrs > 0) {
		echo "-2";
	}else{
		$hashpass = password_hash($password, PASSWORD_DEFAULT);
		$query = $db->prepare("INSERT INTO accounts (userName, password, email, secret, saveData, registerDate, saveKey, onlineTo)
		VALUES (:userName, :password, :email, :secret, '', :time, '', :onlineTo)");
		$query->execute([':userName' => $userName, ':password' => $hashpass, ':email' => $email, ':secret' => $secret, ':time' => time(), ':onlineTo' => time()]);
		$query = $db->prepare("INSERT INTO roleassign (roleID, accountID) VALUES (:roleID, :accountID)");
		$query->execute([':roleID' => 1, ':accountID' => $db->lastInsertId()]);
		echo "1";
	}
}
?>