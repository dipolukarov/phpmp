<?php
/**
 * 
 */
global $settings;
$settings = json_decode( file_get_contents('config.json') );

//include colors
include "theme.php";
