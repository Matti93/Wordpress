<?php
add_action( 'woocommerce_thankyou', 'to_complete' );
if (!function_exists('to_complete')){ 
    function to_complete( $order_id ) { 
        if ( ! $order_id ) {
            return;
        }

        $order = wc_get_order( $order_id );
        if( $order->has_status( 'processing' ) ){
            $order->update_status( 'completed' );
        }
    }
}


add_action( 'woocommerce_order_status_processing', 'modified_to_complete', 10, 1 );
    function modified_to_complete( $order_id ) {
        if( $order->has_status( 'processing' ) ) 
        {
            $order->update_status( 'completed' );
        }
    }

add_action( 'woocommerce_update_order', 'action_woocommerce_update_order', 10, 1 ); 
function action_woocommerce_update_order( $order_get_id ) { 
    $order = wc_get_order( $order_get_id );
    if( $order->has_status( 'processing' ) ) 
    {
        $order->update_status( 'completed' );
    }
}; 