<?php
/**
* Plugin Name: Baat Afip
* Plugin URI: 
* Description: Baat Afip integrates WooCommerce with Afip 
* Version: 0.0.3
* Author: baat-webdesign.com
* Author URI: https://baat-webdesign.com
* WC tested up to: 4.1.0
* Copyright: 2007-2020 baat-webdesign.com.
* Text Domain: baat-afip
* Domain Path: /languages/
*
* @author baat-webdesign.com
* @package Baat Afip
* @version 0.3
*/

define( 'AFIP_BAAT_PATH', plugin_dir_path( __FILE__ ) );
define( 'AFIP_BAAT_DIR',  plugin_dir_url( __FILE__ ) );

  
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The core plugin class,
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-afip.php';

/**
 * Begins execution of the plugin.
 */
function run_wandelust_afip() {

	$plugin = new Baat_Afip();

}
run_wandelust_afip();
?>