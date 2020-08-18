
<?php 

add_action('save_post_product', 'mp_sync_on_product_save', 10, 3);
function mp_sync_on_product_save( $post_id, $post, $update ) {
    $product = wc_get_product( $post_id );
    // do something with this product
}