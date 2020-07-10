<?php 
$Texto;

function getTextLabel(){
    global $wpdb;
    global $Texto; 
    $tableName= $wpdb->prefix . 'gfs_data';
    $Texto = $wpdb->get_results("SELECT * FROM $tableName LIMIT 1");
}



function showView(){
    require_once plugin_dir_path(__FILE__) . 'services.php';
    $free_shipping_settings = get_option( 'woocommerce_free_shipping_settings' );
    $min_amount = $free_shipping_settings['min_amount'];
    global $Texto;
    echo '  <form name="form" action="'. saveData(),getTextLabel() . '" method="post">
                <div style="text-align:center">
                    <h1>Welcome to Get Free Shipment</h1>
                </div>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <label for="texto">Text to show for the client</label>
                                <span class="help-tip"></span>
                            </th>
                        <td> 
                            <input type="text" maxLength="65" id="texto" name="texto" value="' . $Texto[0]->texto .'" class="regular-text">
                            <div>
                            <span> If you want to add the price in the middle of a text add &value& where the indicated value should go, otherwise the value will go to the end of the sentence.</span>
                            </div>
                        </td>
                        </tr>
                    </tbody>
                </table>

                <input type="submit" name="b1" class="button action" value="Aplicar">
            </form>';
}


getTextLabel();
showView();
?>
<!-- HTML to write -->
