(function($) {
    $(document).ready(function() {
        $(document).on('change keyup paste', '.comentario', function() {
            $('.cart_totals').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            $('#post-7 > div > div > form > table > tbody > tr:nth-child(7) > td > button').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            var cart_id = $(this).data('cart-id');
            $.ajax({
                type: 'POST',
                url: prefix_vars.ajaxurl,
                data: {
                    action: 'prefix_update_cart_notes',
                    security: $('#woocommerce-cart-nonce').val(),
                    comentarios: $('#comentario_' + cart_id).val(),
                    reemplazar: $(`#reemplazar_${cart_id}`).val(),
                    cart_id: cart_id
                },
                success: function(response) {
                    console.log(response)
                    $('.cart_totals').unblock();
                    $('#post-7 > div > div > form > table > tbody > tr:nth-child(7) > td > button').unblock();
                }
            })
        });
        $(document).on('change keyup paste', '.reemplazar', function() {
            $('.cart_totals').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            $('#post-7 > div > div > form > table > tbody > tr:nth-child(7) > td > button').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            var cart_id = $(this).data('cart-id');
            $.ajax({
                type: 'POST',
                url: prefix_vars.ajaxurl,
                data: {
                    action: 'prefix_update_cart_notes',
                    security: $('#woocommerce-cart-nonce').val(),
                    comentarios: $('#comentario_' + cart_id).val(),
                    reemplazar: $(`#reemplazar_${cart_id}`).val(),
                    cart_id: cart_id
                },
                success: function(response) {
                    console.log(response)
                    $('.cart_totals').unblock();
                    $('#post-7 > div > div > form > table > tbody > tr:nth-child(7) > td > button').unblock();
                }
            })
        });
    });
})(jQuery);