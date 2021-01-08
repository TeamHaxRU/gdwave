<?php
class GJPCheck {
	public function check($gjp, $accountID) {
		include dirname(__FILE__)."/connection.php";;
		include dirname(__FILE__)."/../../config/security.php";
		require_once dirname(__FILE__)."/XORCipher.php";
		require_once dirname(__FILE__)."/generatePass.php";
		$xor = new XORCipher();
		$gjpdecode = str_replace("_","/",$gjp);
		$gjpdecode = str_replace("-","+",$gjpdecode);
		$gjpdecode = base64_decode($gjpdecode);
		$gjpdecode = $xor->cipher($gjpdecode,37526);
		$generatePass = new generatePass();
		if($generatePass->isValid($accountID, $gjpdecode) == 1 AND $sessionGrants){
			$query = $db->prepare("INSERT INTO actions (type, value, timestamp) VALUES (10, :accountID, :timestamp)");
			$query->execute([':accountID' => $accountID, ':timestamp' => time()]);
		}
		$isValid = $generatePass->isValid($accountID, $gjpdecode);
		if($isValid) {
            $query = $db->prepare("SELECT * FROM accounts WHERE accountID = :accountID");
            $query->execute([':accountID' => $accountID]);
            $ud = $query->fetchAll()[0];
            $diffOnline = time() - $ud["onlineTo"];
            if($diffOnline > 300) {
                $diffOnline = 300;
            }
            $query2 = $db->prepare("UPDATE accounts SET onlineTo = :onlineTo, onlineTime = onlineTime + :onlineTime WHERE accountID = :accountID");
            $query2->execute([':onlineTo' => time(), ':onlineTime' => $diffOnline, ':accountID' => $accountID]);
        }
		return $isValid;
	}
}
?>