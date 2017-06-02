<?php
//Update 3.0.4
//Possibility to copy results into clipboard
require 'config.inc.php';
$only_process = false;
$message = '';
if(!empty($_SERVER['argv'][2]) && $_SERVER['argv'][2] == $c_mysqlService) {
	$port = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '3306';
	$only_process = true;
}
else
	$port = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '80';

$message .=  "***** Test which uses port ".$port." *****\n\n";
$message .=  "===== Tested by command netstat filtered on port ".$port." =====\n\n";
//Port tested by netstat for TCP and TCPv6
$tcp = array('TCP', 'TCPv6');
foreach($tcp as $value) {
$command = 'start /b /wait netstat -anop '.$value.' | find ":'.$port.'"';
ob_start();
passthru($command);
$output = ob_get_contents();
ob_end_clean();
if(!empty($output)) {
	$message .=  "\nTest for ".$value."\n";
	if(preg_match("~^[ \t]*TCP.*:".$port." .*LISTENING[ \t]*([0-9]{1,5}).*$~m", $output, $pid) > 0) {
		$message .=  "Your port ".$port." is used by a processus with PID = ".$pid[1]."\n";
		$command = 'start /b /wait tasklist /FI "PID eq '.$pid[1].'" /FO TABLE /NH';
		ob_start();
		passthru($command);
		$output = ob_get_contents();
		ob_end_clean();
		if(!empty($output)) {
			if(preg_match("~^(.+[^ \t])[ \t]+".$pid[1]." ([a-zA-Z]+[^ \t]*).+$~m", $output, $matches) > 0) {
				$message .=  "The processus of PID ".$pid[1]." is '".$matches[1]."' Session: ".$matches[2]."\n";
				$command = 'start /b /wait tasklist /SVC | find "'.$pid[1].'"';
				ob_start();
				passthru($command);
				$output = ob_get_contents();
				ob_end_clean();
				if(!empty($output)) {
					if(preg_match("~^(.+[^ \t])[ \t]+".$pid[1]." ([a-zA-Z]+[^ \t]*).+$~m", $output, $matches) > 0) {
						$message .=  "The service of PID ".$pid[1]." for '".$matches[1]."' is '".$matches[2]."'\n";
						if($matches[2] == $_SERVER['argv'][2])
							$message .=  "This service is from Wampserver - It is correct\n";
						else
							$message .=  "*** ERROR *** This service IS NOT from Wampserver - Should be: '".$_SERVER['argv'][2]."'\n";
					}
				}
			}
			else
				$message .=  "The processus of PID ".$pid[1]." is not found with tasklist\n";
		}
	}
	else
	 	$message .=  "Port ".$port." is not found associated with TCP protocol\n";
}
else
	$message .=  "Port ".$port." is not found associated with TCP protocol\n";
}

if(!$only_process) {
	$message .=  "\n===== Tested by attempting to open a socket on port ".$port." =====\n\n";
	//Port tested by open socket
	$fp = @fsockopen("127.0.0.1", $port, $errno, $errstr, 1);
	$out = "GET / HTTP/1.1\r\n";
	$out .= "Host: 127.0.0.1\r\n";
	$out .= "Connection: Close\r\n\r\n";
	if ($fp) {
		$message .=   "Your port ".$port." is actually used by :\n\n";
		fwrite($fp, $out);
		while (!feof($fp)) {
			$line = fgets($fp, 128);
			if (preg_match('#Server:#',$line))	{
				$message .=  $line;
				$gotInfo = 1;
			}
		}
		fclose($fp);
		if ($gotInfo != 1)
		$message .=  "Server information not available (might be Skype or IIS).\n";
	}
	else {
		$message .=  "Your port ".$port." is not actually used.\n";
	}
}
echo $message;
	if(!empty($message)) {
		echo "\n--- Do you want to copy the results into Clipboard?
--- Type 'y' to confirm - Press ENTER to continue...";
    $confirm = trim(fgetc(STDIN));
		$confirm = strtolower(trim($confirm ,'\''));
		if ($confirm == 'y') {
			$fp = fopen("temp.txt",'w');
			fwrite($fp,$message);
			fclose($fp);
			$command = 'type temp.txt | clip';
			`$command`;
			$command = 'del temp.txt';
			`$command`;
		}
		exit();
 	}

echo '

Press Enter to exit...';
trim(fgets(STDIN));

?>