<?php
    /*
    Plugin Name: Get Free Shipment
    Description: Get free shipment when you have a exact or more 
    Author:<a href="https://www.facebook.com/mattuqui.blanco" target="_blank">Matias Blanco</a>
    */

    require_once plugin_dir_path(__FILE__) . 'includes/gfs-functions.php';
    function gfs_crear_tablas_bd() {
        global $wpdb;

        // Require parent plugin
    if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) and current_user_can( 'activate_plugins' ) ) {
        // Stop activation redirect and show error
        wp_die('Sorry, but this plugin requires Woocomerce to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    }
        // Definimos el nombre de la tabla con el prefijo usado en la instalación:
        $gfs_data = $wpdb->prefix . 'gfs_data';
        $charset_collate = $wpdb->get_charset_collate();
        // Diseñamos la consulta SQL para la nueva tabla:
        $result = $wpdb->query("SELECT EXISTS (SELECT 1 FROM $gfs_data);
        ");
        if($result == 0) {
            $sql = "CREATE TABLE IF NOT EXISTS $gfs_data (
                id int(9) NOT NULL AUTO_INCREMENT,
                texto varchar(65) NOT NULL default '',
                UNIQUE KEY id(id)
                ) $charset_collate;";
           $sqlInsert = "INSERT INTO $gfs_data (texto) VALUES ('')";
           require_once( ABSPATH . 'wp-admin/includes/upgrade.php');
          
           dbDelta($sql);
           dbDelta($sqlInsert);
        }
       }
       register_activation_hook(__FILE__, 'gfs_crear_tablas_bd');

       function gfs_remove_database() {
           global $wpdb;
           $table_name = $wpdb->prefix . 'gfs_data';
           $sql = "DROP TABLE IF EXISTS $table_name";
           dbDelta($sql);
        //    delete_option("my_plugin_db_version");
        register_deactivation_hook( __FILE__, 'gfs_remove_database' );
}   
