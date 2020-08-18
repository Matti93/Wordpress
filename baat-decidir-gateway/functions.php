<?php

  add_action('wp_ajax_get_tarjetas', 'get_tarjetas', 1);
	add_action('wp_ajax_nopriv_get_tarjetas', 'get_tarjetas', 1);

	add_action('wp_ajax_get_cuotas', 'get_cuotas', 1);
	add_action('wp_ajax_nopriv_get_cuotas', 'get_cuotas', 1);
 

	add_action('wp_ajax_save_decidir_data', 'save_decidir_data', 1);
	add_action('wp_ajax_nopriv_save_decidir_data', 'save_decidir_data', 1);

  function save_decidir_data(){
    $data_saved = json_encode($_POST);
    WC()->session->set( 'decidir_saved_data', $data_saved );
    
  }

        



  add_action( 'woocommerce_cart_calculate_fees', 'bbloomer_add_checkout_fee_for_gateway' );

  function bbloomer_add_checkout_fee_for_gateway() {
    global $woocommerce;
      $decidir_saved_data = WC()->session->get( 'decidir_saved_data' );
      $decidir_saved_data = json_decode($decidir_saved_data, true);
      $fee = $decidir_saved_data['decidir_recargo'];
      $get_subtotal =  floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_subtotal() ) );
      $get_shipping_total =  floatval( preg_replace( '#[^\d.]#', '', $woocommerce->cart->get_shipping_total() ) );
      if($fee > 1){
        $fee = $fee /100; 
        $totalfee = ($get_subtotal + $get_shipping_total) * $fee;
        if ( $fee ) {
          WC()->cart->add_fee( 'Recargo Tarjeta', $totalfee );
        }        
      }

  }

 


	function get_tarjetas() {
		global $wp_session;
		  add_action( 'woocommerce_before_calculate_totals', 'custom_cart_total' );

		if (isset($_POST['decidir_banco'])) {
			
			  $listado = get_option('cuotas', true);
 				echo '<select id="pv_tarjetas" name="pv_tarjetas">';
        $tarjetas = array();						
				foreach($listado[$_POST['decidir_banco']] as $result){
          $nombretarjeta = nombre_tarjeta($result['tipo']);      
          if(in_array($nombretarjeta, $tarjetas)){

          } else {
            $tarjetas[] = $nombretarjeta;
            echo '<option value="'. $result['tipo'].'">'. $nombretarjeta. '</option>';
          }
 				}
				echo '</select>';		 
 			die();
		}
	}


  function get_cuotas(){
    global $woocommerce;
    
     $amount = $woocommerce->cart->get_totals();  
    
    if (isset($_POST['decidir_banco']) && isset($_POST['decidir_tarjeta'])) {
       $listado = get_option('cuotas', true);
       echo '<select id="pv_cuotas" name="pv_cuotas">';

       foreach($listado[$_POST['decidir_banco']] as $result){
         if($result['tipo'] == $_POST['decidir_tarjeta']){           
            if($result['cuotas'] == 13){
              $cuota = 'Ahora 3';
              $cuota_a = 3;
              $cuotas = 13;
            } else if($result['cuotas'] == 16){
              $cuota = 'Ahora 6';
              $cuota_a = 6;
              $cuotas = 16;
            } else if($result['cuotas'] == 17){
              $cuota = 'Ahora 12';
              $cuota_a = 12;
              $cuotas = 7 ;
            } else if($result['cuotas'] == 18){
              $cuota = 'Ahora 18';
              $cuota_a = 18;
              $cuotas = 8;
            } else {
              $cuota_a = $result['cuotas'];
              $cuotas = $result['cuotas'];
			  $cuota = '';
            }
            if($result['recargo'] > 0){
              $recargo = ($amount['total'] * $result['recargo'] ) / 100;

              $total_nuevo = ($amount['total'] + $recargo)  / $cuota_a;
            } else {
              $total_nuevo = $amount['total']  / $cuota_a;

            }
            $total_nuevo = money_format('%i', $total_nuevo) ;
            
            if($cuota){
              echo '<option value="'. $cuotas .'" data-recargo="'.$result['recargo'].'">'. $cuota_a . ' de $'. $total_nuevo .' - ('.$cuota.')</option>';
            } else {
              echo '<option value="'. $cuotas .'" data-recargo="'.$result['recargo'].'">'. $cuota_a . ' de $'. $total_nuevo .'</option>';
             
            }
         }
       }
       echo '</select>';		 
    }       
      
    die();
  }

  function nombre_tarjeta($tarjeta_id){
    
    if($tarjeta_id == 1){$nombre = 'Visa';}
    if($tarjeta_id == 31){$nombre = 'Visa Débito';}
    if($tarjeta_id == 8){$nombre = 'Diners Club';}
    if($tarjeta_id == 23){$nombre = 'Tarjeta Shopping';}
    if($tarjeta_id == 24){$nombre = 'Tarjeta Naranja';}
    if($tarjeta_id == 39){$nombre = 'Tarjeta Nevada';}
    if($tarjeta_id == 42){$nombre = 'Nativa';}
    if($tarjeta_id == 55){$nombre = 'Patagonia 365';}
    if($tarjeta_id == 63){$nombre = 'Cabal';}
    if($tarjeta_id == 108){$nombre = 'Cabal Débito';}
    if($tarjeta_id == 65){$nombre = 'American Express';}
    if($tarjeta_id == 104){$nombre = 'MasterCard';}
    if($tarjeta_id == 105){$nombre = 'MasterCard Débito';}
    if($tarjeta_id == 106){$nombre = 'Maestro';}
    
    return $nombre; 
  }

?>