
<?php 


add_action('wp_head','custom_css_step_checkout');
function custom_css_step_checkout()
{
?>
    <style type="text/css">
        .step-checkout.step-inactive
        {
            display: none !important;
        }

    </style>
<?php
}


add_action('woocommerce_register_form','woocommerce_register_form_end_callback');
function woocommerce_register_form_end_callback()
{
    global $woocommerce;
    $countries_obj   = new WC_Countries();
    $countries   = $countries_obj->__get('countries');
    
    echo '<h2>INFORMACIÓN PERSONAL</h2>';
    echo '<div id="">';
    woocommerce_form_field('my_display_name_field', array(
    'type'       => 'text',
    'required'  => true,
    'class'      => array( 'display_name' ),
    'label'      => __('Nombre y apellido'),
    'placeholder'    => __(''),
    ));
    echo '</div>';
    
        echo '<div id="">';
    woocommerce_form_field('my_fecha_nacimiento_field', array(
    'type'       => 'date',
    'required'  => true,
    'class'      => array( 'fecha_nacimiento' ),
    'label'      => __('Fecha de nacimiento'),
    'placeholder'    => __(''),
    ));
    echo '</div>';
    
    
    echo '<div id="my_custom_countries_field">';
    woocommerce_form_field('my_country_field', array(
    'type'       => 'select',
    'required'  => true,
    'class'      => array( 'chzn-drop' ),
    'label'      => __('País'),
    'placeholder'    => __('Enter something'),
    'options'    => $countries
    ));
    echo '</div>';
 
     echo '<div id="">';
    woocommerce_form_field('my_provincia_field', array(
    'type'       => 'text',
    'required'  => true,
    'class'      => array( 'provincia' ),
    'label'      => __('Provincia'),
    'placeholder'    => __(''),
    ));
    echo '</div>';
    

    echo '<div id="">';
    woocommerce_form_field('my_localidad_field', array(
    'type'       => 'text',
    'required'  => true,
    'class'      => array( 'localidad' ),
    'label'      => __('Localidad'),
    'placeholder'    => __(''),
    ));
    echo '</div>';
    
 
    echo '<div id="">';
    woocommerce_form_field('my_telefono_field', array(
    'type'       => 'text',
    'required'  => true,
    'class'      => array( 'telefono' ),
    'label'      => __('Teléfono fijo / Celular'),
    'placeholder'    => __(''),
    ));
    echo '</div>';
    
    echo '<h2>INFORMACIÓN SOBRE TU CLUB</h2>';
    echo '<p>Si no perteneces a ningún Club puedes completar los siguientes campos simplemente con un guion (-)</p>';
    
    
    echo '<div id="">';
    woocommerce_form_field('my_nombre_club_field', array(
    'type'       => 'text',
    'required'  => true,
    'class'      => array( 'nombre_club' ),
    'label'      => __('Nombre del club'),
    'placeholder'    => __(''),
    ));
    echo '</div>';
    
    
    echo '<div id="">';
    woocommerce_form_field('my_cargo_ocupas_field', array(
    'type'       => 'text',
    'required'  => true,
    'class'      => array( 'cargo_ocupas' ),
    'label'      => __('Cargo que ocupas'),
    'placeholder'    => __(''),
    ));
    echo '</div>';
    
    
    echo '<div id="">';
    woocommerce_form_field('my_nombre_regional_field', array(
    'type'       => 'text',
    'required'  => false,
    'class'      => array( 'nombre_regional' ),
    'label'      => __('Nombre del regional'),
    'placeholder'    => __(''),
    ));
    echo '</div>';
}


//--HOOK PARA GUARDAR INFORMACION UNA VES REGISTRADO
add_action( 'user_register', 'woo_register_custom_fields', 10, 1 );

function woo_register_custom_fields( $user_id ) {

    /*Si existe el nombre*/
    if (isset($_POST['my_display_name_field'])){
        $data_names = explode(' ',$_POST['my_display_name_field']);
        if(isset($data_names[0])){
            update_user_meta($user_id, 'first_name', $data_names[0]);
        }
        if(isset($data_names[1])){
            update_user_meta($user_id, 'last_name', $data_names[1]);
        }

    }
    if (isset( $_POST['my_fecha_nacimiento_field'])){
        update_user_meta($user_id, 'my_fecha_nacimiento_field', $_POST['my_fecha_nacimiento_field']);
    }
    if (isset( $_POST['my_country_field'])){
        update_user_meta($user_id, 'billing_country', $_POST['my_country_field']);
    }


    if (isset( $_POST['my_localidad_field'])){
        update_user_meta($user_id, 'billing_city', $_POST['my_localidad_field']);
    }

    if (isset( $_POST['my_provincia_field'])){
        update_user_meta($user_id, 'billing_state', $_POST['my_provincia_field']);
    }
    if (isset( $_POST['my_telefono_field'])){
        update_user_meta($user_id, 'billing_phone', $_POST['my_telefono_field']);
    }
    /*Datos del club*/
    if (isset( $_POST['my_nombre_club_field'])){
        update_user_meta($user_id, 'my_nombre_club_field', $_POST['my_nombre_club_field']);
    }
    if (isset( $_POST['my_cargo_ocupas_field'])){
        update_user_meta($user_id, 'my_cargo_ocupas_field', $_POST['my_cargo_ocupas_field']);
    }
      if (isset( $_POST['my_nombre_regional_field'])){
        update_user_meta($user_id, 'my_nombre_regional_field', $_POST['my_nombre_regional_field']);
    }
    
}



//FUNCION PARA MOSTRAS LOS DASTOS DEL USUARIO AGREGADOS RECIENTE MENTE
add_action('woocommerce_edit_account_form_start','woo_custom_field_edit_account');
function woo_custom_field_edit_account()
{
    $user_id = get_current_user_id();
    $my_nombre_club_field = get_user_meta($user_id,'my_nombre_club_field',true);
    $my_cargo_ocupas_field = get_user_meta($user_id,'my_cargo_ocupas_field',true);
    $my_nombre_regional_field = get_user_meta($user_id,'my_nombre_regional_field',true);
    $my_fecha_nacimiento_field = get_user_meta($user_id,'my_fecha_nacimiento_field',true);

?>


    <div class="custom_fields">

             <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
            <label for="account_my_fecha_nacimiento_field"><?php esc_html_e( 'Cumpleaños', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
            <input type="date" class="woocommerce-Input woocommerce-Input--text input-text" name="my_fecha_nacimiento_field" id="my_fecha_nacimiento_field" value="<?php echo esc_attr( $my_fecha_nacimiento_field ); ?>" />
        </p>
        <div class="clear"></div>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="account_nombre_club"><?php esc_html_e( 'Nombre del club', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="my_nombre_club_field" id="my_nombre_club_field" value="<?php echo esc_attr( $my_nombre_club_field ); ?>" />
    </p>
    <div class="clear"></div>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="account_cargo_ocupas"><?php esc_html_e( 'Cargo que ocupas', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="my_cargo_ocupas_field" id="my_cargo_ocupas_field" value="<?php echo esc_attr( $my_cargo_ocupas_field ); ?>" />
    </p>
    <div class="clear"></div>

    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="account_nombre_regional"><?php esc_html_e( 'Nombre del Regional', 'woocommerce' ); ?>&nbsp;<span class="required"></span></label>
        <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="my_nombre_regional_field" id="my_nombre_regional_field" value="<?php echo esc_attr( $my_nombre_regional_field ); ?>" />
    </p>
    <div class="clear"></div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function($)
        {
            account_email = $('#account_email').parent();
            $(".custom_fields").insertAfter(account_email);           
        });

    </script>

<?php 
}


//FUNCION PARA GUARDAR LOS DATOS ESCRITOS EN MY ACCOUNT 
//do_action( 'woocommerce_save_account_details', $user->ID );
add_action('woocommerce_save_account_details','woo_custom_save_account_details',10,1);
function woo_custom_save_account_details($user_id)
{

    /*Datos del club*/
    if (isset( $_POST['my_nombre_club_field'])){
        update_user_meta($user_id, 'my_nombre_club_field', $_POST['my_nombre_club_field']);
    }
    if (isset( $_POST['my_cargo_ocupas_field'])){
        update_user_meta($user_id, 'my_cargo_ocupas_field', $_POST['my_cargo_ocupas_field']);
    }
    if (isset( $_POST['my_nombre_regional_field']))
    {
        update_user_meta($user_id, 'my_nombre_regional_field', $_POST['my_nombre_regional_field']);
    }
    if (isset( $_POST['my_fecha_nacimiento_field']))
    {
        update_user_meta($user_id, 'my_fecha_nacimiento_field', $_POST['my_fecha_nacimiento_field']);
    }

    
}



//---------------FUNCIONES PARA EL EVENTO CHECKOUT-------------
//checkout-button
add_action('woocommerce_proceed_to_checkout','woocommerce_proceed_to_checkout_custom_button');

add_action('woocommerce_widget_shopping_cart_buttons','woocommerce_proceed_to_checkout_custom_button');
function woocommerce_proceed_to_checkout_custom_button()
{
   ?>
   <style type="text/css">
       .checkout-button{display: none;}
       .button.checkout.wc-forward{display: none !important;}
       .checkout-button-custom{display: block !important;}
   </style>

    <?php
        if(is_user_logged_in()){
     ?>
   <a href="#" class="checkout-button checkout-button-custom button alt wc-forward">
    Finalizar Pedido</a>

    <script type="text/javascript">
        jQuery(document).ready(function($)
        {
            var searchRequest = null;
            $(".checkout-button.checkout-button-custom").insertAfter('.button.btn-cart.wc-forward');

            $(document).on('click','.checkout-button-custom',function(){
            var form_data = new FormData();
            $(".msj-custom-checkout").remove();
            $(this).after('<p class="msj-custom-checkout">Enviando Orden...</p>');

                ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
                form_data.append('action', 'woo_custom_order');
                /*Veremos si ya existe una peticion en curso*/
                    if (searchRequest != null){ 
                          searchRequest.abort();
                    }
            
                /*Obtendremos la variable search request para hacer una nueva peticion*/
                    searchRequest = jQuery.ajax({
                        url: ajaxurl,
                        type: 'post',
                        contentType: false,
                        processData: false,
                        data: form_data,
                        cache: false,
                        success: function (response) {
                        if(response=='0'){
                             $(".msj-custom-checkout").text('Tenés que seleccionar al menos un producto del carrito.');
                        }else{
                          order_data = response.split('!');
                          url_location = order_data[2]+'order-received/'+order_data[0]+'/?key='+order_data[1];
                          //vamos a la pagina de la orden
                          window.location.href = url_location; 
                        }

                        
                    },  
                    error: function (response) {
                        console.log('error or aborted');
                    }
                    });
            });
        });
    </script>
<?php } else{ ?>
<p>Tenés que iniciar sesión para poder realizar el pedido.</p>
<?php } ?>
   <?php 
}


//==============FUNCION AJAX PARA RECIBIR LA ORDEN DEL PEDIDO=========


add_action('wp_ajax_woo_custom_order','woo_custom_order_callback');
    add_action('wp_ajax_nopriv_woo_custom_order','woo_custom_order_callback');
    function woo_custom_order_callback()
    {
        // Vamos a crear el pedido con los datos    
        global $woocommerce;

         $current_user = wp_get_current_user();

         /*Datos adicionales*/
        $billing_country = get_user_meta(get_current_user_id(), 'billing_country',true);
        $billing_city = get_user_meta(get_current_user_id(), 'billing_city',true);
        $billing_state = get_user_meta(get_current_user_id(), 'billing_state',true);
        $billing_phone = get_user_meta(get_current_user_id(), 'billing_phone',true);


         $items = @$woocommerce->cart->get_cart();
         $checkout_url = $woocommerce->cart->get_checkout_url();
		$order = wc_create_order();
         if(count($items)>0){
             $address = array(
                'first_name' => $current_user->user_firstname,
                'last_name'  => $current_user->user_lastname,
                'email'      => $current_user->user_email,
                'country' => $billing_country,
                'city' => $billing_city,
                'state'=> $billing_state,
                'phone'=> $billing_phone,
               );
                $order->set_address( $address, 'billing' );
                $order->set_address( $address, 'shipping' );
                update_post_meta( $order->get_id(), '_customer_user', get_current_user_id());
             
                foreach($items as $item => $values) {
                    $_product =  wc_get_product( $values['data']->get_id());
                   //buscamos el producto costumizado con el
                    $price_costumize = ($_product->get_price());
                    $_product->set_price($price_costumize);
                    //agregamos el producto
                    $order->add_product($_product,$values['quantity']);
                 }
            //Calculamos el total d
            $order->calculate_totals();
             //limpiamos el carrito de compras
            $woocommerce->cart->empty_cart();
            //wooappord_template_email($order->get_id());
            //enviamos el correo electronico
            woo_custom_send_mail($order->get_id());
            //creamos la accion para la conexion con el pedido y el otro plugin
            //do_action('woocshop_order_thankyou',$order->get_id());
            echo $order->get_id().'!'.$order->order_key.'!'.$checkout_url;
        }else{
            //wp_redirect(get_home_url().'/carrito/');
            echo 0;
        }
        wp_die();
    }

    function woo_custom_send_mail($order_id)
    {
        $mailer = WC()->mailer()->get_emails();
        // Use one of the active emails e.g. "Customer_Completed_Order"
        // Wont work if you choose an object that is not active
        // Assign heading & subject to chosen object
        $heading = 'Pedido realizado.' ;
        $subject = 'Pedido realizado.';

        $mailer['WC_Email_Customer_Completed_Order']->heading = $heading;
        $mailer['WC_Email_Customer_Completed_Order']->settings['heading'] = $heading;
        $mailer['WC_Email_Customer_Completed_Order']->subject = $subject;
        $mailer['WC_Email_Customer_Completed_Order']->settings['subject'] = $subject;
      
        // Send the email with custom heading & subject
        $mailer['WC_Email_Customer_Completed_Order']->trigger($order_id);
    }


    /*Agregamos el nuevo correo electronico*/
    add_filter( 'woocommerce_email_headers', 'mycustom_headers_filter_function', 10, 2);
    function mycustom_headers_filter_function( $headers, $object ) {
        $correo_remitente = get_option( 'woocommerce_email_from_address',true);
        if ($object == 'customer_completed_order') {
            $headers .= 'BCC: My name <'.$correo_remitente.'>' . "\r\n";
        }

        return $headers;
    }