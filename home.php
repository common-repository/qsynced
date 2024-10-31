<?php
if ( ! defined('ABSPATH') ){ echo 'lero lero no puedes acceder'; die;}/*esto es por seguridad por si alguien intenta acceder remotamente*/

require_once plugin_dir_path(__FILE__) . '/css.php'; 

global $wpdb;
$prefix= $wpdb->prefix;
$tabla = $prefix.'qsynced';
$result = $wpdb->get_results( 'SELECT * FROM '.$tabla." WHERE meta='email' LIMIT 1;", OBJECT );
$email=$result[0]->value;
$status = wp_remote_retrieve_body( wp_remote_get( 'https://qsynced.com/qsynced-api.php?email='.$email ) );
$stat = json_decode($status,true);if(!isset($stat['status']) ){$stat['status']="";}

if($stat['status'] == 'DESACTIVADO es free, antes era != paying'){?>
    <div class="qsync">
        <h1>Your subscription is <span class='error'>NOT Active</span>.</h1>
        <h2>Go to <a href='/wp-admin/admin.php?page=qsynced-settings'>settings</a></h2>
    </div>
<?php }else{?>
<?php 
    //echo home_url()."/wp-load.php"; echo $_SERVER['DOCUMENT_ROOT']."/wp-load.php";
?>
    <div class="qsync left reduced">
    <h1>Stock management:</h1>
    <span id="home-ajax" class="help"></span>
    <table class="center">
        <tr>
            <th width="3%">ID</th>
            <th width="12%">Product</th>

            <th width="20%" colspan="3">
                <table><tr><th width="100%" colspan="3">Out of stock policy</th></tr>
                <tr>
                    <th width="33%" class="subth">Don't</th>
                    <th width="33%" class="subth">Allow</th>
                    <th width="33%" class="subth">Notify</th>
                </tr></table>
            </th>
            <th width="35%" colspan="4">
                <table><tr><th width="100%" colspan="4">Stock</th></tr>
                <tr>
                    <th width="23%" class="subth">Price</th>
                    <th width="23%" class="subth">Current</th>
                    <th width="23%" class="subth">Out of</th>
                    <th width="40%" class="subth">Replenish</th>
                </tr></table>
            </th>
            <th width="30%" colspan="3">Send emails
            </th>            
        </tr>
<?php 
$output="";
    if($stat['status'] != 'paying'){
$html='
        <tr class="rowproduct">
            <td>ID</td>
            <td>PROD</td>
            <td><input type="radio" name="pol_ID" value="no"></td>
            <td><input type="radio" name="pol_ID" value="yes"></td>
            <td><input type="radio" name="pol_ID" value="notify"></td>
            <td width="8%">$PRICE</td>
            <td width="8%"><span id="QID">QTY</span> ($<span id="SID">STOCK</span>)</td>
            <td width="8%"><span id="OID">OUT</span> ($<span id="BID">BACK</span>)</td>
            <td> <input type="number" name="add_ID"><button name="save" value="ID">Add</button></td>
            <td colspan="3">Premium feature</td>
        </tr>
';        
    }else{
$html='
        <tr class="rowproduct">
            <td>ID</td>
            <td>PROD</td>
            <td><input type="radio" name="pol_ID" value="no"></td>
            <td><input type="radio" name="pol_ID" value="yes"></td>
            <td><input type="radio" name="pol_ID" value="notify"></td>
            <td width="8%">$PRICE</td>
            <td width="8%"><span id="QID">QTY</span> ($<span id="SID">STOCK</span>)</td>
            <td width="8%"><span id="OID">OUT</span> ($<span id="BID">BACK</span>)</td>
            <td> <input type="number" name="add_ID"><button name="save" value="ID">Add</button></td>
            <td><button name="soon" value="ID">Back soon</button></td>
            <td><button name="out"  value="ID">Out of stock</button></td>
            <td><button name="back" value="ID">Back in stock</button></td>
        </tr>
';
    }
    $result = $wpdb->get_results( 'SELECT * FROM '.$prefix.'posts WHERE post_type="product"; ', ARRAY_A );
    if(!empty($result)){
        $i=0; foreach( $result as $key=>$feach){  
            $id=$feach["ID"];
            $prod=$feach["post_title"];
            
            $price=get_post_meta( $id, '_price', true);
            $qty=get_post_meta( $id, '_stock', true);
            if($qty < 0){$out=$qty;$qty=0;}else{$out=0;}
            
            $preoutput=str_replace("ID", $id, $html);
            $preoutput=str_replace("PROD", $prod, $preoutput);
            $preoutput=str_replace("PRICE", number_format($price,2), $preoutput);
            $preoutput=str_replace("QTY", $qty, $preoutput);
            $preoutput=str_replace("STOCK", number_format($price*$qty,2), $preoutput);
            $preoutput=str_replace("OUT", $out, $preoutput);
            $preoutput=str_replace("BACK", number_format($price*$out,2), $preoutput);

            $back=get_post_meta( $id, '_backorders', true);
            $output.=str_replace('value="'.$back.'"', 'value="'.$back.'" checked ', $preoutput);                  
        $i++;}
    }else{
        $output.='
        <tr class="rowproduct">
            <td colspan="12">Add some products to your WooCommerce first</td>
        </tr>
        ';        
    }
    echo filter_var($output);    
?>
    </table>
</div>

<?php }