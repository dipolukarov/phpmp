<?php
/**
 * @author neo <dipolukarov@gmail.com>
 * @version $Id$
 */

include 'config.php';
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" http://www.w3.org/TR/html4/frameset.dtd">' , "\n"
		, '<html>' , "\n"
		, '<head><title>' , $settings->title , '</title></head>';

if ($settings->frames) {
	echo '<frameset border="3" ' , $settings->frames_layout , '>'
			, '<frame name="main" src="main.php">'
			, '<frame name="playlist" src="playlist.php">'
			, '<noframes>NO FRAMES :-(</noframes>'
			, '</frameset>';
}
else {
	echo '<body bgcolor="' , $colors['background'] , '">'
			, '<table border="0" cellspacing="0" width="100%">'
			, '<tr valign="top"><td>';
	include 'main_body.php';
	
	echo '</td><td width="250">';
	include 'playlist_body.php';
	
	echo '</td></tr>'
			, '</table>'
			, '</body>';
}

echo '</html>';
