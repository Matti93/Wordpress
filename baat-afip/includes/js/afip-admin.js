jQuery(document).ready(function() {
    console.log('afip loaded');
    //jQuery('#wc_settings_tab_woo_afip_cuit').on('keyup',function(){
    if (document.getElementById('cuit')) {

        var charCount = jQuery('#cuit').val().replace(/\s/g, '').length;
        if (charCount == 11) {
            var element = document.getElementById("activo");
            var api_status = jQuery("#api_status").text();

            if (typeof(element) != 'undefined' && element != null) {

            } else {
                if (api_status != 'Activo - Testeo') {
                    console.log('afip insertBefore');

                    jQuery("<div id='activo' class='get_cuit active'>VALIDAR CUIT y GENERAR CERTIFICADOS</div>").insertBefore("#wc_settings_tab_woo_afip_section_certificado-description");
                }
            }

        }
    }
    //});

    jQuery('body').on('click', '.guardar_csr', function(e) {
        e.preventDefault();
        var cert_afip_respo = jQuery('#cert_afip_respo').val();
        var api_key = jQuery('#apikey').val();
        var cuit = jQuery('#cuit').val();
        var boton = jQuery('.guardar_csr');
        boton.text('ESPERE...');
        jQuery.ajax({
            type: 'POST',
            cache: false,
            url: ajaxurl,
            data: {
                action: 'baat_save_csr',
                api_key: api_key,
                cert_afip_respo: cert_afip_respo,
                cuit: cuit,
            },
            success: function(data, textStatus, XMLHttpRequest) {
                if (data == 'ok') {
                    boton.text('VALIDO!');
                    console.log(data);
                }
                if (data == 'Error') {
                    boton.text('ERROR!');
                }
                //location.reload();
            },
            error: function(MLHttpRequest, textStatus, errorThrown) {}
        });
    });

    jQuery('body').on('click', '.get_cuit', function(e) {
        e.preventDefault();
        var cuit = jQuery('#cuit').val();
        var api_key = jQuery('#apikey').val();

        jQuery(".form-table").fadeOut();
        //jQuery( ".active" ).remove();
        //jQuery("<div class='get_cuit active'>VALIDAR CUIT y GENERAR CERTIFICADOS</div>").insertAfter("#wc_settings_tab_woo_afip_cuit");
        var boton = jQuery('.active');
        boton.text('ESPERE...');
        jQuery.ajax({
            type: 'POST',
            cache: false,
            url: ajaxurl,
            data: {
                action: 'baat_get_cuit',
                api_key: api_key,
                cuit: cuit,
            },
            success: function(data, textStatus, XMLHttpRequest) {
                if (data) {
                    boton.text('VALIDO!');
                    jQuery("#cert_out_code").append(data);
                    jQuery("#cert_out").fadeIn();
                    jQuery("#subir_cert").fadeIn();
                    boton.fadeOut();
                    jQuery('<textarea name="cert_afip_respo" id="cert_afip_respo" style="width:300px; height: 75px;" class="" placeholder="N/A"></textarea>').insertAfter("#subir_cert");
                    jQuery("<div class='guardar_csr'>Guardar CSR</div>").insertAfter("#cert_afip_respo");
                }
                if (data == 'Error') {
                    boton.text('ERROR!');
                    console.log(data);
                }
                //location.reload();
            },
            error: function(MLHttpRequest, textStatus, errorThrown) {}
        });
    });

    jQuery('body').on('click', '#copy_but', function(e) {
        e.preventDefault();
        var range = document.createRange();
        range.selectNode(document.getElementById("cert_out_code"));
        window.getSelection().removeAllRanges(); // clear current selection
        window.getSelection().addRange(range); // to select text
        document.execCommand("copy");
        window.getSelection().removeAllRanges(); // to deselect
    });

    jQuery('#wc_settings_tab_woo_afip_cuit').attr({ maxLength: 11 });
    jQuery("#wc_settings_tab_woo_afip_cuit").keypress(function(e) {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    });

    var invokeAfterSuccess = function(order_id) {
        var data = {
            action: 'woo_afip_do_ajax_pdf_request',
            order_id: order_id
        }
        jQuery.ajax({
                type: 'POST',
                cache: false,
                url: ajaxurl,
                data,
            })
            .done(function(data, textStatus, XMLHttpRequest) {
                jQuery('#afip_notices').html('PDF generado!');
                jQuery('#afip_notices').html('');
                var inp = data.replace(/&lt;/gi, "<").replace(/&gt;/gi, ">");
                jQuery('#afip_notices').append(inp);


            })
            .fail(function(reason) {
                // Handles errors only
                console.debug(reason);
            })
            .always(function(data, textStatus, response) {
                // If you want to manually separate stuff
                // response becomes errorThrown/reason OR jqXHR in case of success
            })
            .then(function(data, textStatus, response) {

            });
    }

    jQuery('body').on('click', '.invoice-button', function(e) {
        e.preventDefault();
        jQuery(this).hide();
        jQuery('#afip_notices').html('Enviando datos a AFIP..');
        var order_id = jQuery("#post_ID").val();
        var data = {
            action: 'woo_afip_do_ajax_request',
            order_id: order_id
        }
        jQuery.ajax({
                type: 'POST',
                cache: false,
                url: ajaxurl,
                data,
            })
            .done(function(data) {
                jQuery('#afip_notices').html('');
                var inp = data.replace(/&lt;/gi, "<").replace(/&gt;/gi, ">");
                jQuery('#afip_notices').html(inp);

            })
            .fail(function(reason) {
                // Handles errors only
                console.debug(reason);
            })
            .always(function(data, textStatus, response) {
                // If you want to manually separate stuff
                // response becomes errorThrown/reason OR jqXHR in case of success
            })
            .then(function(data, textStatus, response) {
                invokeAfterSuccess(order_id);
            });


    });

    jQuery('body').on('click', '.invoicepdf-button', function(e) {

        e.preventDefault();
        jQuery(this).hide();

        var order_id = jQuery("#post_ID").val();
        var data = {
            action: 'woo_afip_do_ajax_pdf_request',
            order_id: order_id
        }
        jQuery.ajax({
                type: 'POST',
                cache: false,
                url: ajaxurl,
                data,
            })
            .done(function(data) {
                jQuery('#afip_notices').html('PDF generado!');
                jQuery('#afip_notices').html('');
                var inp = data.replace(/&lt;/gi, "<").replace(/&gt;/gi, ">");
                jQuery('#afip_notices').append(inp);


            })
            .fail(function(reason) {
                // Handles errors only
                console.debug(reason);
            })
            .always(function(data, textStatus, response) {
                // If you want to manually separate stuff
                // response becomes errorThrown/reason OR jqXHR in case of success
            })
            .then(function(data, textStatus, response) {

            });


    });




});




function viewInvoice() {


}