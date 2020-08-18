<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Baat_Afip' ) ){
  class Baat_Afip {

    public function __construct() {
      $this->plugin_name = 'Baat Afip';
      $this->version = '0.0.1';
      $this->load_dependencies();
      $this->load_dni();
      $this->load_verificador();
      $this->define_admin();
      //add_action('woocommerce_api_'.strtolower(get_class($this)), array(&$this, 'handle_callback'));
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' )  );
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' )  );
    }
    
    public function enqueue_styles() {
      wp_enqueue_style($this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/afip-admin.css', array(), $this->version, 'all' );
    }
    
    public function enqueue_scripts() {
		  wp_enqueue_script($this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/afip-admin.js', array( 'jquery' ), $this->version, false );
      
      wp_localize_script( $this->plugin_name, 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	  }

    private function load_dependencies() {
      //require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-afip-admin.php';
    }
    
    private function define_admin() {
      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/bulk.php';
      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-afip-admin.php';
      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-afip-actions.php';
      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-afip-factura.php';
    }
    
    private function load_dni() {
      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-dni.php';
    }    
    
    private function load_verificador() {
      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-verificador.php';
    }       
    
    public function handle_callback(){
      global $wpdb;
      ob_start();
      exit;
	  }
  }
}