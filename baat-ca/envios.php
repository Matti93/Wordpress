<?php
/*
  Plugin Name: WooCommerce Envíos por CorreoArgentino
  Plugin URI: https://baat.com.ar/
  Description: Cotiza el costo de envios por Correo Argentino
  Version: 0.0.0
  Author: Baat Wordpress Study
  Author URI: https://baat.com.ar
  WC tested up to: 3.9.1
  Copyright: 2007-2020 baat.com.ar.
*/


if ( ! defined( 'WPINC' ) ) {die;}
if ( ! defined( 'ABSPATH' ) ) {exit;}

$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

if ( in_array( 'woocommerce/woocommerce.php',  $active_plugins) ) {

	add_filter( 'woocommerce_shipping_methods', 'tipos_de_envio' );

	function tipos_de_envio( $methods ) {

		$methods['correoargentino_sucursal'] = 'WC_Correoargentino_Sucursal';
		$methods['correoargentino_domicilio'] = 'WC_Correoargentino_Domicilio';

	return $methods;
	}

	add_action( 'woocommerce_shipping_init', 'tipos_de_envio_init' );

	function tipos_de_envio_init(){

		require_once plugin_dir_path(__FILE__) . 'clases/class-correoargentino-sucursal.php';
		require_once plugin_dir_path(__FILE__) . 'clases/class-correoargentino-domicilio.php';

	}

}
