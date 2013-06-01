<?php
/**
 * @author neo <dipolukarov@gmail.com>
 * @version $Id$
 */

include 'sort.php';

function lsinfo2playlistTable($lsinfo,$sort)
{
	$pic = 0;
	$pcount = count($lsinfo['playlist']);
	if($pcount) usort($lsinfo['playlist'],'strcasecmp');
	for($i=0;$i<$pcount;$i++) {
		$dirent = $lsinfo['playlist'][$i];
		$dirstr = $dirent;
		$dirss = explode('/',$dirstr);
		if(count($dirss)==0) 
		$dirss[0] = $dirstr;
		$dirss[0] = $dirss[count($dirss)-1];
		$dirstr = sanitizeForURL($dirstr);
		$fc = strtoupper(mbFirstChar($dirss[0]));
		if($pic==0 || $pindex[$pic-1]!=$fc) {
			$pindex[$pic] = $fc;
			$foo = $pindex[$pic];
			$pic++;
			$pprint[$i] = '<a name="p' . $foo . '">';
		} else
			$pprint[$i] = '';
		$pprint[$i] .= '[<a target="playlist" href="playlist.php?command=load&arg='
				. $dirstr . '">load</a>] ' . $dirss[0] . ' (<small><a href="main.php?sort=' . $sort . '&command=rm&arg=' . $dirstr . '">d</a>elete</small>)<br/>';
	}
	if(!isset($pprint)) $pprint = [];
	if(!isset($pindex)) $pindex = [];
	return [$pprint,$pindex];
}

function lsinfo2musicTable($lsinfo,$sort,$dir_url)
{
	global $settings, $sort_array,$colors;
	$color = $colors['music']['body'];
	$mic = 0;
	$mcount = count($lsinfo['music']);
	if($mcount) usort($lsinfo['music'],'msort');
	$add_all = '';
	for($i=0;$i<$mcount;$i++) {
		$dirent = $lsinfo['music'][$i]['file'];
		$dirstr = $dirent;
		$dirss = explode('/', $dirstr);
		if(count($dirss)==0) 
			$dirss[0] = $dirstr;
		$dirss[0] = $dirss[count($dirss)-1];
		if($i<$mcount-1) $add_all .= addslashes($dirstr) . $settings->song_seperator;
		else $add_all .= $dirstr;
		$dirstr = sanitizeForURL($dirstr);
		$col = $color[$i%2];
		if(!$settings->filenames_only && isset($lsinfo['music'][$i]['Title']) && $lsinfo['music'][$i]['Title']) {
			if(strcmp($sort_array[0],'Track')) {
				if(isset($lsinfo['music'][$i][$sort_array[0]]) && strlen($lsinfo['music'][$i][$sort_array[0]]) && ($mic==0 || $mindex[$mic-1]!=strtoupper(mbFirstChar($lsinfo['music'][$i][$sort_array[0]])))) {
					$mindex[$mic] = strtoupper(mbFirstChar($lsinfo['music'][$i][$sort_array[0]]));
					$foo = $mindex[$mic];
					$mic++;
					$mprint[$i] = '<a name="m' . $foo . '">';
				} else
					$mprint[$i] = '';
			} else {
				if(isset($foo)) unset($foo);
				if(isset($lsinfo['music'][$i][$sort_array[0]])) {
					$foo = strtok($lsinfo['music'][$i][$sort_array[0]],'/');
				}
				if(isset($foo) && ($mic==0 || 0!=strcmp($mindex[$mic-1],$foo))) {
					$mindex[$mic] = $foo;
					$mic++;
					$mprint[$i] = '<a name="m' . $foo . '">';
				} else
					$mprint[$i] = '';
			}
			$mprint[$i] = '<tr bgcolor="' . $col . '"><td width="0">' . $mprint[$i]
					. '[<a target="playlist" href="playlist.php?command=add&arg=' . $dirstr . '">add</a>]</td><td>';
			if(!isset($lsinfo['music'][$i]['Artist'])) {
				$mprint[$i].= $settings->unknown_string . '</td><td>';
			} else {
				$artist_url = sanitizeForURL($lsinfo['music'][$i]['Artist']);
				$mprint[$i] .= '<a href="find.php?find=artist&arg=' . $artist_url . '&sort=' . $sort . '&dir=' . $dir_url . '">'
						. $lsinfo['music'][$i]['Artist'] . '</a></td><td>';
			}
			$mprint[$i].= $lsinfo['music'][$i]['Title'] . '</td><td>';
			if(!isset($lsinfo['music'][$i]['Album'])) {
				$mprint[$i].= $settings->unknown_string . '</td><td>';
			} else {
				$album_url = sanitizeForURL($lsinfo['music'][$i]['Album']);
				$mprint[$i] .= '<a href="find.php?find=album&arg=' . $album_url . '&sort=' . $sort . '&dir=' . $dir_url . '">'
						. $lsinfo['music'][$i]['Album'] . '</a></td><td>';
			}
			if(!isset($lsinfo['music'][$i]['Track'])) {
				$mprint[$i].= $settings->unknown_string . '</td></tr>';
			} else {
				$mprint[$i].= $lsinfo['music'][$i]['Track'] . '</td></tr>';
			}
		} else {
			if($mic==0 || $mindex[$mic-1]!=strtoupper($dirss[0][0])) {
				$mindex[$mic] = strtoupper($dirss[0][0]);
				$foo = $mindex[$mic];
				$mic++;
				$mprint[$i] = '<a name="m' . $foo . '">';
			} else
				$mprint[$i] = '';
			$mprint[$i] = '<tr bgcolor="' . $col . '"><td>' . $mprint[$i]
					. '[<a target="playlist" href="playlist.php?command=add&arg=' . $dirstr . '">add</a>]</td><td colspan="4">' . $dirss[0] . '</td></tr>';
		}
	}
	if(!isset($mprint)) $mprint = [];
	if(!isset($mindex)) $mindex = [];
	return [$mprint,$mindex,$add_all];
}

function printIndex($index,$title,$anc)
{
	if(count($index)) {
		echo $title , ': [ ';
		for($i=0;$i<count($index);$i++) {
			$foo = $index[$i];
			echo '<a href="#' , $anc , $foo , '">' , $foo , '</a>';
		}
		echo ']<br/>';
	}
}

function printMusicTable($mprint,$url,$add_all,$mindex)
{
	global $settings, $colors,$sort_array;
	if(count($mprint)>0) {
		echo '<br/>';
		if($settings->use_javascript_add_all) {
			$add_all = sanitizeForPost($add_all);
			echo '<form style="padding:0;margin:0;" name="add_all" method="post" action="playlist.php" target="playlist">'
					, '<input type="hidden" name="add_all" value="' , $add_all , '" />'
					, '<table border="0" cellspacing="1" bgcolor="' , $colors['music']['title']
					, '" width="100%">'
					, '<tr><a name="music"><td colspan="4" nowrap><b>Music</b>'
					, '(<a href="javascript:document.add_all.submit()">'
					, 'add all</a>)';
			printIndex($mindex,'','m');
			echo '</td></tr>';
		} else {
			$add_all = sanitizeForUrl($add_all);
			echo '<table border="0" cellspacing="1" bgcolor="' , $colors['music']['title']
					, '" width="100%">'
					, '<tr><td colspan="4"><b>Music</b>'
					, '(<a target="playlist" href="playlist.php?add_all=' , $add_all , '">'
					, 'add all</a>)';
			printIndex($mindex,'','m');
			echo '</td></tr>';
		}
		echo '<tr><td>'
				, '<table border="0" cellspacing="1" bgcolor="' , $colors['music']['body'][1]
				, '" width="100%">';
		if(!$settings->filenames_only) {
			echo '<tr bgcolor="' , $colors['music']['sort'] , '"><td width="0"></td>';
			$cols[0] = 'Artist';
			$cols[1] = 'Title';
			$cols[2] = 'Album';
			$cols[3] = 'Track';
			for($i=0;$i<count($cols);$i++) {
				$new_sort = pickSort("$cols[$i]");
				if($cols[$i]==$sort_array[0])
					$cols[$i] = "<b>$cols[$i]</b>";
				echo '<td><a href="' , $url , '&sort=' , $new_sort , '">' , $cols[$i] , '</a></td>';
			}
			echo '</tr>';
		}
		for($i=0;$i<count($mprint);$i++) echo $mprint[$i];
		echo '</td></tr></table>'
				, '</table>';
		if($settings->use_javascript_add_all)
			echo '</form>';
	}
}

function printPlaylistTable($pprint,$pindex)
{
	global $colors;
	if(count($pprint)) {
		print "<br>\n";
		print "<table border=0 cellspacing=1 bgcolor=\"";
		print $colors["playlist"]["title"];
		print "\" width=\"100%\">\n";

		print "<tr><a name=playlists><td nowrap><b>Playlists</b>";
		printIndex($pindex,"","p");
		print "</td></tr>\n";
		print "<tr bgcolor=\"";
		print $colors["playlist"]["body"];
		print "\"><td>\n";
		for($i=0;$i<count($pprint);$i++) print $pprint[$i];
		print "</td></tr></table>\n";
	}
}

function songInfo2Display($song_info)
{
	global $settings;
	if(preg_match("/^[a-z]*:\/\//",$song_info["file"])) {
		$song = $song_info["file"];
	}
	else {
		$song_array = explode("/", $song_info["file"]);
		$song = $song_array[count($song_array)-1];
	}
	if(!$settings->filenames_only && isset($song_info["Title"]) && $song_info["Title"]) {
		if(isset($song_info["Artist"])) $artist = $song_info["Artist"];
		else $artist = "";
		if(isset($song_info["Title"])) $title = $song_info["Title"];
		else $title = "";
		if(isset($song_info["Album"])) $album = $song_info["Album"];
		else $album = "";
		if(isset($song_info["Track"])) $track = $song_info["Track"];
		else $track = "";
		$trans = array("artist" => $artist, "title" => $title, "album" => $album, "track" => $track);
		$song_display = strtr($settings->song_display_conf, $trans);
	}
	else if(!$settings->filenames_only && isset($song_info["Name"]) && $song_info["Name"]) {
		$song_display = $song_info["Name"];
	}
	else {
		$song_display = $song;
	}
	return $song_display;
}
