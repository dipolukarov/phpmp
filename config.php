<?php
/**
 * 
 */

$settings = json_decode( file_get_contents('config.json') );

// OPTIONAL
global $song_display_conf;
$song_display_conf = "(artist) title";
global $unknown_string;
$unknown_string = "";
global $filenames_only;
$filenames_only = "no";
global $use_javascript_add_all;
$use_javascript_add_all = "yes";

// SHOULDN'T NEED TO TOUCH THIS
global $song_seperator;
$song_seperator = "rqqqrqqqr";

//include colors
include "theme.php";
