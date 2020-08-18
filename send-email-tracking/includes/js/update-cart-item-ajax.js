jQuery(document).ready(function($) {

    $(document).on('click', '.email_trigger', function(e) {

        // Adding the script tag to the head as suggested before
        var head = document.head;
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js";
        head.appendChild(script);
        script.type = 'text/javascript';
        script.src = "https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js";
        head.appendChild(script);
        // e.preventDefault();
        $('#wpbody').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
        var orderId = $(this).data('orderid');
        var mail = $(this).data('mail');
        var orderTrackId = null;
        $.ajax({
            type: 'POST',
            url: prefix_vars.ajaxurl,
            data: {
                action: 'prefix_update_cart_notes',
                orderId: orderId,
                orderTrackId,
                mail: mail,
            },
            success: function(response) {
                alert('MENSAJE ENVIADO !');
                $('#wpbody').unblock();
            }
        })
    });
    $(document).on('click', '.email_trigger-tracking', function(e) {
        $('#wpbody').block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        });
        var orderId = $(this).data('orderid');
        var mail = $(this).data('mail');
        var orderTrackId = $('#orderTrackId').val();
        if (orderTrackId === '') {
            alert('El campo para ingresar código de trackeo no puede estar vacio');
            $('#wpbody').unblock();
        }
        $.ajax({
            type: 'POST',
            url: prefix_vars.ajaxurl,
            data: {
                action: 'prefix_update_cart_notes',
                orderId: orderId,
                orderTrackId,
                mail: mail,
            },
            success: function(response) {
                alert('MENSAJE ENVIADO !');
                $('#orderTrackId').val('');
                $('#wpbody').unblock();
            }
        })
    });
});