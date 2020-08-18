<?php
  if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
  }

	add_action( 'add_meta_boxes', 'woo_afip_add_metaboxes' );	
  add_action( 'wp_ajax_woo_afip_do_ajax_request', 'woo_afip_invoice' );		
  add_action( 'wp_ajax_woo_afip_do_ajax_pdf_request', 'woo_afip_pdf' );		
 
  add_action( 'wp_ajax_woo_afip_view_ajax_request', 'woo_afip_view_invoice' );

  function woo_afip_add_metaboxes(){
		add_meta_box( 'afip_metabox', __('Factura AFIP','baat-afip'), 'woo_afip_order_buttons', 'shop_order', 'side', 'core' );
	}

  function woo_afip_order_buttons(){
		global $post;
		$the_order = wc_get_order($post->ID);
		if ( ! $the_order->has_status( array( 'cancelled' ) ) && ( $the_order->has_status( array( 'processing' ) ) || $the_order->has_status( array( 'completed' ) ) ) ){ 
			$invoice_id = get_post_meta($post->ID,'afip_cae',true);
			$estado_afip = get_post_meta( $post->ID, 'afip_response', true );
			$pdf_afip = get_post_meta( $post->ID, 'afip_pdf', true );
			?>
			<p>
			<?php if($invoice_id==''){ ?>
        <a class="button invoice-button"  title="Generar Comprobante">Crear Factura AFIP</a>
      <?php } else if(empty($pdf_afip) && $invoice_id!='') { ?>
        <a class="button invoicepdf-button"  title="Imprimir Comprobante">Generar PDF</a>
     <?php  } else {  ?>
       <a class="button view-button" href="<?php echo $pdf_afip; ?>" target="_blank"  title="Imprimir Comprobante">Imprimir Factura AFIP</a>
      <?php }  ?>
			</p>
      <div id="afip_notices"></div>
			<?php
		}
	}

  function normalize($string) {
      $table = array(
          'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj',  'Ž'=>'Z', 'ž'=>'z', 'C'=>'C', 'c'=>'c', 'C'=>'C', 'c'=>'c',
          'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'É'=>'E',
          'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
          'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
          'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'é'=>'e',
          'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
          'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
          'ÿ'=>'y',
      );

      return strtr($string, $table);
  }


  function woo_afip_invoice($order_id){
		global $states;		
    
    if(empty($order_id)){
		  $order_id = intval($_POST["order_id"]);      
    }

		$order = wc_get_order( $order_id );
		$shipping_data = $order->get_items( 'shipping' );
		$shipping_methods = array();
		$total = 0;
		$precio_sin_iva = 0;
	
		if(is_array($shipping_data)):
			foreach($shipping_data as $k=>$sm){
        if($sm['total'] > 1){
          $precio_sin_iva = $sm['item_meta']['cost'][0]/1.21;
          $item_name = normalize($sm['method_title']);
          array_push($shipping_methods,
            array(
              'codigo' => strtoupper($sm['instance_id']),
              'descripcion' => strtoupper($item_name),
              'cantidad' => 1,			
              'UnidadMedida' 	=> 'Unidad',
              'precioUnitario' => $sm['total'],        
              'porcBonif' 	=> 0.00,
              'impBonif' 	=>  0.00,
              'Alic' =>  0.00,
              'importeItem' => $sm['total'],  
            )
          );
        }
        $total+=$sm['total'];         
			}

		endif;
		
		$order_meta = get_post_meta($order_id);
		$items = $order->get_items();
		$billing_currency = $order_meta["_order_currency"][0];
		$billing_company = strtoupper($order->get_billing_company());
		$billing_first_name = strtoupper($order->get_billing_first_name());
		$billing_last_name = strtoupper($order->get_billing_last_name());
		$billing_email = strtoupper($order->get_billing_email());
		$billing_postcode = $order->get_billing_postcode();
		$payment_method = $order_meta["_payment_method"][0];
		$billing_address_1 = strtoupper($order->get_billing_address_1());
		$billing_address_2 = strtoupper($order->get_billing_address_2());
		$billing_city = strtoupper($order->get_billing_city());
		$billing_phone = $order->get_billing_phone();
		$client_state = $order->get_billing_state();
		$customer_user = $order_meta["_customer_user"][0];
    if ($client_state == 'C') {$client_state = 'Ciudad Autonoma de Buenos Aires';}
    if ($client_state == 'B') {	$client_state = 'Buenos Aires';	}
    if ($client_state == 'K') {	$client_state = 'Catamarca';	}
    if ($client_state == 'H') {	$client_state = 'Chaco';	}
    if ($client_state == 'U') {	$client_state = 'Chubut';	}
    if ($client_state == 'X') {	$client_state = 'Cordoba';	}
    if ($client_state == 'W') {	$client_state = 'Corrientes';	}
    if ($client_state == 'E') {	$client_state = 'Entre Rios';	}
    if ($client_state == 'P') {	$client_state = 'Formosa';	}
    if ($client_state == 'Y') {	$client_state = 'Jujuy';	}
    if ($client_state == 'L') {	$client_state = 'La Pampa';	}
    if ($client_state == 'F') {	$client_state = 'La Rioja';	}
    if ($client_state == 'M') {	$client_state = 'Mendoza';	}
    if ($client_state == 'N') {	$client_state = 'Misiones';	}
    if ($client_state == 'Q') {	$client_state = 'Neuquen';	}
    if ($client_state == 'R') {	$client_state = 'Rio Negro';	}
    if ($client_state == 'A') {	$client_state = 'Salta';	}
    if ($client_state == 'J') {	$client_state = 'San Juan';	}
    if ($client_state == 'D') {	$client_state = 'San Luis';	}
    if ($client_state == 'Z') {	$client_state = 'Santa Cruz';	}
    if ($client_state == 'S') {	$client_state = 'Santa Fe';	}
    if ($client_state == 'G') {	$client_state = 'Santiago del Estero';	}
    if ($client_state == 'V') {	$client_state = 'Tierra del Fuego';	}
    if ($client_state == 'T') {	$client_state = 'Tucuman';	}		
    
		$time = new DateTime;
		$today_atom = $time->format(DateTime::ATOM);

		$bienes = array();

		foreach( $items as $k=>$item ){
			$product_id = $item['product_id'];
			if($item['variation_id'] > 0){
				$product_id = $item['variation_id'];
			}
			
			$product = wc_get_product($product_id);
			$sku = $product->get_sku();
			if($sku == ''){
				$sku = $product_id;
			}

			$precio_sin_iva = $item['line_total'] / 1.21;
			$precio_sin_iva = round($precio_sin_iva);
 			$item_name = normalize($item['name']);
      array_push($bienes,array(
        'codigo' => strtoupper($sku),
				'descripcion' => strtoupper($item_name),
				'cantidad' => $item['qty'],			
        'UnidadMedida' 	=> 'Unidad',
				'precioUnitario' => $item['line_total'] / $item['qty'],        
        'porcBonif' 	=> 0.00,
        'impBonif' 	=>  0.00,
        'Alic' =>  0.00,
        'importeItem' => $item['line_total'],       
			));
			
			$total+=  $item['line_total'];
		}
		
		if(is_array($shipping_methods) && count($shipping_methods)>0 ){
			foreach($shipping_methods as $k=>$v){
				array_push($bienes,$v);
			}
		}

    $fee =  $order->get_fees();
    foreach( $fee as $k=>$fees ){ 
      $item_name = normalize($fees['name']);
      array_push($bienes,array(
        'codigo' => strtoupper($sku),
        'descripcion' => strtoupper($item_name),
        'cantidad' => 1,      
        'UnidadMedida'  => 'Unidad',
        'precioUnitario' => $fees['total'] ,        
        'porcBonif'   => 0.00,
        'impBonif'  =>  0.00,
        'Alic' =>  0.00,
        'importeItem' => $fees['total'],       
      ));
      
      $total+=  $fees['total'];
    }    
    
    $dni = get_post_meta($order_id,'DNI',true);
    $dni_valido = strlen($dni); // 13
    if($dni_valido == 8){
      $tipo_doc = 96;
    } else if($dni_valido >= 9){
      $tipo_doc = 80;
    } else {
      $tipo_doc = 99; //CUIT
      $dni = 0;
    }
    
      
    $empresa = normalize($billing_company);
    $direccion = normalize($billing_address_1.', '.$billing_address_2 . ', ' .$billing_city .', '. $client_state);
    $nombreyapellido = normalize($billing_first_name . ' ' . $billing_last_name);
    
		$cliente = array(
					"CodigoPostal" => $billing_postcode,
					"CondicionPago" => 1,
					"DireccionFiscal" =>  $direccion,
					"MailFacturacion" => $billing_email, 
					"NroDocumento" => $dni,
					"PercibeIVA" => false, ////True: Si la empresa emisora es Agente de Retención de ARBA
					"PercibeIIBB" => false, ////Si la empresa emisora es Agente de Retención de IVA
					"RazonSocial" => $empresa,
					"TipoDocumento" => $tipo_doc, //DNI
					"MailContacto" => $billing_email,
					"Contacto" => $nombreyapellido,
					"Telefono" => $billing_phone,
          "Total" => $total
		);
		
		$vendedor = array(
					"afip_entorno" => get_option('wc_settings_tab_woo_afip_entorno'),
          "afip_punto_venta" => get_option('wc_settings_tab_woo_afip_punto_venta'),
					"afip_api" => get_option('wc_settings_tab_woo_afip_api'),
					"afip_cuit" =>  get_option('wc_settings_tab_woo_afip_cuit'),
					"afip_bienes" => get_option('wc_settings_tab_woo_afip_bienes'), 
					"afip_condicionventa" => get_option('wc_settings_tab_woo_afip_condicionventa'),
					"afip_condicion" => strtoupper(get_option('wc_settings_tab_woo_afip_condicion')),  
					"afip_ingresosbrutos" => strtoupper(get_option('wc_settings_tab_woo_afip_ingresosbrutos')), 
					"afip_facttipo" => get_option('wc_settings_tab_woo_afip_facttipo'),
          "afip_razonsocial" => get_option('wc_settings_tab_woo_afip_razonsocial'),
          "afip_inicio" => get_option('wc_settings_tab_woo_afip_inicio'),
          "afip_alicuotaiva" => get_option('wc_settings_tab_woo_afip_alicuota'),
		);
	 			
		$params = array(
						"method" => array(
								 "generar_comprobante" => array(
												'vendedor' => $vendedor,
												'cliente' => $cliente,
									 			'bienes' => $bienes,
								 )
						)
		);			
    
 							
		$afip_response = wp_remote_post( 'https://afip.dev/generar_comprobante.php', array(
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
      
      if(!empty($afip_response->result->cae_full->cae)){
        update_post_meta($order_id, 'afip_cae', json_encode($afip_response->result->cae_full->cae)); 
        update_post_meta($order_id, 'afip_response', json_encode($afip_response->result)); 
        
        if($_POST["order_id"]){
          echo 'CAE GENERADO: '. $afip_response->result->cae_full->cae .' </br> GENERANDO PDF..';
          exit;          
        } else {
          woo_afip_pdf($order_id);
        }

        
      } else {
        if($_POST["order_id"]){
          echo 'ERROR: <pre>' ;print_r($afip_response->result);echo' ..</pre>'; 
        }
        update_post_meta($order_id, 'afip_response', json_encode($afip_response->result)); 
      }
      
         
		} else {
      if($_POST["order_id"]){
		    echo 'Error en servidor';
      }
		}		
    
    if($_POST["order_id"]){
      die();
      exit;
    }
	}

  function woo_afip_pdf($order_id){
    
    if(empty($order_id)){
      $order_id = $_POST['order_id'];
    }
    
    $afip = get_post_meta($order_id, 'afip_response', true );  
    $afip = json_decode($afip);
    $params = null;
    $params = array(
      "method" => array(
        "generar_pdf" => array(
          'afip_data' => $afip,
        )
      )
    );			

    $afip_response = null;
    $afip_response = wp_remote_post( 'https://afip.dev/generar_pdf.php', array(
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
        
        $etiqueta = base64_decode($afip_response->pdf);
        if(!empty($etiqueta)){
          $save_path = AFIP_BAAT_PATH . 'facturas/';
          $save_url = AFIP_BAAT_DIR . 'facturas/';
          $fp = fopen($save_path . $afip->cae_full->cae . '.pdf', 'wb'); //Create PNG or PDF file
          fwrite($fp, $etiqueta); 
          fclose($fp);		
          $url_factura = $save_url.$afip->cae_full->cae .'.pdf';
          update_post_meta($order_id, 'afip_pdf', $url_factura);      
          
          if($_POST['order_id']){
            
             echo  '<div  style="position: relative; width: 100%; height: 60px;" >
            <a style=" width: 225px; text-align: center;background: #643494;color: white;padding: 10px;margin: 10px;float: left;text-decoration: none;" href="'. $url_factura .'" target="_blank">
            IMPRIMIR FACTURA</a></div>';           
            
          }
           


          $enviar = get_option('wc_settings_tab_woo_afip_enviar_factura');
          $enviar_texto = get_option('wc_settings_tab_woo_afip_factura_texto');
          $order = wc_get_order($order_id);
          if($enviar == 'yes'){
            $texto = $enviar_texto .' <a href="'.$url_factura.'" target="_blank">DESCARGAR</a>';
            $order->add_order_note( $texto, true );
          }     
          if($_POST['order_id']){
            exit();
          }
        }

    } else {
      if($_POST['order_id']){
        echo 'Error en PDF';
        update_post_meta($order_id, 'afip_pdf', 'Error en PDF');      
      }
    }
}
	
	function woo_afip_view_invoice(){
		$order_id = intval($_POST["order"]);
		$invoice_id = get_post_meta($order_id,'_invoice_id',true);
		$client = new SoapClient($this->soap_url);
		
		//Autentication
		$auth = array(	
					"Empresa" => get_option( 'wc_settings_tab_woo_afip_organization_ID' ),
					"Hash" => get_option( 'wc_settings_tab_woo_afip_password' ),
					"Usuario" => get_option( 'wc_settings_tab_woo_afip_user' )
		);
		
		//Parametros	
		$param = array(
						"Autenticacion" => $auth,
						"IdComprobante" => $invoice_id
					);				
		
		//Request
		$request = array("request" => $param);
		
		try {
			  $response = $client->DetalleComprobante($request);
			  echo json_encode($response);
			  exit;
		} catch (Exception $e)  {
			  echo json_encode($e->getMessage());
		}
	}