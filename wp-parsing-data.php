<?php
/*
Plugin Name: wp-parsing-data
Plugin URI: http://voodoopress.net
Description: Some description.
Version: 1.1
Author: Evgen "EvgenDob" Dobrzhanskiy
Author URI: http://voodoopress.net
Stable tag: 1.1
*/

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');

include('modules/functions.php');
#include('modules/shortcodes.php');
include('modules/settings.php');
include('modules/meta_box.php');
#include('modules/widgets.php');
include('modules/hooks.php');
#include('modules/cpt.php');
include('modules/scripts.php');
include('modules/ajax.php');
 
?>