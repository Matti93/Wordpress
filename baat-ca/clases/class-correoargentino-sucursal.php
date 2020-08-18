<?php
class WC_Correoargentino_Sucursal extends WC_Shipping_Method{

	public function __construct($instance_id = 2){
	  
		$this->id = 'correoargentino_sucursal';
		$this->instance_id = absint( $instance_id );
		$this->method_title = __( 'Retiro en sucursal de CorreoArgentino', 'woocommerce' );

		$this->supports  = array(
			'shipping-zones',
			'instance-settings',
			'instance-settings-modal',
 		);

		$this->init_form_fields();
		$this->init_settings();

		$this->enabled	= $this->get_option( 'enabled' );
		$this->title	= $this->get_option( 'title' );

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
	}
  
	public function init_form_fields(){
	  
		$this->instance_form_fields = array(
			'enabled' => array(
			'title' 		=> __( 'Enable/Disable', 'woocommerce' ),
			'type' 			=> 'checkbox',
			'label' 		=> __( 'Activar Motomensajería', 'woocommerce' ),
			'default' 		=> 'yes'
			),
			'title' => array(
			'title' 		=> __( 'Method Title', 'woocommerce' ),
			'type' 			=> 'text',
			'description' 	=> __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
			'default'		=> __( 'Retiro en sucursal de CorreoArgentino', 'woocommerce' )
			)
		);
	}
  
	public function is_available( $package ){ // Los que no tienen envío por C.Arg: por ID > por peso

		require plugin_dir_path(__DIR__) . 'includes/tarifas.php';
		require plugin_dir_path(__DIR__) . 'includes/zonas.php';
		require plugin_dir_path(__DIR__) . 'includes/limites.php';

		foreach ( $package['contents'] as $item_id => $values ) {
			$_product = $values['data'];
			if ( in_array( $_product->get_id() , $productosSinCarg ) ){
				return false;
			}else{
				$weight = $_product->get_weight();
				if($weight > 250){														// Después ver si necesitamos revisar el peso acá también
					return false;
				}
			}
		}
		return true;
	}

	public function calculate_shipping($package = array()){
	
		//Calcular peso y dimensiones
		$peso = 0;
		$dimensiones = 0;
		$diferencia = 0;
		
		foreach ( $package['contents'] as $item_id => $values ) {
			$_product  = $values['data'];
			
			if ($_product->get_weight()){
				$peso =	$peso + $_product->get_weight() * $values['quantity'];
			}

			if ($_product->get_length() && $_product->get_width() && $_product->get_height()){
				$dimensiones = $dimensiones + (($_product->get_length() * $values['quantity']) * $_product->get_width() * $_product->get_height());
				$pesovolumetrico = $dimensiones / 6000;
			}
	
			if ($pesovolumetrico > $peso ) { $peso = $pesovolumetrico; }
		}

		require plugin_dir_path(__DIR__) . 'includes/tarifas.php';
		require plugin_dir_path(__DIR__) . 'includes/zonas.php';
		require plugin_dir_path(__DIR__) . 'includes/limites.php';

		//Sanitizar peso:
		switch ($peso){
			case ($peso <= 1):
				$peso = 1;
				break;
			case ($peso > 1 && $peso <= 3):
				$peso = 3;
				break;
			case ($peso > 3 && $peso <= 5):
				$peso = 5;
				break;
			case ($peso > 5 && $peso <= 10):
				$peso = 10;
				break;
			case ($peso > 10 && $peso <= 15):
				$peso = 15;
				break;
			case ($peso > 15 && $peso <= 20):
				$peso = 20;
				break;
			case ($peso > 20 && $peso <= 25):
				$peso = 25;
				break;
			case ($peso > 25 && $peso <= 30):
				$peso = 30;
				break;
			case ($peso > 30):
				$diferencia = $peso - 30;
				$peso = 30;
				break;
			break;
		}	

		$region = $package['destination']['state'];
		$regionalNacional = $provincia[$region];

		switch ($regionalNacional){
			case (0):
				$cost = $sucursalTarifaRegional[$peso] + ($diferencia * $sucursalTarifaRegional['adicional']);
				break;
			case (1):
				$cost = $sucursalTarifaNacional[$peso] + ($diferencia * $sucursalTarifaNacional['adicional']);
				break;
		}

		//Si el CP no tiene sucursal, ofrecer CP anterior y siguiente
		$cpostal = $package['destination']['postcode'];

		if ( ! array_key_exists($cpostal,$codigosPostales) ){
			
			krsort($codigosPostales);
			foreach ($codigosPostales as $cpostalAltA => $value){
				if ($cpostalAltA <= $cpostal) break;
			}
			ksort($codigosPostales);
			foreach ($codigosPostales as $cpostalAltB => $value){
				if ($cpostalAltB >= $cpostal) break;
			}

		}	

		// Lógica de envíos gratis

		$total = WC()->cart->get_displayed_subtotal();

		if ( $total >= $envioGratis && ! in_array( $_product->get_id() , $productosSinGratis ) ){
				$cost = 0;
		}

// Enviar el precio calculado

		if ($cost == 0) {

			foreach ($codigosPostales[$cpostal] as $clave => $valor){

				$this->add_rate( array(
				'id' 	=> $valor,
				'label' => $this->title . ': ' . $valor . ' ¡GRATIS!',
				'cost' 	=> $cost
				));
			
			}
		}else{
			
			if (isset($cpostalAltA)){
		
				foreach ($codigosPostales[$cpostalAltA] as $clave => $valor){
					
					$this->add_rate( array(
					'id' 	=> $valor,
					'label' => $this->title . ': ' . $valor,
					'cost' 	=> $cost
					));
				}
				foreach ($codigosPostales[$cpostalAltB] as $clave => $valor){
					$this->add_rate( array(
					'id' 	=> $valor,
					'label' => $this->title . ': ' . $valor,
					'cost' 	=> $cost
					));
				}
			
			}else{
	
				foreach ($codigosPostales[$cpostal] as $clave => $valor){

					$this->add_rate( array(
					'id' 	=> $valor,
					'label' => $this->title . ': ' . $valor,
					'cost' 	=> $cost
					));
				}
			}
		}
	}
}
