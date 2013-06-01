<?php
/**
 * @author neo <dipolukarov@gmail.com>
 * @version $Id$
 */

include 'config.php';
include 'theme.php';

if($settings->use_cookies && isset($_COOKIE['phpMp_playlist_hide'])) {
	$hide = $_COOKIE['phpMp_playlist_hide'];
}

if(isset($_COOKIE['phpMp_password'])) {
	$password = $_COOKIE['phpMp_password'];
}

extract($_GET);
extract($_POST);

if(!isset($hide)) $hide = 1;
else if($settings->use_cookies) {
	setcookie('phpMp_playlist_hide', $hide);
}

header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=UTF-8');

echo '<!DOCTYPE html>' , "\n"
		, '<html>' , "\n"
		, '<head>'
		, '<meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />'
		, '<meta http-equiv="Pragma" content="no-cache" />'
		, '<meta http-equiv="REFRESH" content="' , $settings->refresh_freq , ';URL=playlist.php" />'
		, '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'
		// php won't interpret inside of the style block
		, '<style type="text/css">' , "\n"
		, '* {' , "\n"
		, '  font-family: ' , $fonts["all"] , ";\n"
		, "}\n"
		, '</style>'
		, '</head>' , "\n"
		, '<body link="' , $colors['links']['link'] , '" vlink="' , $colors['links']['visual']
		, '" alink="' , $colors['links']['active'] , '" bgcolor="' , $colors['background'] , '">';

include 'playlist_body.php';

echo '</body></html>';
