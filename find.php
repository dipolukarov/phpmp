<?php
/**
 * @author neo <dipolukarov@gmail.com>
 * @version $Id$
 */

global $has_password;
$has_password = 0;
if(isset($_COOKIE['phpMp_password'])) {
	$password = $_COOKIE['phpMp_password'];
	$has_password = 1;
}
include 'config.php';
include 'theme.php';
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=UTF-8');

echo '<html>' , "\n"
		, '<head>'
		, '<meta http-equiv="Expires" content="Thu, 01 Dec 1994 16:00:00 GMT" />'
		, '<meta http-equiv="Pragma" content="no-cache" />'
		, '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />'
		// php won't interpret inside of the style block
		, '<style type="text/css">'
		, "* {\n"
		, '  font-family: ' , $fonts['all'] , ';}'
		, '</style>'
		, '</head>'
		, '<body link="' , $colors['links']['link'] , '" vlink="' , $colors['links']['visual'] , '" '
		, 'alink="' , $colors['links']['active'] , '" bgcolor="' , $colors['background'] , '">';

include 'find_body.php';

echo '</body></html>';
