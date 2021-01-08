<?php
$config = json_decode(file_get_contents("config.json", true), true);
/*
	QUESTS
*/
//NOW SET IN THE QUESTS TABLE IN THE MYSQL DATABASE
/*
	REWARDS
*/
//SMALL CHEST
$chest1minOrbs = $config["chest1minOrbs"];
$chest1maxOrbs = $config["chest1maxOrbs"];
$chest1minDiamonds = $config["chest1minDiamonds"];
$chest1maxDiamonds = $config["chest1maxDiamonds"];
$chest1minShards = $config["chest1minShards"];
$chest1maxShards = $config["chest1maxShards"];
$chest1minKeys = $config["chest1minKeys"];
$chest1maxKeys = $config["chest1maxKeys"];
//BIG CHEST
$chest2minOrbs = $config["chest2minOrbs"];
$chest2maxOrbs = $config["chest2maxOrbs"];
$chest2minDiamonds = $config["chest2minDiamonds"];
$chest2maxDiamonds = $config["chest2maxDiamonds"];
$chest2minShards = $config["chest2minShards"];
$chest2maxShards = $config["chest2maxShards"]; // THIS VARIABLE IS NAMED IMPROPERLY, A MORE ACCURATE NAME WOULD BE $chest2minItemID AND $chest2maxItemID, BUT I DON'T WANT TO RENAME THIS FOR COMPATIBILITY REASONS... IF YOU'RE GETTING A BLANK CUBE IN YOUR DAILY CHESTS, YOU SET THIS TOO HIGH
$chest2minKeys = $config["chest2minKeys"];
$chest2maxKeys = $config["chest2maxKeys"];
//REWARD TIMES (in seconds)
$chest1wait = $config["chest1wait"];
$chest2wait = $config["chest2wait"];
?>