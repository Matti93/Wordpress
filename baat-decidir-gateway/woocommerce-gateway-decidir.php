<?php
/**
 * Plugin Name: Baat Decidir Gateway
 * Plugin URI: https://baat.com.ar/
 * Description: Paga con la plataforma de decidir por woocommerce.
 * Author: Baat WordPress Study
 * Author URI: https://baat.com.ar/
 * Version: 0.0.1
 *
 *
 *
 * @package   WC-Gateway-Decidir
 * @author    Baat WordPress Study
 * @category  Admin
 * @copyright Copyright (c) 2020, Baat WordPress Study
 *
 */

add_filter( 'woocommerce_payment_gateways', 'decidir_add_gateway_class' );
function decidir_add_gateway_class( $gateways ) {
    $gateways[] = 'WC_Decidir_Gateway'; // your class name is here
    return $gateways;
}

require_once( 'functions.php' );

add_action( 'plugins_loaded', 'decidir_init_gateway_class' );
function decidir_init_gateway_class() {

    class WC_Decidir_Gateway extends WC_Payment_Gateway {

        public function __construct() {
            $this->id = 'decidir_gateway'; // payment gateway plugin ID
            $this->icon = apply_filters( 'woocommerce_decidir_icon', plugins_url( 'baat-decidir-gateway/img/logos-tarjetas.png', plugin_dir_path( __FILE__ ) ) );
            $this->has_fields = true; // in case you need a custom credit card form
            $this->method_title = 'DECIDIR';
            $this->method_description = 'El Sistema de Pago Seguro DECIDIR (SPS) permite cobrar con tarjeta de crédito los productos y/o servicios que las empresas venden vía internet. Opera con VISA (Verified by VISA homologado), Mastercard, Diners, American Express, Tarjeta Shopping y Tarjeta Naranja, cumpliendo con los estándares internacionales y locales definidos por las tarjetas mencionadas';
            $this->supports = array(
                'products'
            );

            // Method with all the options fields
            $this->init_form_fields();

            // Load the settings.
            $this->init_settings();
            $this->title = $this->get_option( 'title' );
            $this->description = $this->get_option( 'description' );
            $this->enabled = $this->get_option( 'enabled' );
            $this->testmode = 'yes' === $this->get_option( 'testmode' );
            $this->private_key = $this->testmode ? $this->get_option( 'test_private_key' ) : $this->get_option( 'private_key' );
            $this->publishable_key = $this->testmode ? $this->get_option( 'test_publishable_key' ) : $this->get_option( 'publishable_key' );
 		        $this->cuotas        = $this->get_option( 'cuotas', array( ));
            $this->plan_gobierno = $this->get_option( 'plan_gobierno' );

            add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );

            // We need custom JavaScript to obtain a token
            add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );

            add_action( 'woocommerce_api_decidir', array( $this, 'webhook' ) );


         }


        public function init_form_fields(){

            $this->form_fields = array(
                'enabled' => array(
                    'title'       => 'Enable/Disable',
                    'label'       => 'Enable Decidir SPS',
                    'type'        => 'checkbox',
                    'description' => '',
                    'default'     => 'no'
                ),
                'title' => array(
                    'title'       => 'Title',
                    'type'        => 'text',
                    'description' => 'This controls the title which the user sees during checkout.',
                    'default'     => 'Credit Card',
                    'desc_tip'    => true,
                ),
                'description' => array(
                    'title'       => 'Description',
                    'type'        => 'textarea',
                    'description' => 'This controls the description which the user sees during checkout.',
                    'default'     => 'Pay with your credit card via our super-cool payment gateway.',
                ),
                'testmode' => array(
                    'title'       => 'Test mode',
                    'label'       => 'Enable Test Mode',
                    'type'        => 'checkbox',
                    'description' => 'Place the payment gateway in test mode using test API keys.',
                    'default'     => 'yes',
                    'desc_tip'    => true,
                ),
                'test_publishable_key' => array(
                    'title'       => 'Test Publishable Key',
                    'type'        => 'text'
                ),
                'test_private_key' => array(
                    'title'       => 'Test Private Key',
                    'type'        => 'password',
                ),
                'publishable_key' => array(
                    'title'       => 'Live Publishable Key',
                    'type'        => 'text'
                ),
                'private_key' => array(
                    'title'       => 'Live Private Key',
                    'type'        => 'password'
                ),
                'establishment_name' => array(
                  'title'       => __( 'Establishment Name', 'wc-gateway-decidir' ),
                  'type'        => 'text',
                  'description' => __( 'Enter your Establishment Name', 'wc-gateway-decidir' ),
                  'default'     => __( '', 'wc-gateway-decidir' ),
                  'desc_tip'    => true,
                ),
                'plan_gobierno' => array(
                  'title'       => __( 'Planes AHORA X para categoria', 'wc-gateway-decidir' ),
                  'type'        => 'text',
                  'description' => __( 'Ingresar ID de categoria', 'wc-gateway-decidir' ),
                  'default'     => __( '', 'wc-gateway-decidir' ),
                  'desc_tip'    => true,
                ),
              	'cuotas'  => array(
		              'type'            => 'cuotas'
	              ),
            );
        }


        public function generate_cuotas_html() {
          ob_start();
          include( 'cuotas.php' );
          return ob_get_clean();
        }

        public function validate_cuotas_field( $key ) {
          $banco_name     = isset( $_POST['banco_name'] ) ? $_POST['banco_name'] : array();
          $tarjeta     = isset( $_POST['tarjetas'] ) ? $_POST['tarjetas'] : array();
          $cuotas    = isset( $_POST['cuotas'] ) ? $_POST['cuotas'] : array();
          $recargo    = isset( $_POST['recargo'] ) ? $_POST['recargo'] : array();
          $service_enabled    = isset( $_POST['service_enabled'] ) ? $_POST['service_enabled'] : array();

          $services = array();

          if ( ! empty( $cuotas ) && sizeof( $cuotas ) > 0 ) {
            for ( $i = 0; $i <= max( array_keys( $cuotas ) ); $i ++ ) {

              if ( ! isset( $cuotas[ $i ] ) )
                continue;

              if ( $cuotas[ $i ] ) {
                  $services[] = array(
                  'banco_name'     =>  $banco_name[ $i ],
                  'tarjetas'     => $tarjeta[ $i ] ,
                  'cuotas' =>  $cuotas[ $i ] ,
                  'recargo' =>  $recargo[ $i ] ,
                  'enabled'    => isset( $service_enabled[ $i ] ) ? true : false
                );
              }
            }

          }

          return $services;
        }


        public function payment_fields() {

            if ( $this->description ) {
                // you can instructions for test mode, I mean test card numbers etc.
                if ( $this->testmode ) {
                    $this->description .= ' TEST MODE ENABLED. In test mode, you can use the card numbers listed in <a href="#" target="_blank" rel="noopener noreferrer">documentation</a>.';
                    $this->description  = trim( $this->description );
                }
                // display the description with <p> tags etc.
                echo wpautop( wp_kses_post( $this->description ) );
            }

            $_SESSION['publishable_key'] = $this->publishable_key;
            if($this->settings['testmode'] == 'no'){
               $_SESSION['urlSandbox'] = "https://live.decidir.com/api/v2";
            } else {
               $_SESSION['urlSandbox'] = "https://developers.decidir.com/api/v2";
            }
            ?>

             <div class='card-wrapper'></div>
            <!-- CSS is included via this JavaScript file -->
             <decidir_form>

                <?php if(!empty($this->settings['cuotas'])){
                        $plan = 'ok';
					              $planno = 'okokk';

 						            $listado_ordenado = array();
						            foreach($this->settings['cuotas'] as $tipo => $value){

                          if(!empty($listado_ordenado)){

                            foreach($listado_ordenado as $banco => $key){


                              if($banco == $value['banco_name']){

                                if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                                  if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                                  }else {
                                    $tarjeta = array(
                                         'tipo' => $value['tarjetas'] ,
                                         'cuotas' => $value['cuotas'],
                                         'recargo' => $value['recargo'],
                                      );
                                    $listado_ordenado[ $value['banco_name']][] = $tarjeta;
                                  }
                                }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                                   $tarjeta = array(
                                       'tipo' => $value['tarjetas'] ,
                                       'cuotas' => $value['cuotas'],
                                       'recargo' => $value['recargo'],
                                   );
                                  $listado_ordenado[ $value['banco_name']][] = $tarjeta;
                                }
                              }else{
                                if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                                  if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                                  }else{
                                    $listado_ordenado += array(
                                      $value['banco_name'] => array(
                                       '0' => array(
                                        'tipo' => $value['tarjetas'] ,
                                        'cuotas' => $value['cuotas'],
                                        'recargo' => $value['recargo'],
                                        )
                                      ),
                                    );
                                  }
                                }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                                   $listado_ordenado += array(
                                    $value['banco_name'] => array(
                                     '0' => array(
                                       'tipo' => $value['tarjetas'] ,
                                       'cuotas' => $value['cuotas'],
                                       'recargo' => $value['recargo'],
                                     )
                                    ),
                                    );
                                }
                              }
                            }
                          }else{
                            if( ($plan != 'ok' && $planno != 'ok') || ($plan != 'ok' && $planno === false) || ($plan == 'ok' && $planno == 'ok') || ($plan === false && $planno == 'ok')){ //13,16,17,18
                              if($value['cuotas'] == 13 || $value['cuotas'] == 16 || $value['cuotas'] == 17 || $value['cuotas'] == 18){

                              }else {
                                 $listado_ordenado = array(
                                   $value['banco_name'] => array(
                                   '0' => array(
                                     'tipo' => $value['tarjetas'] ,
                                     'cuotas' => $value['cuotas'],
                                     'recargo' => $value['recargo'],
                                   )
                                  ),
                                  );
                              }
                            }else if(($plan == 'ok' && $planno != 'ok') || ($plan === false && $planno != 'ok')) {
                              $listado_ordenado = array(
                                 $value['banco_name'] => array(
                                 '0' => array(
                                   'tipo' => $value['tarjetas'] ,
                                   'cuotas' => $value['cuotas'],
                                   'recargo' => $value['recargo'],
                                 )
                                ),
                              );
                            }


                          }
                        }
                        update_option('cuotas',$listado_ordenado);
                }if (strpos($_SERVER['HTTP_HOST'], 'mobiletech.com.ar') !== false) {
               ?>

                <input type="text" id="nombre_titular" name="card_holder_name" placeholder="NOMBRE COMPLETO"/>
                <input type="text" id="dni_titular" name="dni_titular" placeholder="DNI"/>
                <select id="decidir_banco_tipo" class="input-text wc-credit-card-form-card-name" name="decidir-banco-tipo">
                  <option value="">Seleccionar Banco</option>
                  <?php if(!empty($listado_ordenado)){
                    foreach($listado_ordenado as $opciones => $key){
                      echo '<option value="'.$opciones.'"  >'.$opciones.'</option>';
                    }
                } ?>

              </select>
                <select id="decidir_tarjeta_tipo" class="input-text wc-credit-card-form-card-name" name="decidir-tarjeta-tipo">
                  <option value="">Tipo Tarjeta</option>
                </select>
                <select id="decidir_installments" class="input-text wc-credit-card-form-card-name" name="decidir-cuotas">
                 <option value="0">Cuotas</option>
                </select>
                <input type="text" id="decidir_numero" name="number" placeholder="NUMERO DE TARJETA">
                <input type="text" id="card_expiration" name="expiry" placeholder="MM/AA"/>
                <input type="text" id="decidir_cvc" name="cvc" placeholder="CVC"/>

            </decidir_form>
<?php }else{?><p style="color:red; font-size:24;">ESTE PLUGIN NO CONTIENE LICENCIA, INGRESE A <a href="https://baat.com.ar" style="color:red; font-size:24;">Baat Estudio WEB</a> PARA CONTRATAR LA LICENCIA CORRESPONDIENTE</p>
		   <?php }?>
            <style>
            .jp-card .jp-card-front, .jp-card .jp-card-back {
                background: #6f5353 !important;
            }
              #decidir_installments{
                float: left;
                position: relative;
                /*padding: 20px 20px;*/
                margin: 3px 0 14px;
                font-family: inherit;
                font-size: 15px;
                line-height: 18px;
                font-weight: inherit;
                color: #717171;
                background-color: #fff;
                border: 1px solid #e6e6e6;
                outline: 0;
                -webkit-appearance: none;
                box-sizing: border-box;
                border-radius: 0;
                width: 100%;
                text-align: center;
              }
                #decidir_tarjeta_tipo, #decidir_banco_tipo {
                  position: relative;
                width: 100%;
                /*padding: 15px 20px;*/
                margin: 3px 0 14px;
                font-family: inherit;
                font-size: 15px;
                line-height: 18px;
                font-weight: inherit;
                color: #717171;
                background-color: #fff;
                border: 1px solid #e6e6e6;
                outline: 0;
                -webkit-appearance: none;
                box-sizing: border-box;
                /* height: 50px; */
                border-radius: 0;
                }
            </style>

              <fieldset id="<?php echo $this->id; ?>-cc-form"  style="display:none;" >
              <li>
                <label for="decidir-card-tipo">Seleccione su tarjeta <span class="required">*</span></label>
                <select id="decidir-card-tipo" class="input-text wc-credit-card-form-card-name" name="decidir-card-tipo">

                </select>
              </li>
              <li>
                  <input type="text" id="card_number" name="card_number" data-decidir="card_number" placeholder="CVC"/>
              </li>
              <li>
                <label for="card_expiration_month">Mes de vencimiento:</label>
                <input type="text" id="card_expiration_month"  data-decidir="card_expiration_month" placeholder="MM" value=""/>
              </li>
              <li>
                <label for="card_expiration_year">Año de vencimiento:</label>
                <input type="text" id="card_expiration_year"  data-decidir="card_expiration_year" placeholder="AA" value=""/>
              </li>
              <li>
                  <input type="text" id="security_code" name="security_code" data-decidir="security_code" placeholder="CVC"/>
              </li>
              <li>
                <label for="card_holder_name">Nombre del titular:</label>
                <input type="text" id="card_holder_name" data-decidir="card_holder_name" placeholder="TITULAR" value=""/>
              </li>
              <li>
                <label for="card_holder_doc_type">Tipo de documento:</label>
                <select data-decidir="card_holder_doc_type">
                  <option value="dni">DNI</option>
                </select>
              </li>
              <li>
                <label for="card_holder_doc_type">Numero de documento:</label>
                <input id="card_holder_doc_number" type="text"data-decidir="card_holder_doc_number" placeholder="" value=""/>
              </li>
              <div class="clear"></div>
            </fieldset>

            <input type="hidden" id="result_decidir"/>

            <script>
              var card = new Card({
                form: 'decidir_form',
                container: '.card-wrapper',
                 debug: false, // optional - default false

                formSelectors: {
                    nameInput: 'input[name="card_holder_name"]',

                },


                formatting: true, // optional - default true

                // Strings for translation - optional
                messages: {
                    validDate: 'valid\ndate', // optional - default 'valid\nthru'
                    monthYear: 'mm/yy', // optional - default 'month/year'
                },

                // Default placeholders for rendered fields - optional
                placeholders: {
                    number: '•••• •••• •••• ••••',
                    name: 'NOMBRE Y APELLIDO',
                    expiry: '••/••',
                    cvc: '•••'
                },

                masks: {
                    cardNumber: '•' // optional - mask card number
                },


            });
              </script>
      <style type="text/css">
        decidir_form {
          width: 100%;
          position: relative;
          display: table;
          margin: 0px auto;
          max-width: 300px;
        }
        .payment_method_decidir_gateway p {
            padding: 10px;
        }
        .card-wrapper {
            margin: 10px 0px;
        }
        .payment_method_decidir_gateway input {
            width: 100%;
            margin: 5px 0px;
            max-width: 300px;
            clear: both;
            float: left;
        }
        .payment_method_decidir_gateway .jp-card-name {
            font-size: 15px !important;
        }
        .payment_method_decidir_gateway img {
            max-width: 110px !important;
            float: right;
        }
        input#decidir_cvc {
            width: 80px;
            clear: none;
        }
        input#card_expiration {
            width: 100px;
            float: left;
            clear: left;
        }
        #payment_method_decidir_gateway {
          width: auto;
          clear: none;
          float: inherit;
        }
      </style>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.min.js"></script>

            <?php
        }
        /*
         * Custom CSS and JS, in most cases required only when you decided to go with a custom credit card form
         */
        public function payment_scripts() {

            // we need JavaScript to process a token only on cart/checkout pages, right?
            if ( ! is_cart() && ! is_checkout() && ! isset( $_GET['pay_for_order'] ) ) {
                return;
            }

            // if our payment gateway is disabled, we do not have to enqueue JS too
            if ( 'no' === $this->enabled ) {
                return;
            }

            // no reason to enqueue JavaScript if API keys are not set
            if ( empty( $this->private_key ) || empty( $this->publishable_key ) ) {
                return;
            }

            // do not work with card detailes without SSL unless your website is in a test mode
            if ( ! $this->testmode && ! is_ssl() ) {
                return;
            }

           if($this->settings['testmode'] == 'no'){
               $url = "https://live.decidir.com/api/v2";
            } else {
               $url = "https://developers.decidir.com/api/v2";
            }

            // let's suppose it is our payment processor JavaScript that allows to obtain a token
            wp_enqueue_script( 'decidir_js', 'http://licencias.baat.com.ar/decidir/decidir2.5.js' );

            // and this is our custom JS in your plugin directory that works with token.js
            wp_register_script( 'woocommerce_decidir', 'http://licencias.baat.com.ar/decidir/decidir.js', array( 'jquery', 'decidir_js' ) );
            wp_register_script( 'woocommerce_decidirb', 'http://licencias.baat.com.ar/decidir/card.js', array( 'jquery', 'decidir_js' ) );


            $ajaxurl = admin_url('admin-ajax.php');

            // in most payment processors you have to use PUBLIC KEY to obtain a token
            wp_localize_script( 'woocommerce_decidir', 'decidir_params', array(
                'publishableKey' => $this->publishable_key,
                'url' => $url ,
                'ajaxurl' => $ajaxurl ,
            ) );

            wp_enqueue_script( 'woocommerce_decidir' );
            wp_enqueue_script( 'woocommerce_decidirb' );

        }

        /*
         * Fields validation, more in Step 5
         */
        public function validate_fields(){

            if( empty( $_POST[ 'billing_first_name' ]) ) {
                wc_add_notice(  'First name is required!', 'error' );
                return false;
            }
            return true;

        }

        /*
         * We're processing the payments here
         */
        public function process_payment( $order_id ) {

            global $woocommerce;

              require_once __DIR__ . '/decidir/vendor/autoload.php';
              $clear_slashes = stripslashes($_COOKIE['result_decidir']);
              $result_decidir= json_decode($clear_slashes);

              $order = wc_get_order( $order_id );

              $keys_data = array('public_key' => $this->publishable_key, 'private_key' => $this->private_key);

              if($this->settings['testmode'] == 'no'){
                 $ambient = "prod";
              } else {
                 $ambient = "test";
              }

              $connector = new \Decidir\Connector($keys_data, $ambient);

              $decidir_MerchOrderIdnewdate = date("his");
              $site_transaction_id = $order_id .'-'. $decidir_MerchOrderIdnewdate ;
              $psp_Amount =  preg_replace( '#[^\d.]#', '', $order->order_total  );
              $amount = str_replace('.', '', $psp_Amount);

              $newdate = date("Y-m-d H:i:s");
              $psp_MerchTxRef = $order->customer_id .'-'. $decidir_MerchOrderIdnewdate;
              $psp_CardFirstName = $_POST['decidir_gateway-card-first-name'];
              $psp_CardLastName = $_POST['decidir_gateway-card-last-name'];
              $psp_Product = $_POST['decidir_gateway-card-tipo'];
              $psp_CardNumber = str_replace(' ', '', $_POST['decidir_gateway-card-number']);
              $data = $_POST['decidir_gateway-card-expiry'];
              $year = substr($data, strpos($data, "/") + 1);
              $month = str_split($data, 2);
              $psp_CardExpDate = $year . $month[0];
              $psp_CardExpDate = str_replace(' ', '', $psp_CardExpDate);
              $psp_CardSecurityCode = str_replace(' ', '', $_POST['decidir_gateway-card-cvc']);
              $psp_CustomerMail = $_POST['billing_email'];
              $psp_NumPayments = str_replace(' ', '', $_POST['decidir-cuotas']);
              $tarjeta_tipo = str_replace(' ', '', $_POST['decidir-tarjeta-tipo']);
              $decidir_card_tipo = intval($_POST['decidir-card-tipo']);


              $cs_data = array(
                "send_to_cs" => true,
                "channel" => "Web",
                "bill_to" => array(
                  "city" => $order->get_billing_city(),
                  "country" => "AR",
                  "customer_id" => $order->get_customer_id() . $order->get_billing_last_name(),
                  "email" => $order->get_billing_email(),
                  "first_name" => $order->get_billing_first_name(),
                  "last_name" => $order->get_billing_last_name(),
                  "phone_number" => $order->get_billing_phone(),
                  "postal_code" => $order->get_billing_postcode(),
                  "state" => $order->get_billing_state(),
                  "street1" => $order->get_billing_address_1(),
                  "street2" => $order->get_billing_address_2(),
                ),
                "ship_to" => array(
                  "city" => $order->get_shipping_city(),
                  "country" => "AR",
                  "customer_id" => $order->get_customer_id() . $order->get_billing_last_name(),
                  "email" => $order->get_billing_email(),
                  "first_name" => $order->get_shipping_first_name(),
                  "last_name" => $order->get_shipping_last_name(),
                  "phone_number" => $order->get_billing_phone(),
                  "postal_code" => $order->get_shipping_postcode(),
                  "state" => $order->get_shipping_state(),
                  "street1" => $order->get_shipping_address_1(),
                  "street2" => $order->get_shipping_address_2(),
                ),
                "currency" => "ARS",
                "amount" => $psp_Amount,
              );

          	  $items = $order->get_items();
              $cs_products = array();
              foreach( $items as $item ) {
								if ( $item['product_id'] > 0 ) {
									$product = wc_get_product( $item['product_id'] );
                  array_push($cs_products,
                    array(
                      "csitproductcode" => $product->get_sku(), //Código de producto. MANDATORIO.
                      "csitproductdescription" => $product->get_name(), //Descripción del producto. MANDATORIO.
                      "csitproductname" => $product->get_name(),  //Nombre del producto. MANDATORIO.
                      "csitproductsku" => $product->get_sku(), //Código identificador del producto. MANDATORIO.
                      "csittotalamount" => $product->get_price(), //MANDATORIO
                      "csitquantity" => $item['quantity'],//Cantidad del producto. MANDATORIO.
                      "csitunitprice" => $product->get_price() //Formato Idem CSITTOTALAMOUNT. MANDATORIO
                      )
                  );
                }
              }

              $shipping_data = $order->get_items( 'shipping' );
              if(is_array($shipping_data)):

                foreach($shipping_data as $k=>$sm){

                  array_push($cs_products,
                    array(
                      "csitproductcode" => 'envio', //Código de producto. MANDATORIO.
                      "csitproductdescription" => $sm['method_title'], //Descripción del producto. MANDATORIO.
                      "csitproductname" => $sm['method_title'],  //Nombre del producto. MANDATORIO.
                      "csitproductsku" => 'envio', //Código identificador del producto. MANDATORIO.
                      "csittotalamount" => $sm['total'], //MANDATORIO
                      "csitquantity" => 1,//Cantidad del producto. MANDATORIO.
                      "csitunitprice" => $sm['total'] //Formato Idem CSITTOTALAMOUNT. MANDATORIO
                    )
                  );
                }

              endif;

          //$cybersource = new Decidir\Cybersource\Retail($cs_data,$cs_products);
         // $connector->payment()->setCybersource($cybersource->getData());

              $data = array(
                    "site_transaction_id" => $site_transaction_id,
                    "token" => $result_decidir->id,
                    "payment_method_id" => (int)$tarjeta_tipo,
                    "bin" => $result_decidir->bin,
                    "amount" => $psp_Amount,
                    "currency" => "ARS",
                    "installments" => (int)$psp_NumPayments,
                    "description" => $this->settings['establishment_name'],
                    "payment_type" => "single",
                    "sub_payments" => array()
                  );


              try {
                $response = $connector->payment()->ExecutePayment($data);
                $status = $response->getStatus();
                if($status == 'approved'){

                  $json = json_encode($response);
                  update_post_meta($order_id, 'decidir_response',$json );

                  $order->update_status( 'processing', __( 'TRANSACCION ID: ' . $response->getSite_transaction_id(), 'wc-gateway-decidir' ) );
                  $order->add_order_note(
                    sprintf(
                      "Detalle pago: '%s'", $response->getId() . ' - ' . $status
                    )
                  );

                  // Reduce stock levels
                  $order->reduce_order_stock();

                  // Remove cart
                  WC()->cart->empty_cart();

                  // Return thankyou redirect
                  return array(
                    'result'  => 'success',
                    'redirect'  => $this->get_return_url( $order )
                  );

                } else {

                  $details = json_encode($response->getStatus_details());

                  $order->add_order_note(
                    sprintf(
                      "Detalle pago: '%s'", $status
                    )
                  );
                  $order->add_order_note(
                    sprintf(
                      "Detalle error: '%s'", $details
                    )
                  );

                  // Remove cart
                  //WC()->cart->empty_cart();
                  wc_add_notice( __( $status ), 'error' );


                }


               } catch( \Exception $e ) {

                $resultado = json_encode($e->getData());

                $order->add_order_note(
                    sprintf(
                      "Detalle error: '%s'", $resultado
                    )
                  );
                $detalle = json_encode($data);
                $order->add_order_note(
                    sprintf(
                      "Detalle error data: '%s'", $detalle
                    )
                  );


                wc_add_notice( __( $e->getData() ), 'error' );
              }

        }

        /*
         * In case you need a webhook, like PayPal IPN etc
         */
        public function webhook() {

            $order = wc_get_order( $_GET['id'] );
            $order->payment_complete();
            $order->reduce_order_stock();

            update_option('webhook_debug', $_GET);
        }

 

    }
}
