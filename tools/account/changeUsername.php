<?php
include "../../incl/lib/connection.php";
require "../../incl/lib/generatePass.php";
require_once "../../incl/lib/exploitPatch.php";
$ep = new exploitPatch();
//here im getting all the data
$userName = $ep->remove($_POST["userName"]);
$newusr = $ep->remove($_POST["newusr"]);
$password = $ep->remove($_POST["password"]);
if($userName != "" AND $newusr != "" AND $password != ""){
	$generatePass = new generatePass();
	$pass = $generatePass->isValidUsrname($userName, $password);
	if ($pass == 1) {
		$query = $db->prepare("UPDATE accounts SET username=:newusr WHERE userName=:userName");	
		$query->execute([':newusr' => $newusr, ':userName' => $userName]);
		if($query->rowCount()==0){
			echo "Ошибка входа в аккаунт";
		}else{
			echo "Ваш ник успешно изменён";
		}
	}else{
		echo "Ошибка входа в аккаунт";
	}
}else{
	echo 'Данные не переданы';
}
?>