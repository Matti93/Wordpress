<?php

  add_filter( 'bulk_actions-edit-shop_order', 'register_afip_bulk_actions' );
  add_filter( 'handle_bulk_actions-edit-shop_order', 'afip_action_handler', 10, 3 );
  add_action( 'admin_notices', 'afip_action_admin_notice' );

 

  /**
   * Adds a new item into the Bulk Actions dropdown.
   */
  function register_afip_bulk_actions( $bulk_actions ) {
    $bulk_actions['wc-generar-afip'] = __( 'Generar Factura', 'domain' );
    return $bulk_actions;
  }
  /**
   * Handles the bulk action.
   */
  function afip_action_handler( $redirect_to, $action, $post_ids ) {
    if ( ! in_array($action, array('wc-generar-afip'))) {
      return $redirect_to;
    }
    $afip_response = array();
    
     
    foreach ( $post_ids as $post_id ) {
      $afip_response[] = woo_afip_invoice($post_id);
    }
        
    $redirect_to = add_query_arg( array(
			'bulk_afip_success' => count( $afip_response ),
			'bulk_afip_etiquetas' => $afip_response,
			'bulk_afip_error' => '',
    ), $redirect_to );
      
    return $redirect_to;
  }
  /**
   * Shows a notice in the admin once the bulk action is completed.
   */
  function afip_action_admin_notice() {
    if ( ! empty( $_REQUEST['bulk_afip_success'] ) ) {
      $drafts_count = intval( $_REQUEST['bulk_afip_success'] );
      printf('<div id="message" class="updated fade">'.$drafts_count.' factura/s generadas!</div>');
    }  
    
 
    if ( ! empty( $_REQUEST['bulk_afip_error'] ) ) {
      $error = $_REQUEST['bulk_afip_error']; 
      printf('<div id="message" class="updated fade">'.$error.'</div>');
    }
    
    if ( ! empty( $_REQUEST['bulk_afip_etiquetas'] ) ) {
      $etis = $_REQUEST['bulk_afip_etiquetas']; 
      $list_ids = implode(', ',$etis);
      $list_ids = str_replace(' ', '', $list_ids);
           
    }    

  }

  function afip_bulk_save(){
    $envioid = $_POST['envioid']; //
    $dataid = $_POST['dataid'];
    update_post_meta($dataid, '_bulk_afip', $envioid);
    echo 'OK';
    die();
  }

?>