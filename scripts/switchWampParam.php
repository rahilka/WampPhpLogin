<?php
//3.0.6
require 'config.inc.php';
require 'wampserver.lib.php';

if($_SERVER['argv'][2] == 'create') {
	createWampConfParam($_SERVER['argv'][1],$_SERVER['argv'][3],$_SERVER['argv'][4],$configurationFile);
}
else {
	$wampIniNewContents[$_SERVER['argv'][1]] = $_SERVER['argv'][2];
	wampIniSet($configurationFile, $wampIniNewContents);
}
?>