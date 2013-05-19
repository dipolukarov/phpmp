<?php
/**
 * @author neo <dipolukarov@gmail.com>
 * @version $Id$
 */

function getStatusInfo($conn)
{
	fputs($conn,"status\n");
	while(!feof($conn)) {
		$got =  fgets($conn,1024);
		$got = preg_replace("/\n/","",$got);
		if(strncmp('OK',$got,strlen('OK'))==0) 
			break;
		if(strncmp('ACK',$got,strlen('ACK'))==0) {
			echo "$got<br/>";
			break;
		}
		$el = strtok($got,':');
		$ret["$el"] = strtok("\0");
		$ret["$el"] = preg_replace("/^ /","",$ret["$el"]);
	}
	
	return isset($ret) ? $ret : [];
}

function setNotSetSongFields($song)
{
	if(isset($song['Title'])) {
		if(!isset($song['Track'])) $song['Track'] = '';
		if(!isset($song['Album'])) $song['Album'] = '';
		if(!isset($song['Artist'])) $song['Artist'] = '';
	}

	return $song;
}

function getPlaylistInfo($conn,$song)
{
	global $unknown_string;
	fputs($conn,"playlistinfo $song\n");
	$count = -1;
	while(!feof($conn)) {
		$got =  fgets($conn,1024);
		$got = preg_replace("/\n/","",$got);
		if(strncmp('OK',$got,strlen('OK'))==0) 
			break;
		if(strncmp('ACK',$got,strlen('ACK'))==0) 
			break;
		$el = strtok($got,':');
		if(0==strcmp($el,'file')) {
			if($count>=0) $ret[$count] = setNotSetSongFields($ret[$count]);
			$count++;
		}
		$ret[$count]["$el"] = strtok("\0");
		$ret[$count]["$el"] = preg_replace("/^ /","",$ret[$count]["$el"]);
	}
	
	return isset($ret) ? $ret : [];
}

function printPlaylistInfo($conn,$num,$hide,$spread,$length)
{
	global $colors,$filenames_only;
	$tm = time();
	$start = 0;
	$end = $length-1;
	if($hide) {
		$start = $num-$spread/2;
		$end = $num+$spread/2;
		if($start<0) {
			$end-=$start;
			$start = 0;
		}
		if($end>=$length) {
			$start-=$end-$length+1;
			if($start<0) $start = 0;
			$end = $length-1;
		}
		if($start>0) {
			echo '<tr bgcolor="' , $colors['playlist']['body'] , '">'
					, '<td colspan="2" align="center"><small>'
					, '<a href="playlist.php?hide=0">...</a>'
					, '</small></td></tr>';
		}
		fputs($conn,"command_list_begin\n");
		for($i=$start;$i<=$end;$i++) fputs($conn,"playlistinfo $i\n");
		fputs($conn,"command_list_end\n");
	}
	else {
		if($length>$spread+1) {
			echo '<tr bgcolor="' , $colors['playlist']['body'] , '">'
					, '<td colspan="2" align="center"><small>'
					, '(<a href="playlist.php?hide=1">condense</a>)'
					, '</small></td></tr>';
		}
		fputs($conn,"playlistinfo -1\n");
	}
	$count = $start-1;
	while(!feof($conn)) {
		$got =  fgets($conn,1024);
		$got = preg_replace("/\n/","",$got);
		if(strncmp('OK',$got,strlen('OK'))==0) 
			break;
		if(strncmp('ACK',$got,strlen('ACK'))==0) 
			break;
		$el = strtok($got,':');
		if(0==strcmp($el,'file')) {
			if($count>=$start) {
				if($count>$start) $goto = $count-1;
				else $goto = $count;
				if($filenames_only!='yes' && isset($ret['Name']) && $ret['Name']) {
					$display = $ret['Name'];
				}
				else $display = songInfo2Display($ret);
				$id = $ret['Id'];
				unset($ret);
				if(isset($num) && $num==$count)
					echo '<tr bgcolor="' , $colors['playlist']['current'] , '">';
				else 
					echo '<tr bgcolor="' , $colors['playlist']['body'] , '">';
				echo '<td valign="top"><a name="' , $count , '"><small><a href="playlist.php?hide='
						, $hide , '&command=deleteid ' , $id , '&time=' , $tm , '#' , $goto , '">d</a></small></td>'
						, '<td width="100%"><a href="playlist.php?hide=' , $hide , '&command=playid%20' , $id , '">' , $display , '</a></td></tr>';
			}
			$count++;
		}
		$ret["$el"] = strtok("\0");
		$ret["$el"] = preg_replace("/^ /","",$ret["$el"]);
	}
	if($count>=$start) {
		if($count>$start) $goto = $count-1;
		else $goto = $count;
		if($filenames_only!='yes' && isset($ret['Name']) && $ret['Name']) {
			$display = $ret['Name'];
		}
		else $display = songInfo2Display($ret);
		$id = $ret['Id'];
		if(isset($num) && $num==$count)
			echo '<tr bgcolor="' , $colors['playlist']['current'] , '">';
		else 
			echo '<tr bgcolor="' , $colors['playlist']['body'] , '">';
		echo '<td valign="top"><a name="' , $count , '"><small><a href="playlist.php?hide='
				, $hide , '&command=delete ' , $count , '&time=' , $tm , '#' , $goto , '">d</a></small></td>'
				, '<td width="100%"><a href="playlist.php?hide=' , $hide , '&command=play%20' , $count , '">' , $display , '</a></td></tr>';
	}
	if($hide && $end < $length-1)
		echo '<tr bgcolor="' , $colors['playlist']['body'] , '">'
				, '<td colspan="2" align="center"><small>'
				, '<a href="playlist.php?hide=0">...</a>'
				, '</small></td></tr>';
}

function getLsInfo($conn,$command)
{
	fputs($conn,$command);
	$mcount = -1;
	$dcount = 0;
	$pcount = 0;
	while(!feof($conn)) {
		$got =  fgets($conn,1024);
		$got = preg_replace("/\n/",'',$got);
		if(strncmp('OK',$got,strlen('OK'))==0) 
			break;
		if(strncmp('ACK',$got,strlen('ACK'))==0) {
			print "$got<br/>\n";
			break;
		}
		$el = strtok($got,':');
		if(0==strcmp($el,'directory')) {
			$dir[$dcount] = preg_replace("/^$el: /",'',$got);
			$dcount++;
			continue;
		}
		if(0==strcmp($el,'playlist')) {
			$playlist[$pcount] = preg_replace("/^$el: /",'',$got);
			$pcount++;
			continue;
		}
		if(0==strcmp($el,'file')) {
			if($mcount>=0) $music[$mcount] = setNotSetSongFields($music[$mcount]);
			$mcount++;
		}
		$music[$mcount]["$el"] = preg_replace("/^$el: /",'',$got);
	}
	
	$ret['dir'] = isset($dir) ? $dir : [];
	$ret['music'] = isset($music) ? $music : [];
	$ret['playlist'] = isset($playlist) ? $playlist : [];
	
	return $ret;
}
