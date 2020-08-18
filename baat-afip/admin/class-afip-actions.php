<?php
  if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
  }

  add_action( 'wp_ajax_baat_get_cuit', 'baat_get_cuit' );
	add_action( 'wp_ajax_nopriv_baat_get_cuit', 'baat_get_cuit' );
	add_action( 'wp_ajax_baat_save_csr', 'baat_save_csr' );
	add_action( 'wp_ajax_nopriv_baat_save_csr', 'baat_save_csr' );	

	add_action( 'wp_ajax_baat_check_cuit', 'baat_check_cuit' );
	add_action( 'wp_ajax_nopriv_baat_check_cuit', 'baat_check_cuit' );	

  //add_filter( 'woocommerce_admin_order_actions', 'woo_afip_order_actions',10,2 );		


	add_filter( 'manage_edit-shop_order_columns', 'cw_add_order_afip_column_header');
	add_action( 'manage_shop_order_posts_custom_column', 'cw_add_order_afip_column_content' );

   
	function cw_add_order_afip_column_header($columns){
			$new_columns = array();
			foreach ($columns as $column_name => $column_info) {
					$new_columns[$column_name] = $column_info;
					if ('order_total' === $column_name) {
							$new_columns['order_afip'] = 'AFIP Factura';
					}
			}
   
			return $new_columns;
	}


	function cw_add_order_afip_column_content( $column ) {
			global $post;
 
			if ( 'order_afip' === $column ) {
		    $order = wc_get_order( $post->ID );
				$afip_pdf = get_post_meta($post->ID, 'afip_pdf', true);	

				if (empty($afip_pdf)){ 
          //echo '<a id="imprimir_factura" data-order="'.$post->ID.'" class="button" href="'.$afip_pdf.'">GENERAR FACTURA</a>'; 
				} else {  
					echo '<a id="imprimir_factura" data-order="'.$post->ID.'" class="button" href="'.$afip_pdf.'" target="_blank">FACTURA</a>'; 
				}  
			}
	}


  function baat_check_cuit(){
    
    if (isset($_POST['cuit'])) {
			
			$params = array(
						"method" => array(
								 "consultar" => array(
												'CUIT' => $_POST['cuit'],
												'api_key' => $_POST['api_key'],
								 )
						)
			);			
      
      $afip_response = wp_remote_post( 'https://afip.dev/get_cuit.php', array(
        'method' => 'POST',
        'timeout' => 60,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $params,
        'cookies' => array()
        )
      );
  
      if($afip_response['response']['code'] == 200){
        $afip_response = json_decode($afip_response['body']);

        if(!empty($afip_response->result)){
           
        $resultado = array(
          'nombre' => $afip_response->result->nombre,
          'apellido' => $afip_response->result->apellido,
          'empresa' => $afip_response->result->empresa,
          'billing_address_1' => $afip_response->result->billing_address_1,
          'billing_city' => $afip_response->result->billing_city,
          'billing_state' => $afip_response->result->billing_state,
          'billing_state_text' => $afip_response->result->billing_state_text,
          'billing_postcode' => $afip_response->result->billing_postcode,

        );
        $resultado = json_encode($resultado);
        echo $resultado; 
        
				}      
      }  
 
		}
    die();
  }
 

  function woo_afip_order_actions($actions,$the_order){
		if ( ! $the_order->has_status( array( 'cancelled' ) ) && ( $the_order->has_status( array( 'processing' ) ) || $the_order->has_status( array( 'completed' ) ) ) ) { 
			$invoice_id = get_post_meta($the_order->id,'_invoice_id',true);
			$estado_afip = get_post_meta( $the_order->id, '_estado_afip', true );
			if($invoice_id==''){
				$actions['invoice'] = array(
					'url'       => '#',
					'name'      => __( 'Invoice', 'baat-afip' ),
					'action'    => "fy-invoice-button", 			
				);
			} else {
				
				switch($estado_afip){
					
					case 1:
						/* 1: awaiting invoice */
						$actions['awaiting_invoice'] = array(
								'url'       => '',
								'name'      => __( 'awaiting invoice', 'baat-afip' ),
								'action'    => "fy-awaiting-button",
						);
						
						break;
					
					case 2:
						/* 2: invoice ready! */
						$actions['view_invoice'] = array(
							'url'       => '#',
							'name'      => __( 'view invoice', 'baat-afip' ),
							'action'    => "fy-view-invoice-button",
						);
						
						break;
					
					default:
					    /* 3: error (should have a re-send button?) */
						$actions['invoice'] = array(
							'url'       => '#',
							'name'      => __( 'Invoice', 'baat-afip' ),
							'action'    => "fy-invoice-button", 
						);
						
						break;
				}
			}
		}
		return $actions;
	}

  function baat_get_cuit() {
		global $wp_session;
 
		if (isset($_POST['cuit'])) {
			
			$params = array(
						"method" => array(
								 "validar" => array(
												'CUIT' => $_POST['cuit'],
												'api_key' => $_POST['api_key'],
								 )
						)
			);			
      
      $afip_response = wp_remote_post( 'https://afip.dev/get_cuit.php', array(
        'method' => 'POST',
        'timeout' => 60,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $params,
        'cookies' => array()
        )
      );
 
      if($afip_response['response']['code'] == 200){
                
        $afip_response = json_decode($afip_response['body']);
        
				if(!empty($afip_response->csr)){
					echo $afip_response->csr;			
        } else if(!empty($afip_response->lives)){
          echo 'Ya existe';
          update_option('afip_status', 'ok');
				} else {
					echo 'Error en la obtención del csr';
				}     
        
      } else {
        echo 'Error en servidor';
      }
										 
 			die();
		}
	}

  function baat_save_csr() {
		if (isset($_POST['cert_afip_respo'])) {
			
			$wc_settings_tab_woo_afip_entorno = get_option('wc_settings_tab_woo_afip_entorno');
 
			$params = array(
						"method" => array(
								 "guardar_csr" => array(
												'cert_afip_respo' => $_POST['cert_afip_respo'],
												'api_key' => $_POST['api_key'],
									 			'CUIT' => $_POST['cuit'],
									 			'entorno' => $wc_settings_tab_woo_afip_entorno,
								 )
						)
			);			
									
      $afip_response = wp_remote_post( 'https://afip.dev/get_cuit.php', array(
        'method' => 'POST',
        'timeout' => 60,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $params,
        'cookies' => array()
        )
      );
      if($afip_response['response']['code'] == 200){
        $afip_response = json_decode($afip_response['body']);
				if(!empty($afip_response->result)){
					echo $afip_response->result;		
          update_option('afip_status', $afip_response->result);
				} else {
					echo 'Error al guardar el certificado';
				}        
      } else {
        echo 'Error en servidor';
      }

 			die();
		}			
	}

