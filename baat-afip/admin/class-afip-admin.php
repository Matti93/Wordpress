<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Baat_Afip_Admin' ) ) :
function baat_afip_add_settings() {
	/**
	 * Settings class
	 *
	 * @since 1.0.0
	 */
	class Baat_Afip_Admin extends WC_Settings_Page {
    		
		/**
		 * Setup settings class
		 *
		 * @since  1.0
		 */
		public function __construct() {
		
			$this->id    = 'wanderulst_afip_settings';
			$this->label = __( 'Afip Settings', 'baat-afip' );
      $this->version = '0.0.1';
      $this->plugin_name = 'Baat Afip';

			add_filter( 'woocommerce_settings_tabs_array',        array( $this, 'add_settings_page' ), 20 );
			add_action( 'woocommerce_settings_' . $this->id,      array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'woocommerce_sections_' . $this->id,      array( $this, 'output_sections' ) );
		}
    		
		/**
		 * Get sections
		 *
		 * @return array
		 */
		public function get_sections() {
		
			$sections = array(
				''       => __( 'Datos Factura', 'baat-afip' ),
				'testeo' => __( 'API Testeo', 'baat-afip' ),
				'produccion'  => __( 'API Produccion', 'baat-afip' )
			);
			
			return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
		}
		
		/**
		 * Get settings array
		 *
		 * @since 1.0.0
		 * @param string $current_section Optional. Defaults to empty string.
		 * @return array Array of settings
		 */
		public function get_settings( $current_section = '' ) {
		
			if ( 'testeo' == $current_section ) {
        
        $get_api_status = get_option('afip_status');     
        $afip_entorno = get_option('wc_settings_tab_woo_afip_entorno');
        if($afip_entorno == 1){
          $afip_entorno = 'Testeos';
        } else if($afip_entorno == 2){
          $afip_entorno = 'Producción';
        }                         
        echo '<h4>Estado de conexión: ';
        if(empty($get_api_status)){
          echo '<span id="api_status" style="color: #cc2727;">Aún no se guardaron/generaron los certificados.</span></h4>';
        } else {
          echo '<span id="api_status" style="color: #17af30;">Activo - '.$afip_entorno.'</span></h4>';
        } 
        
			  $wc_settings_tab_woo_afip_cuit = get_option('wc_settings_tab_woo_afip_cuit');
			  $wc_settings_tab_woo_afip_api = get_option('wc_settings_tab_woo_afip_api');
        echo '<input id="cuit" name="cuit" type="hidden" value="'.$wc_settings_tab_woo_afip_cuit.'">';
        echo '<input id="apikey" name="apikey" type="hidden" value="'.$wc_settings_tab_woo_afip_api.'">';
				$settings = apply_filters( 'afip_section2_settings', array(
          
          'section_instrucciones' => array(
							 'name'     => __( 'Instrucciones:', 'baat-afip' ),
 								'type'     => 'title',
 								'desc' => '<a href="#wc_settings_tab_woo_afip_section_certificado-description">Generar los certificados de Testeo o Producción.</a><br> ',
 								'id'       => 'wc_settings_tab_woo_afip_section_instrucciones'
 						),         
				
					'section_certificados' => array(
							 'name'     => __( '', 'baat-afip' ),
 								'type'     => 'title',
 								'desc' => 'Para generar sus certificados digitales tanto en Homologación como en Producción, tiene que <strong>validar su CUIT</strong> y el certificado se generará debajo de esta linea: <br> 
                  <div id="cert_out">
                  <h3>1- COPIAR Y PEGAR EN AFIP</h3> <button id="copy_but">Copiar Certificado</button>

                  <div id="cert_out_code"></div> 
                  <strong>Una vez generado, debe copiar el código y pegarlo en la pagina de la Afip. <a href="https://shop.baat-webdesign.com/manual-afip/" target="_blank">INSTRUCCIONES</a></strong><br><br> 
                  <strong>Homologación: </strong><a href="https://wsass-homo.afip.gob.ar/wsass/portal/main.aspx" target="_blank">https://wsass-homo.afip.gob.ar/wsass/portal/main.aspx</a><br>
                  <strong>Producción:   </strong><a href="https://serviciosweb.afip.gob.ar/ClaveFiscal/AdminRel/relationAdd.aspx" target="_blank">https://serviciosweb.afip.gob.ar/ClaveFiscal/AdminRel/relationAdd.aspx</a><br>
                  </div>
									<div id="subir_cert" style="font-weight: bold;cursor: pointer;"> <h3>2- Subir CSR generado en AFIP</h3> </div>   <br> 
									<div id="certificado"></div> <br>',
 								  'id'       => 'wc_settings_tab_woo_afip_section_certificado'
 						),
						'sectionend_certificados' => array(
 								'type' => 'sectionend',
 								'id' => 'wc_settings_tab_woo_afip_section_certificado_end'
 						),			
					
				) );
      } else if ( 'produccion' == $current_section ) {
        
        $get_api_status = get_option('afip_status');     
        $afip_entorno = get_option('wc_settings_tab_woo_afip_entorno');
        if($afip_entorno == 1){
          $afip_entorno = 'Testeos';
        } else if($afip_entorno == 2){
          $afip_entorno = 'Producción';
        }                         
        echo '<h4>Estado de conexión: ';
        if(empty($get_api_status)){
          echo '<span id="api_status" style="color: #cc2727;">Aún no se guardaron/generaron los certificados.</span></h4>';
        } else {
          echo '<span id="api_status" style="color: #17af30;">Activo - '.$afip_entorno.'</span></h4>';
        } 
        
			  $wc_settings_tab_woo_afip_cuit = get_option('wc_settings_tab_woo_afip_cuit');
			  $wc_settings_tab_woo_afip_api = get_option('wc_settings_tab_woo_afip_api');
        echo '<input id="cuit" name="cuit" type="hidden" value="'.$wc_settings_tab_woo_afip_cuit.'">';
        echo '<input id="apikey" name="apikey" type="hidden" value="'.$wc_settings_tab_woo_afip_api.'">';
				$settings = apply_filters( 'afip_section2_settings', array(
          
          'section_instrucciones' => array(
							 'name'     => __( 'Instrucciones:', 'baat-afip' ),
 								'type'     => 'title',
 								'desc' => '<a href="#wc_settings_tab_woo_afip_section_certificado-description">Generar los certificados de Testeo o Producción.</a><br> ',
 								'id'       => 'wc_settings_tab_woo_afip_section_instrucciones'
 						),         
				
					'section_certificados' => array(
							 'name'     => __( '', 'baat-afip' ),
 								'type'     => 'title',
 								'desc' => 'Para generar sus certificados digitales tanto en Homologación como en Producción, tiene que <strong>validar su CUIT</strong> y el certificado se generará debajo de esta linea: <br> 
                  <div id="cert_out">
                  <h3>1- COPIAR Y PEGAR EN AFIP</h3> <button id="copy_but">Copiar Certificado</button>

                  <div id="cert_out_code"></div> 
                  <strong>Una vez generado, debe copiar el código y pegarlo en la pagina de la Afip. <a href="https://shop.baat-webdesign.com/manual-afip/" target="_blank">INSTRUCCIONES</a></strong><br><br> 
                  <strong>Homologación: </strong><a href="https://wsass-homo.afip.gob.ar/wsass/portal/main.aspx" target="_blank">https://wsass-homo.afip.gob.ar/wsass/portal/main.aspx</a><br>
                  <strong>Producción:   </strong><a href="https://serviciosweb.afip.gob.ar/ClaveFiscal/AdminRel/relationAdd.aspx" target="_blank">https://serviciosweb.afip.gob.ar/ClaveFiscal/AdminRel/relationAdd.aspx</a><br>
                  </div>
									<div id="subir_cert" style="font-weight: bold;cursor: pointer;"> <h3>2- Subir CSR generado en AFIP</h3> </div>   <br> 
									<div id="certificado"></div> <br>',
 								  'id'       => 'wc_settings_tab_woo_afip_section_certificado'
 						),
						'sectionend_certificados' => array(
 								'type' => 'sectionend',
 								'id' => 'wc_settings_tab_woo_afip_section_certificado_end'
 						),			
					
				) );        
			} else {				
				/**
				 * Filter Plugin Section 1 Settings
				 *
				 * @since 1.0.0
				 * @param array $settings Array of the plugin settings
				 */
				$settings = apply_filters( 'afip_section1_settings', array(
				
            'section_title' => array(
                'name'     => __( 'API Afip Baat', 'baat-afip' ),
                'type'     => 'title',
 								'desc' => 'Cargar todos los datos. (Una vez cargados, guardarlos, despues de eso, vas a poder seguir con la carga de la API.) </br>',
                'id'       => 'wc_settings_tab_woo_afip_section_title'
            ),        
 						'api_key' => array(
                'name' => __( 'Baat API Key', 'baat-afip' ),
                'type' => 'password',
                'desc' => __( 'Ingresar Baat API Key.', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_api'
            ),		
	          'entorno' => array(
                'name' => __( 'Entorno', 'baat-afip' ),
                'type' => 'select',
                'id'   => 'wc_settings_tab_woo_afip_entorno',
								'default' => '1',
								'options' => array(
										'1' => 'Testeo',
										'2' => 'Producción',
								 )
            ),							
            'cuit' => array(
                'name' => __( 'CUIT', 'baat-afip' ),
                'type' => 'text',
                'desc' => __( 'Ingresar CUIT que va a facturar, sin guiones.', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_cuit'
            ),
            'punto_venta' => array(
                'name' => __( 'Punto de Venta', 'baat-afip' ),
                'type' => 'text',
                'desc' => __( 'Ingresar el Punto de Venta para factura electronica. Ej; 00002', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_punto_venta'
            ),          
	          'razonsocial' => array(
                'name' => __( 'Razón Social', 'baat-afip' ),
                'type' => 'text',
                'desc' => __( 'Texto a mostrar en titulo de factura.', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_razonsocial'
            ),		
	          'inicio' => array(
                'name' => __( 'Inicio Actividad', 'baat-afip' ),
                'type' => 'text',
                'desc' => __( 'Texto a mostrar en factura. EJ: 29/12/2016', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_inicio'
            ),		          
	          'bienes' => array(
                'name' => __( 'Conceptos a incluír', 'baat-afip' ),
                'type' => 'select',
                'desc' => __( 'Que se va a facturar.', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_bienes',
								'default' => '1',
								'options' => array(
										'1' => 'Productos',
										'2' => 'Servicios',
										'3' => 'Productos y Servicios',
								 )
            ),	
	          'condicionventa' => array(
                'name' => __( 'Condicion de Venta', 'baat-afip' ),
                'type' => 'select',
                'desc' => __( 'Como se va a facturar.', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_condicionventa',
								'default' => '1',
								'options' => array(
									'1' => 'Contado',
									'4' => 'Cuenta Corriente',
									'3' => 'Tarjeta de Debito',
									'4' => 'Tarjeta de Credito',
									'5' => 'Cheque',
									'6' => 'Ticket',
									'7' => 'Otro',
									'8' => 'MercadoPago',
									'9' => 'Cobro Digital',
									'10' => 'DineroMail',
									'11' => 'Decidir',
									'12' => 'Todo Pago',
								 )
            ),						
						'condicion' => array(
							'name' => __( 'Condicion frente al IVA', 'baat-afip' ),
							'type' => 'text',
							'id'   => 'wc_settings_tab_woo_afip_condicion',
              				'desc'  => 'Ingrese su Condición frente al IVA.'     
						),									
						'ingresosbrutos' => array(
							'name' => __( 'Ingresos Brutos', 'baat-afip' ),
							'type' => 'text',
							'id'   => 'wc_settings_tab_woo_afip_ingresosbrutos',
							'default' => 'Exento',
 							'desc'  => 'Ingrese su Condición frente a Ingresos Brutos.'     

             ),				
						'tipofactura' => array(
											'name' => __( 'Tipo de Factura', 'baat-afip' ),
											'type' => 'select',
											'desc' => __( 'Tipos de comprobantes. ', 'baat-afip' ),
											'id'   => 'wc_settings_tab_woo_afip_facttipo',
							'default' => '11',
							'options' => array(
								'001' => 'Factura A',
								//'FA CBU INF' => 'Factura con CBU informado',
								//'NCA' => 'Nota de crédito A',
								//'NDA' => 'Nota de débito A',
								//'RA' => 'Recibo A',
								'006' => 'Factura B',
								//'FB8001' => 'Factura B a RI con informe 8001',
								//'NCB' => 'Nota de crédito B',
								//'NCB8001' => 'Nota de crédito B a RI con informe 8001',
								//'NDB' => 'Nota de débito B',
								//'RB' => 'Recibo B',
								'011' => 'Factura C',
								//'NCC' => 'Nota de crédito C',
								//'NDC' => 'Nota de débito C',
								//'RC' => 'Recibo C',
								//'FM' => 'Factura M',
								//'NCM' => 'Nota de crédito M',
								//'NDM' => 'Nota de débito M',
								//'RM' => 'Recibo M',
								//'PF' => 'Proforma'
				 			)
            ),
						'alicuotaiva' => array(
											'name' => __( 'Alícuota IVA', 'baat-afip' ),
											'type' => 'select',
											'desc' => __( 'Alícuota IVA. ', 'baat-afip' ),
											'id'   => 'wc_settings_tab_woo_afip_alicuota',
							'default' => '11',
							'options' => array(
								'nogravado' => 'No gravado',
								'exento' => 'Exento',
								'0' => '0%',
								'2.5' => '2,5%',
								'5' => '5%',
								'10.5' => '10,5%',
								'21' => '21%',
								'27' => '27%',

				 			)
            ),          
           
            'enviar_factura' => array(
                'name' => __( 'Enviar link de la factura', 'baat-afip' ),
                'type' => 'checkbox',
                'desc' => __( 'Enviar link de la factura por mail, una vez generada.', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_enviar_factura'
            ),
            'enviar_factura_texto' => array(
                'name' => __( 'Texto del link para factura', 'baat-afip' ),
                'type' => 'text',
                'desc' => __( 'Texto a mostrar en mail cuando se envia la factura', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_factura_texto'
            ),	
            'validar_dni' => array(
                'name' => __( 'Validar DNI / CUIT en el checkout', 'baat-afip' ),
                'type' => 'checkbox',
                'desc' => __( 'Valida el cuit o dni en el checkout', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_validar_dni'
            ),
			/*
			'IVA' => array(
                'name' => __( 'IVA', 'baat-afip' ),
                'type' => 'select',
                'desc' => __( 'IVA %', 'baat-afip' ),
                'id'   => 'wc_settings_tab_woo_afip_IVA',
				'default' => '21',
				'options' => array(
					  '21' => '21 %',
					  //'10,5' => '10,5 %',
					  //'27' => '27 %',
					  //'2,5' => '2,5 %',
					  //'5' => '5 %',
					  //'0' => '0 %'
				 )
            ),*/
			/*
			'impositive_treatment' => array(
				'name' => __( 'Impositive treatment', 'baat-afip' ),
                'type' => 'select',
                'desc' => '',
                'id'   => 'wc_settings_tab_woo_afip_impositive_treatment',
				'default' => '3',
				'options' => array(
					  '3' => 'Consumidor final',
					  //'1' => 'Monotributista',
					  //'2' => 'Responsable inscripto',
					  //'4' => 'IVA exento',
					  //'5' => 'IVA no responsable'
				 )
			),*/
			/*
			'automatic' => array(
                'name' => __( 'Automatic invoicing', 'woocommerce-settings-tab-demo' ),
                'type' => 'checkbox',
                'desc' => __( 'Check if you want to send invoices automatically after payment, otherwhise you can send invoices manually once the order is in processing state.', 'woocommerce-settings-tab-demo' ),
                'id'   => 'wc_settings_tab_woo_afip_user'
            ),*/
            'sectionend' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_settings_tab_woo_afip_section_title'
            ),
            'section_contacto' => array(
											'name'     => __( 'Contacto y Soporte', 'baat-afip' ),
											'type'     => 'title',
											'desc' => 'Soporte: <a href="https://baat-webdesign.com/contacto">https://baat-webdesign.com/contacto</a> <br>',
											'id'       => 'wc_settings_tab_woo_afip_section_contacto'
 						),
						'sectionend_contacto' => array(
											 'type' => 'sectionend',
											 'id' => 'wc_settings_tab_woo_afip_section_contacto'
 						),	
					
				) );
				
			}
			
			/**
			 * Filter afip Settings
			 *
			 * @since 1.0.0
			 * @param array $settings Array of the plugin settings
			 */
			return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
			
		}
		
		
		/**
		 * output_afip the settings
		 *
		 * @since 1.0
		 */
		public function output() {
		
			global $current_section;
			
			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );
		}
		
		
		/**
	 	 * Save_afip settings
	 	 *
	 	 * @since 1.0
		 */
		public function save() {
		
			global $current_section;
			
			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::save_fields( $settings );
		}
	}
	
	return new Baat_Afip_Admin();
}

add_filter( 'woocommerce_get_settings_pages', 'baat_afip_add_settings', 15 );

endif;