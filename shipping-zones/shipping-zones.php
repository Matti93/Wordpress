<?php
/**
 * Plugin Name: Shipping Zones
 * Description: Coloquele precio a los envios en su ciudad, sin interferencia de una plataforma externa
 * Version: 1.1.29
 * Author: Matias Blanco 
 * Author URI: https://baat.com.ar
 * License: GPLv2
 * Text Domain: shipping-zone
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action('plugins_loaded','states_places_argentina_init',1);

function states_places_argentina_smp_notices($classes, $notice){
    ?>
    <div class="<?php echo $classes; ?>">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function states_places_argentina_init(){
    load_plugin_textdomain('shipping-zone-wcm',
        FALSE, dirname(plugin_basename(__FILE__)) . '/languages');

    /**
     * Check if WooCommerce is active
     */
    if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

        require_once ('includes/states-places.php');
        /**
         * Instantiate class
         */
        $GLOBALS['wc_states_places'] = new WC_States_Places_Argentina(__FILE__);


        require_once ('includes/filter-by-cities.php');

        add_filter( 'woocommerce_shipping_methods', 'add_filters_by_cities_method' );

        function add_filters_by_cities_method( $methods ) {
            $methods['filters_by_cities_shipping_method'] = 'Filters_By_Cities_Method';
            return $methods;
        }

        add_action( 'woocommerce_shipping_init', 'filters_by_cities_method' );

        global $pagenow;

    }
}


add_filter( 'woocommerce_default_address_fields', 'states_places_argentina_smp_woocommerce_default_address_fields', 1000, 1 );

function states_places_argentina_smp_woocommerce_default_address_fields( $fields ) {
    if ($fields['city']['priority'] < $fields['state']['priority']){
        $state_priority = $fields['state']['priority'];
        $fields['state']['priority'] = $fields['city']['priority'];
        $fields['city']['priority'] = $state_priority;

    }
    return $fields;
}

add_filter( 'woocommerce_states', 'custom_woocommerce_states' );

function custom_woocommerce_states( $states ) {

	$states['AR'] = array (
		'C' => __( 'Ciudad Aut&oacute;noma de Buenos Aires', 'woocommerce' ),
		'B' => __( 'Buenos Aires', 'woocommerce' ),
		'K' => __( 'Catamarca', 'woocommerce' ),
		'GBA' => __( 'Gran Buenos Aires (GBA)', 'woocommerce' ),
		'H' => __( 'Chaco', 'woocommerce' ),
		'U' => __( 'Chubut', 'woocommerce' ),
		'X' => __( 'C&oacute;rdoba', 'woocommerce' ),
		'W' => __( 'Corrientes', 'woocommerce' ),
		'E' => __( 'Entre R&iacute;os', 'woocommerce' ),
		'P' => __( 'Formosa', 'woocommerce' ),
		'Y' => __( 'Jujuy', 'woocommerce' ),
		'L' => __( 'La Pampa', 'woocommerce' ),
		'F' => __( 'La Rioja', 'woocommerce' ),
		'M' => __( 'Mendoza', 'woocommerce' ),
		'N' => __( 'Misiones', 'woocommerce' ),
		'Q' => __( 'Neuqu&eacute;n', 'woocommerce' ),
		'R' => __( 'R&iacute;o Negro', 'woocommerce' ),
		'A' => __( 'Salta', 'woocommerce' ),
		'J' => __( 'San Juan', 'woocommerce' ),
		'D' => __( 'San Luis', 'woocommerce' ),
		'Z' => __( 'Santa Cruz', 'woocommerce' ),
		'S' => __( 'Santa Fe', 'woocommerce' ),
		'G' => __( 'Santiago del Estero', 'woocommerce' ),
		'V' => __( 'Tierra del Fuego', 'woocommerce' ),
		'T' => __( 'Tucum&aacute;n', 'woocommerce' ),
		);

  return $states;
}