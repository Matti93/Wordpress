<?php

function csp_locate_template( $template, $template_name, $template_path ) {
  $basename = basename( $template );
  if( $basename == 'cart.php' ) {
  $template = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/template/cart/cart.php';
  }
  return $template;
 }
 add_filter( 'woocommerce_locate_template', 'csp_locate_template', 10, 3 );

 
//  /**
//   * Enqueue our JS file
//   */
function prefix_enqueue_scripts() {
  wp_register_script( 'prefix-script', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/update-cart-item-ajax.js', array( 'jquery-blockui' ), time(), true );
  wp_localize_script(
  'prefix-script',
  'prefix_vars',
  array(
  'ajaxurl' => admin_url( 'admin-ajax.php' )
  )
  );
  wp_enqueue_script( 'prefix-script' );
 }
 add_action( 'wp_enqueue_scripts', 'prefix_enqueue_scripts' );

 /**
 * Update cart item notes
 */
function prefix_update_cart_notes() {
  // Do a nonce check
  if( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'woocommerce-cart' ) ) {
  wp_send_json( array( 'nonce_fail' => 1 ) );
  exit;
  }
  // Save the notes to the cart meta
  $cart = WC()->cart->cart_contents;
  $cart_id = $_POST['cart_id'];
  $notes = $_POST['comentarios'];
  $reemplazar = $_POST['reemplazar'];
  $cart_item = $cart[$cart_id];
  $cart_item['notes'] = $notes;
  $cart_item['reemplazar'] = $reemplazar;
  WC()->cart->cart_contents[$cart_id] = $cart_item;
  WC()->cart->set_session();
  wp_send_json( array( 'success' => 1 ) );
  exit;
 }
 add_action( 'wp_ajax_prefix_update_cart_notes', 'prefix_update_cart_notes' );
 
 function prefix_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
  foreach( $item as $cart_item_key=>$cart_item ) {
  if( isset( $cart_item['notes'] ) ) {
  $item->add_meta_data( 'notes', $cart_item['notes'], true );
    }
  if( isset( $cart_item['reemplazar'] ) ) {
    $item->add_meta_data( 'reemplazar', $cart_item['reemplazar'], true );
  }
  }
 }
 add_action( 'woocommerce_checkout_create_order_line_item', 'prefix_checkout_create_order_line_item', 10, 4 );


 function prefix_after_cart_item_name( $cart_item, $cart_item_key ) {
  $comentario = isset( $cart_item['notes'] ) ? $cart_item['notes'] : '';
  $reemplazar = isset( $cart_item['reemplazar'] ) ? $cart_item['reemplazar'] : 'no';
  printf(
  ' <td>
  <textarea class="%s" id="comentario_%s" data-cart-id="%s">%s</textarea>
  </td>',
  'comentario',
  $cart_item_key,
  $cart_item_key,
  $comentario
  );
if($reemplazar === 'no'){
  printf(
    ' <td>
    <select name="select" class="%s" id="reemplazar_%s" data-cart-id="%s">
    <option value="si">Reemplazar por otro similar</option> 
    <option value="no" selected>No Reemplazar</option>
  </select>
    </td>',
    'reemplazar',
    $cart_item_key,
    $cart_item_key,
    );
   }else{
    printf(
      ' <td>
      <select name="select" class="%s" id="reemplazar_%S" data-cart-id="%s">
      <option value="si" selected>Reemplazar por otro similar</option> 
      <option value="no" >No Reemplazar</option>
    </select>
      </td>',
      'reemplazar',
      $cart_item_key,
      $cart_item_key,
      );
   }
}

  // ADD THE INFORMATION AS ORDER ITEM META DATA SO THAT IT CAN BE SEEN AS PART OF THE ORDER
  function add_product_custom_field_to_order_item_meta(  $cart_item, $cart_item_key ) {
    
    // the meta-key is 'Date event' because it's going to be the label too
    if( ! empty( $POST['comentarios' ] ) )
    wc_update_order_item_meta( $cart_item, 'notes', sanitize_text_field( $POST['comentarios' ] ) );
    if( ! empty( $POST['reemplazar' ] ) )
    wc_update_order_item_meta( $cart_item, 'reemplazar', sanitize_text_field( $POST['reemplazar' ] ) );
  }

 add_filter('woocommerce_after_cart_item_name','add_product_custom_field_to_order_item_meta', 10, 2 );
 add_action( 'woocommerce_after_cart_item_name', 'prefix_after_cart_item_name', 10, 2 );

 // display the extra data in the order admin panel
function modified_order_admin_view( $item_id, $item, $product ){  
  if(!(!!$item['method_title'])){
    echo '<div><p>Reemplazar en caso de falta de stock?';
    echo '<b> ' . $item->get_meta('reemplazar') . '</b>';
    echo '</p></div>';
    $notes = $item->get_meta('notes');
    if(isset($notes)){
      echo '<div><p>Comentarios realizados sobre el producto:<b> ' . $item->get_meta('notes') . '</b></p></div>';
    }
    }
  }
add_filter( 'woocommerce_before_order_itemmeta', 'modified_order_admin_view',10,3 );

