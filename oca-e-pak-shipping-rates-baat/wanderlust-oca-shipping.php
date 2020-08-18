<?php
/*
	Plugin Name: WooCommerce Oca Shipping
	Plugin URI: https://wanderlust-webdesign.com/
	Description: Obtain shipping rates dynamically via the OCA API for your orders.
	Version: 1.1.1
	Author: Wanderlust Web Design
	Author URI: https://wanderlust-webdesign.com
	WC tested up to: 4.3.0
	Copyright: 2007-2020 wanderlust-webdesign.com.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


/**
 * Plugin global API URL
*/
global $wp_session;

$wp_session['url_oca'] = 'https://oca.wanderlust-webdesign.com/api-server/api.php';

require_once( 'includes/functions.php' );

/**
 * Plugin page links
*/
function wc_oca_plugin_links( $links ) {

	$plugin_links = array(
		'<a href="https://wanderlust-webdesign.com/contact" target="_blank">' . __( 'Soporte', 'woocommerce-shipping-oca' ) . '</a>',
		'<a href="https://shop.wanderlust-webdesign.com/20-de-descuento-a-clientes-de-wanderlust-que-integren-con-oca/" target="_blank">' . __( 'Abrir cuenta con OCA', 'wc-gateway-decidir' ) . '</a>',
		'<a href="https://shop.wanderlust-webdesign.com/shop/woocommerce-oca-premium-shipping/" target="_blank">' . __( 'Plugin PREMIUM', 'wc-gateway-decidir' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'wc_oca_plugin_links' );

/**
 * WooCommerce is active
*/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	/**
	 * woocommerce_init_shipping_table_rate function.
	 *
	 * @access public
	 * @return void
	 */
	function wc_oca_init() {
		include_once( 'includes/class-wc-shipping-oca.php' );
	}
  add_action( 'woocommerce_shipping_init', 'wc_oca_init' ); 

	/**
	 * wc_oca_add_method function.
	 *
	 * @access public
	 * @param mixed $methods
	 * @return void
	 */
	function wc_oca_add_method( $methods ) {
		$methods[ 'oca_wanderlust' ] = 'WC_Shipping_Oca';
		return $methods;
	}

	add_filter( 'woocommerce_shipping_methods', 'wc_oca_add_method' );

	/**
	 * wc_oca_scripts function.
	 */
	function wc_oca_scripts() {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	add_action( 'admin_enqueue_scripts', 'wc_oca_scripts' );

	$oca_settings = get_option( 'woocommerce_oca_settings', array() );
	
}