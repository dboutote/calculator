<?php 
/*
Plugin Name: Suretint Pricing Calculator
Version: 1.0.4
Plugin URI:
Description: A Lightweight Pricing Calculator
Author: Darrin Boutote
Author URI: http://darrinb.com
*/



/**
 * Define some constants
 */
define('CALC_URL', plugin_dir_url(__FILE__));
define('CALC_DIR', plugin_dir_path(__FILE__));
define('CALC_CPT_DIR', CALC_DIR . 'cpt' );
define('CALC_TAX_DIR', CALC_DIR . 'tax' );
define('CALC_CSS_URL', CALC_URL . 'css');
define('CALC_IMG_URL', CALC_URL . 'img');
define('CALC_JS_URL', CALC_URL . 'js');
define('CALC_LIB_DIR', CALC_DIR . 'lib' );
define('CALC_BASE_URL', admin_url('admin.php').'?page='.basename(__FILE__, '.php'));
define('CALC_TPL_DIR', CALC_DIR . 'tpl' );
define('CALC_BASE_NAME', basename(__FILE__, '.php'));
define('CALC_PLUGIN_HOOK', plugin_basename(__FILE__));


// load classes
require dirname( __FILE__ ) . '/calc_class_utils.php';
require dirname( __FILE__ ) . '/calc_class_backend.php';
require dirname( __FILE__ ) . '/calc_class.php';

/**
 * Instantiate the Join List Class
 */
$new_calc = new TintCalc();


if( is_admin() ){
	$CALC_Backend = new CALC_Backend();
}


// Utility function for debugging output
function debug($var){
	echo "\n<pre style=\"text-align:left;\">";
	if( is_array($var) || is_object($var)){
		print_r($var);
	} else {
		var_dump($var);
	}
	echo "</pre>\n";
}