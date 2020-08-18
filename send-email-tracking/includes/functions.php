<?php


//  /**
//   * Enqueue our JS file
//   */
function prefix_enqueue_scripts() {
  wp_register_script( 'prefix-script', trailingslashit( plugin_dir_url( __FILE__ ) ) . 'js/update-cart-item-ajax.js', array( 'jquery' ), time(), true );
  wp_localize_script(
  'prefix-script',
  'prefix_vars',
  array(
  'ajaxurl' => admin_url( 'admin-ajax.php' )
  )
  );
  wp_enqueue_script( 'prefix-script' );
 }
 add_action( 'admin_enqueue_scripts', 'prefix_enqueue_scripts' );

 /**
 * Send email
 */
function prefix_update_cart_notes() {
  $orderId = $_POST['orderId'];
  $mail = $_POST['mail'];
  $orderTrackId = $_POST['orderTrackId'];
  $to = $mail;
  if($orderTrackId == null){
    $html = '<td align="center" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#f5f5f5;border:0px solid #dcdcdc;border-radius:3px"><tbody><tr><td align="center" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#fff;color:#000000;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:3px 3px 0 0"><tbody><tr><td><p style="margin-top:20px"><img src="https://ar.delcerropatagonia.com/wp-content/uploads/2019/10/logo2.jpg" alt="Del Cerro" style="border:none;display:inline-block;font-size:14px;font-weight:bold;height:auto;outline:none;text-decoration:none;text-transform:capitalize;vertical-align:middle;margin-right:10px;width:120px;padding-left:20px"></p></td></tr></tbody></table></td></tr><tr><td align="center" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#fff;color:#000000;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:3px 3px 0 0"><tbody><tr><td style="padding:40px 48px 0 48px;display:block"><h1 style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:30px;line-height:150%;margin:0;text-align:left;text-shadow:0 1px 0 #7797b4;color:#000000;font-weight:bold">Gracias por tu compra!!</h1></td></tr></tbody></table></td></tr><tr><td align="center" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="600"><tbody><tr><td valign="top" style="background-color:#fff"><table border="0" cellpadding="20" cellspacing="0" width="100%"><tbody><tr><td valign="top" style="padding:48px 48px 32px"><div style="color:#000000;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left"><p style="margin:0 0 16px">Hola ,</p><p style="margin:0 0 16px">Te queríamos contar que tu pago se registró exitosamente! Estamos preparando tu pedido y cuando esté listo para ser retirado por el correo, te vamos a enviar otro mail, con tu número de seguimiento.¡Agradecemos tu confianza!.</p><p style="margin:0 0 16px">¡Agradecemos tu confianza!</p></div></td></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table></td>';
    $subject = 'Confirmación de pedido #' . $orderId;
    $body = $html;
  }else{
    $html = '<td align="center" valign="top">
						
    <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#f5f5f5;border:0px solid #dcdcdc;border-radius:3px">
      <tbody><tr>
        <td align="center" valign="top">
          
          <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#fff;color:#000000;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:3px 3px 0 0">
            <tbody><tr>
              <td>
                <p style="margin-top:20px"><img src="https://ar.delcerropatagonia.com/wp-content/uploads/2019/10/logo2.jpg" alt="Del Cerro" style="border:none;display:inline-block;font-size:14px;font-weight:bold;height:auto;outline:none;text-decoration:none;text-transform:capitalize;vertical-align:middle;margin-right:10px;width:120px;padding-left:20px"></p>											</td>
            </tr>
          </tbody></table>
          
        </td>
      </tr>
      <tr>
        <td align="center" valign="top">
          
          <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#fff;color:#000000;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:3px 3px 0 0">
            <tbody><tr>
              <td style="padding:40px 48px 0 48px;display:block">
                <h1 style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:30px;line-height:150%;margin:0;text-align:left;text-shadow:0 1px 0 #7797b4;color:#000000;font-weight:bold">¡Hola! ¡Tu pedido ya está en camino!</h1>
                <h1 style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:30px;line-height:150%;margin:0;text-align:left;text-shadow:0 1px 0 #7797b4;color:#000000;font-weight:bold">El número de seguimiento de Correo Argentino es '.$orderTrackId.'</h1>
              </td>
            </tr>
          </tbody></table>
          
        </td>
      </tr>
      <tr>
        <td align="center" valign="top">
          
          <table border="0" cellpadding="0" cellspacing="0" width="600">
            <tbody><tr>
              <td valign="top" style="background-color:#fff">
                
                <table border="0" cellpadding="20" cellspacing="0" width="100%">
                  <tbody><tr>
                    <td valign="top" style="padding:48px 48px 32px">
                      <div style="color:#000000;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left">

<p style="margin:0 0 16px">A modo de agradecer tu confianza, te generamos un cupón de descuento para alguna futura compra que quieras hacer en nuestra tienda.
Código del descuento: gracias
</p>

<p style="margin:0 0 16px"><div><ol><li style="margin-left:15px">Recibilo siempre respetando la distancia y con las medidas de protección necesarias.</li><li style="margin-left:15px">Desinfecta&nbsp;con agua con lavandina o alcohol.<br></li><span style="color:rgb(80,0,80)"><li style="margin-left:15px">Tirá el envoltorio (no es necesario para cambio)</li></span><li style="margin-left:15px">Lávate&nbsp;las manos</li><li style="margin-left:15px">Disfrutá tu prenda<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</li></ol>¡Gracias por tu compra!</div>
          
</p>

                      </div>
                    </td>
                  </tr>
                </tbody></table>
                
              </td>
            </tr>
          </tbody></table>
          
        </td>
      </tr>
    </tbody></table>
  </td>';
    $subject = 'Confirmación de pedido Enviado.';
    $body = $html;
  }
$headers = array('Content-Type: text/html; charset=UTF-8');
 
wp_mail( $to, $subject, $body, $headers );
  exit;
 }
 add_action( 'wp_ajax_prefix_update_cart_notes', 'prefix_update_cart_notes' );





// Add a custom metabox only for shop_order post type (order edit pages)
add_action( 'add_meta_boxes', 'add_meta_boxes' );

function add_meta_boxes()
{
    add_meta_box( 
        'woocommerce-order-my-custom', 
        __( 'Enviar emails' ), 
        'order_my_custom', 
        'shop_order', 
        'side', 
        'default' 
    );
}
function order_my_custom()
{
  global $woocommerce, $post;

$order = new WC_Order($post->ID);
$order_id = trim(str_replace('#', '', $order->get_order_number()));
$mail = $order->get_billing_email();
    echo '<input class="orderTrackId"  placeholder="Ingrese Id de trackeo" id="orderTrackId"></input>';
    echo '<button class="email_trigger-tracking" type="button" id="email_trigger-tracking" data-mail="'.$mail.'" data-orderId="'.$order_id.'">Send Email</button>';
    echo '---------------';
    echo '<button class="email_trigger" type="button" id="email_trigger" data-mail="'.$mail.'" data-orderId="'.$order_id.'">Send Email</button>';
}


