<?php
/**
 * @author neo <dipolukarov@gmail.com>
 * @version $Id$
 */

include 'config.php';
include 'utils.php';

function secondsToDHMS($seconds)
{
	$days = floor($seconds/86400);
	$remaining_seconds = $seconds-$days*86400;
	$date = date('H:i:s', mktime(0,0,$remaining_seconds));

	return "$days days, $date";
}

$dir = '';
$sort = $default_sort;
EXTRACT($_GET);
$sort_array = explode(',',$sort);
$fp = fsockopen($host,$port,$errno,$errstr,10);
if(!$fp) {
	echo "$errstr ($errno)<br/>\n";
} else {
	while(!feof($fp)) {
		$got =  fgets($fp,1024);
		if(strncmp('OK',$got,strlen('OK'))==0) 
			break;
		print "$got<br/>";
		if(strncmp('ACK',$got,strlen('ACK'))==0) 
			break;
	}
	if(isset($password)) {
		fputs($fp,"password \"$password\"\n");
		while(!feof($fp)) {
			$got =  fgets($fp,1024);
			if(strncmp('OK',$got,strlen('OK'))==0)
				break;
			print "$got<br/>";
			if(strncmp('ACK',$got,strlen('ACK'))==0) 
				break;
		}
	}
	$dir_url = sanitizeForURL($dir);
	displayDirectory($dir,$sort,'Back to Directory',0,0);
	fputs($fp,"stats\n");
	while(!feof($fp)) {
		$got =  fgets($fp,1024);
		if(strncmp('OK',$got,strlen('OK'))==0)
			break;
		if(strncmp('ACK',$got,strlen('ACK'))==0) 
			break;
		$el = strtok($got,':');
		$got = strtok("\0");
		$stats["$el"] = preg_replace("/^ /",'',$got);
	}
	fclose($fp);
	echo '<br/><b>Artists</b>: ' , $stats['artists'] , '<br/>'
			, '<b>Albums</b>: ' , $stats['albums'] , '<br/>'
			, '<b>Songs</b>: ' , $stats['songs'] , '<br/>';
	//print "<b>Songs Played</b>: " . $stats["songs_played"] . "<br>";			
	$DHMS = secondsToDHMS($stats['playtime']);
	echo '<b>Play Time</b>: ' , $DHMS , '<br/>';
	$DHMS = secondsToDHMS($stats['uptime']);
	echo '<b>Uptime</b>: ' , $DHMS , '<br/>'
			, '<b>DB Updated</b>: ' , date('F j, Y, g:i a', $stats['db_update']) , '<br/>';
	$DHMS = secondsToDHMS($stats['db_playtime']);
	echo '<b>Total DB playtime</b>: ' , $DHMS , '<br/>';
}
