<?php
  $validar = get_option('wc_settings_tab_woo_afip_validar_dni');
  if($validar == 'yes'){
     add_action( 'woocommerce_after_checkout_form', 'baat_afip_jscript_checkout');
  }

  function baat_afip_jscript_checkout() { ?>
    <div id="cuis"></div>
    <script type="text/javascript">
      jQuery(document).ready(function () {  
        
        if (jQuery('#billing_country').val() == 'AR'){
          jQuery('#billing_dni_afip').show();
          jQuery('#billing_dni_afip_field').show();
          jQuery('#billing_dni_afip').val('');
        } else {
          jQuery('#billing_dni_afip').hide();
          jQuery('#billing_dni_afip').val('00000000000');
          jQuery('#billing_dni_afip_field').hide();          
        }

        jQuery('#billing_country').on('change',function() {
          if (jQuery('#billing_country').val() == 'AR'){
            jQuery('#billing_dni_afip').show(); 
            jQuery('#billing_dni_afip_field').show();
            jQuery('#billing_dni_afip').val('');
          } else {
            jQuery('#billing_dni_afip').hide();
            jQuery('#billing_dni_afip').val('00000000000');
            jQuery('#billing_dni_afip_field').hide();
          }
        })
  							 		
        jQuery('#billing_dni_afip').focusout(function () {
          jQuery( ".wait" ).remove();
          jQuery("#billing_dni_afip").after(' <span class="wait" style="color:red;">- Validando, aguarde un segundo por favor..</span>');
          jQuery( "#billing_dni_afip" ).prop( "disabled", true );
				    	jQuery.ajax({
				    		type: 'POST',
                dataType: "json",
				    		cache: false,
				    		url: wc_checkout_params.ajax_url,
				    		data: {
 									action: 'baat_check_cuit',
									cuit: jQuery('#billing_dni_afip').val(),							
				    		},
				    		success: function(data, textStatus, XMLHttpRequest){
                    jQuery( "#billing_dni_afip" ).prop( "disabled", false );
                    jQuery( ".wait" ).remove();
                    jQuery("#billing_dni_afip").after(' <span class="wait" style="color:green;">- Datos validados!</span>');
                    jQuery('#billing_first_name').val(data["nombre"]);
                    jQuery('#billing_last_name').val(data["apellido"]);
                    jQuery('#billing_company').val(data["empresa"]);
                    jQuery('#billing_address_1').val(data["billing_address_1"]);
                    jQuery('#billing_city').val(data["billing_city"]);
                    jQuery('#billing_state').val(data["billing_state"]);
                    jQuery('#select2-billing_state-container').html(data["billing_state_text"]);
                    jQuery('#billing_postcode').val(data["billing_postcode"]);
  									
                },
                error: function(MLHttpRequest, textStatus, errorThrown){
                      alert(errorThrown);
                }
									});
				    	return false;		
							 
				    });		
					
				});

				  
				
			</script>
<?php  }