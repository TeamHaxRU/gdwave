<?php
file_put_contents("debug.json", json_encode($_POST));
function pluginwork($name = "", $params = array()) {
    $name=str_replace(array("19", "20", "21", "22"), array("", "", "", ""), $name);
    if($name == "" or count($params) == 0) {
        return("");
    }
    $files = scandir("plugins");
	foreach($files as $file) {
		if(pathinfo($file, PATHINFO_EXTENSION) == "php" AND $file != "index.php"){
			include("plugins/".$file);
		}
	}
}
?>