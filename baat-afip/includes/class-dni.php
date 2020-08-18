<?php
  if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
  }

  add_action( 'woocommerce_register_form_start', 'woo_afip_add_dni_field_to_register' );
  add_action( 'woocommerce_created_customer' , 'woo_afip_save_DNI' );
  add_action( 'woocommerce_edit_account_form', 'woo_afip_add_dni_field_to_my_account');
  add_action( 'woocommerce_save_account_details', 'woo_afip_save_DNI' );
  add_action( 'woocommerce_checkout_fields' , 'woo_afip_dni_checkout_field' );

	add_action( 'woocommerce_checkout_process', 'woo_afip_checkout_field_process' );
	add_action( 'woocommerce_checkout_update_order_meta', 'woo_afip_update_order_meta' );
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'woo_afip_display_admin_order_meta');
	add_filter( 'woocommerce_email_order_meta_keys', 'woo_afip_display_dni_in_email_fields' );

  function woo_afip_add_dni_field_to_register(){
			?>
			<p class="form-row form-row-first">
				<label for="woo_afip_billing_dni"><?php _e( 'DNI / CUIT / CUIL', 'baat-afip' ); ?> <span class="required">*</span></label>
				<input type="text" class="input-text" name="billing_dni_afip" id="reg_billing_dni_afip" value="<?php if ( ! empty( $_POST['billing_dni_afip'] ) ) esc_attr_e( $_POST['billing_dni_afip'] ); ?>" />
			</p>
			<?php
	}
	
	function woo_afip_add_dni_field_to_my_account(){
			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );
			if ( !$user )
				return;
			?>
			  <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide"> 
				  <label for="woo_afip_billing_dni"><?php _e( 'DNI / CUIT / CUIL', 'baat-afip' ); ?> <span class="required">*</span></label> 
				  <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_dni_afip" id="ma_billing_dni_afip" value="<?php echo esc_attr( $user->billing_dni_afip ); ?>" /> 
			  </p> 
			<?php
	}
	
	function woo_afip_save_DNI( $user_id ) {
		if(isset($_POST[ 'billing_dni_afip' ])){
			update_user_meta( $user_id, 'billing_dni_afip', htmlentities( $_POST[ 'billing_dni_afip' ] ) ); 
		}
	}
	
	function woo_afip_dni_checkout_field( $checkout_fields ){
		 //$user_meta  =  get_user_meta ( get_current_user_id() );
		 //$billing_dni =  $user_meta['billing_dni_afip']['0'];
		 $checkout_fields['billing']['billing_dni_afip']  =  array(
       'label'          => __('DNI / CUIT / CUIL', 'woocommerce'),
       'placeholder'    => _x('20313131316 ', 'placeholder', 'baat-afip'),
       'required'       => true,
       'clear'          => false,
       'type'           => 'number',
       'class'          => array('form-row-wide'),
       'priority'    => 0,
		 );
		 return $checkout_fields;
	}
	
	function woo_afip_checkout_field_process() {
		if ( ! $_POST['billing_dni_afip'] ||  !preg_match('/^[0-9]*$/', $_POST['billing_dni_afip']) )	
			wc_add_notice( __( 'DNI / CUIT / CUIL inválido, ingresar solo números. </br>IF YOU ARE NOT FROM ARGENTINA, JUST TYPE ANY NUMBER','baat-afip' ), 'error' );
	}
	
	function woo_afip_update_order_meta( $order_id ) {
		if ( ! empty( $_POST['billing_dni_afip'] ) ) {
			update_post_meta( $order_id, 'DNI', sanitize_text_field( $_POST['billing_dni_afip'] ) );
		}
	}
	
	function woo_afip_display_admin_order_meta($order){
		echo '<p><strong>'.__('DNI / CUIT / CUIL').':</strong> ' . get_post_meta( $order->id, 'DNI', true ) . '</p>';
	}

	function woo_afip_display_dni_in_email_fields( $keys ){		 
        $keys['DNI'] = 'DNI';
        return $keys;
	}