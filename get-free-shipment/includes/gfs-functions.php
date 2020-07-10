<?php
/*
 * Add my new menu to the Admin Control Panel
 */
 
// Hook the 'admin_menu' action hook, run the function named 'mfp_Add_My_Admin_Link()'
$postal_code;

add_action( 'admin_menu', 'mfp_Add_My_Admin_Link' );
 
// Add a new top level menu link to the ACP
function mfp_Add_My_Admin_Link()
{
      add_menu_page(
        'GFSA', // Title of the page
        'GFSA', // Text to show on the menu link
        'manage_options', // Capability requirement to see the link
        plugin_dir_path(__FILE__) . '/gfs-first-acp-page.php' // The 'slug' - file to display when clicking the link
    );
}

function getPostalCode(){
  global $postal_code;
  $postal_code = WC()->customer->get_shipping_postcode();
}


function free_shipping_cart_notice() { 
  global $woocommerce; 
  global $postal_code;
  require_once plugin_dir_path(__FILE__) . '/services.php';
  $default_zone = new WC_Shipping_Zone(0); 
  $default_methods = $default_zone->get_shipping_methods();
    foreach( $default_methods as $key => $value ) {
      if ( $value->id === "free_shipping" ) {
        if ( $value->min_amount > 0 ) $min_amounts[] = $value->min_amount;
      }
    }
    
    $delivery_zones = WC_Shipping_Zones::get_zones();
    foreach ( $delivery_zones as $key => $delivery_zone ) {
      $object = new stdClass();
      $object->zone = $delivery_zone["formatted_zone_location"];
      foreach ( $delivery_zone['shipping_methods'] as $key => $value ) {
        if ( $value->id === "free_shipping" ) {
          if ( $value->min_amount > 0 ) {
          $object->amount = $value->min_amount;
            $min_amounts[] = $object;
          }
        }
      }
    }

    $postal_code = WC()->customer->get_shipping_postcode();
    foreach ( $min_amounts as $key => $row ) {
      $valueToCompare = $row->zone;
      if(strpos($valueToCompare, $postal_code) !== false){
        $amountToCheck = $row->amount;
          $current = WC()->cart->subtotal;
          if ( $current < $amountToCheck ) {
           $text = gfs_Get_Text();
           $sum = $amountToCheck - $current;
           if(strpos($text, '&value&') !== false){
             $sum = ' ' . $sum . ' ';
             $text = '<div class="gfs-text">' . $text . '</div>';
             $text = str_replace("&value&",'<span class="gfs-price">' . $sum . '</span>',$text);
           } else{
             $text = $text . '<span class="gfs-price"> ' . $sum . '</span>';
           }
            $notice = $text;
         wc_print_notice( $notice, 'notice' );
          }
      } 
    }
}
add_action( 'woocommerce_after_shipping_calculator', 'free_shipping_cart_notice',20 );
add_action('woocommerce_after_shipping_calculator','getPostalCode', 20);


