<?php
/*
Plugin Name: QSynced
Description: Qsynced restores confidence in your consumers by offering security in the products they want. 
Plugin URI: https://qsynced.com/
Author: AlexCD2000
Author URI: http://alexcd2000.com
Version: 1.2.2
License: GPL v3

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//ini_set('display_errors', 1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);//Activa ver warnings
//error_reporting(0);//Desactiva warnings, cuenta como echo after headers sent
if ( ! defined('ABSPATH') ){ echo 'Lero lero no puedes ver nada...'; die;}/*esto es por seguridad por si alguien intenta acceder remotamente mata todo*/

class QSyncedPlugin{
    function __construct(){
    }
    
    function activate(){
        // Display the admin notification
        qsynced_fx_admin_notice_example_activation_hook();//función fuera de class
        //Create DB tables
        // Esto crea la tabla de sql si es que no existe...
        global $wpdb;
        $prefix= $wpdb->prefix;
        $tabla = $prefix.'qsynced';
        $sql="CREATE TABLE IF NOT EXISTS ".$tabla." (
          id int not null PRIMARY KEY AUTO_INCREMENT,
          creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

          meta text,
          value text,
          value2 text,
          value3 text,
          value4 text
        );";
        $wpdb->query( $sql );
        if ( !$wpdb->get_results( 'SELECT * FROM '.$tabla.' WHERE meta="email";' ) ) {
        $wpdb->query( "INSERT INTO ".$tabla." (meta,value) VALUES ('email','');" );
        $wpdb->query( "INSERT INTO ".$tabla." (meta,value) VALUES ('adminemail','');" );
        $wpdb->query( "INSERT INTO ".$tabla." (meta,value) VALUES ('colortemp','');" );
        $wpdb->query( "INSERT INTO ".$tabla." (meta,value) VALUES ('outoftemp','');" );
        $wpdb->query( "INSERT INTO ".$tabla." (meta,value) VALUES ('outofsend','');" );
        $wpdb->query( "INSERT INTO ".$tabla." (meta,value) VALUES ('backintemp','');" );
        $wpdb->query( "INSERT INTO ".$tabla." (meta,value) VALUES ('backinsend','');" );
        $wpdb->query( "INSERT INTO ".$tabla." (meta,value) VALUES ('soontemp','');" );
        $wpdb->query( "INSERT INTO ".$tabla." (meta,value) VALUES ('soonsend','');" );
        }
    }
}

if( class_exists("QSyncedPlugin") ){
    $QSynced = new QSyncedPlugin();
    if( isset($QSynced) ){
        // activation
        register_activation_hook( __FILE__, array($QSynced, 'activate') );
        // deactivate
        // nothing changes, table and data remains. Code dont run cause it is deactivated...?
        // uninstall
        // executes uninstall.php, maybe there run survey...?
    }
}

//Admin notification for thanks for installing
function qsynced_fx_admin_notice_example_activation_hook() {
    set_transient( 'qsynced_fx-admin-notice-example', true, 5 );//esto hace que se muestre
}
add_action( 'admin_notices', 'qsynced_fx_admin_notice_example_notice' );
function qsynced_fx_admin_notice_example_notice(){ /* Check transient, if available display notice */
    if( get_transient( 'qsynced_fx-admin-notice-example' ) ){ ?>
        <div class="updated notice is-dismissible">
            <p>Thank you for using QSynced! <strong>You are awesome</strong>.</p>
        </div>
        <?php /* Delete transient, only display this notice once. */
        delete_transient( 'qsynced_fx-admin-notice-example' );
    }
}

add_action("admin_menu", "qsynced_addMenu");//esto es lo que se ejecuta al cargar el panel de wordpress
function qsynced_addMenu(){    
//titulo pagina (arriba en el tab name), titulo panel, manage_options, el slug, la función de echo, icono panel, null o 25 abajo de comments
//https://wordpress.stackexchange.com/questions/276230/how-can-i-control-the-position-in-the-admin-menu-of-items-added-by-plugins aquí hay una lista
    add_menu_page("QSynced", "QSynced", 'manage_options', "qsynced", "qsynced_echoIndex" , "dashicons-chart-area" ,25);    
//add submenu $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function    
    add_submenu_page('qsynced','E-mails', 'E-mails',  'manage_options', "qsynced-emails", 'qsynced_echoMail'); 
    add_submenu_page('qsynced','Settings', 'Settings',  'manage_options', "qsynced-settings", 'qsynced_echoSettings'); 
}
function qsynced_echoIndex(){
require_once plugin_dir_path(__FILE__) . '/home.php'; 
}
function qsynced_echoMail(){
require_once plugin_dir_path(__FILE__) . '/mails.php'; 
}
function qsynced_echoSettings(){
require_once plugin_dir_path(__FILE__) . '/settings.php'; 
}




//This is the plugin AJAX


//add JS to footer
add_action( 'admin_footer', 'qsynced_action_javascript' ); // Write our JS below here
function qsynced_action_javascript() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

        function qsynced_salvarajax(nom,val,cual) {
		var data = {
			'action': 'qsynced_action',
                nom: nom,
                val: val,
                cual: cual
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			//alert('Got this from the server: ' + response);
            eval(response);
		});
            
        }
        $( "button, input:not([type=number])" ).click(function() {
            let nom=$(this).attr("name");
            let cual=$(this).val();
            let val=$(this).val();                
			if (typeof nom != "undefined") {//para que no interfiera en otras pestañas del WP
				if(nom.indexOf("_") >= 0 ){
					cual=nom.substr(nom.indexOf("_") + 1);//trae después de _
					nom=nom.substr(0,nom.indexOf("_") );//antes de
				}
				if(nom == "save"){
					val=$("input[name=add_"+cual+"]").val()*1;
				}
				//alert( nom+":"+val+":"+cual );
				qsynced_salvarajax(nom,val,cual);
			}
        });
    
    });
        
	</script> 


<?php
}


//the main Ajax function
add_action( 'wp_ajax_qsynced_action', 'qsynced_action' );
function qsynced_action() {

if(isset($_POST['nom']) ){

//require_once($_SERVER['DOCUMENT_ROOT']."/wp-load.php");//mala practica usar $_SERVER, así que me mando la url desde ajax con func de WP
//require_once filter_var ( $_POST['load'], FILTER_SANITIZE_URL);//esto activa las funciones de wp y woocom
    
global $wpdb;
$prefix= $wpdb->prefix;
    
    $prod=get_the_title(sanitize_text_field($_POST['cual']));
    if(sanitize_text_field($_POST["nom"]) == 'save'){
        wc_update_product_stock(sanitize_text_field($_POST['cual']), sanitize_text_field($_POST["val"]) , 'increase', false);
        //id,qty, increase-decrease o set, false para que sea inmediato        

        //echo nuevos stocks
        $price=get_post_meta( sanitize_text_field($_POST['cual']), '_price', true);
        $qty=get_post_meta( sanitize_text_field($_POST['cual']), '_stock', true);
        if($qty >= 0){
            echo '
            $("input[name=add_'.esc_html($_POST['cual']).']").val("");
            $("#Q'.esc_html($_POST['cual']).'").text("'.$qty.'");
            $("#S'.esc_html($_POST['cual']).'").text("'.number_format($qty*$price,2).'");
            $("#O'.esc_html($_POST['cual']).'").text("0");
            $("#B'.esc_html($_POST['cual']).'").text("'.number_format(0,2).'");
            ';

            //si activado envia email al replenish pero no hace entregas parciales, todos o ninguno...
            $sql2="SELECT * FROM ".$prefix."qsynced WHERE meta='backinsend' ORDER BY creacion DESC LIMIT 1;"; 
            $result = $wpdb->get_results( $sql2, OBJECT );
            $enviados="";
            if($result[0]->value == 'on'){
                $response = qsynced_checkbackorder(sanitize_text_field($_POST['cual']),$wpdb,$prefix);
                //echo $response['msg'];
                $i=0; foreach( $response['arrBack'] as $key=>$feach){  
                    if(isset($feach['prod']) ){
                        //$subject=$feach['backorder'];
                        $template=qsynced_mailtemplate('backintemp',$feach['order_id'],$feach['name'],$feach['prod'],$wpdb,$prefix);
                        //echo "$('#home-ajax').html(`* ".$template["temp"]."<br><br>`);";
                        qsynced_mailsend($feach['mail'],$template["subj"],$template["temp"]);
                        if(!empty($template["adminemail"]) ){
                        qsynced_mailsend($template["adminemail"],"ADMIN: ".$feach['prod']." is back in stock for order ".$feach['order_id'].", units: ".$feach['backorder'],$template["temp"]);
                        }
                        $enviados="* And emails were sent to costumers.<br>";
                        
                        //elimina los backorders de las ordenes pendientes
                        $sql = "UPDATE ".$prefix."woocommerce_order_itemmeta SET meta_value='0' WHERE meta_key='Backordered' AND order_item_id=%s ;";
                        $sql= $wpdb->prepare( $sql, $feach['order_item_id'] );
                        $wpdb->query(  );    
                    }
                $i++;}            
            }
            

            echo '$("#home-ajax").html("* '.esc_html($_POST['val']).' units were added to '.esc_html($prod).'.<br>'.esc_html($enviados).'<br>");';
        }else{
            echo '
            $("input[name=add_'.esc_html($_POST['cual']).']").val("");
            $("#Q'.esc_html($_POST['cual']).'").text("0");
            $("#S'.esc_html($_POST['cual']).'").text("'.number_format(0,2).'");
            $("#O'.esc_html($_POST['cual']).'").text("'.$qty.'");
            $("#B'.esc_html($_POST['cual']).'").text("'.number_format($qty*$price,2).'");
            ';            
            echo '$("#home-ajax").html("* '.esc_html($_POST['val']).' units were added to '.esc_html($prod).'.<br>* Still in backorder.<br><br>");';
        }        
    }
    
    if($_POST["nom"] == 'pol'){
        //actualiza la politica segun la bolita...
        update_post_meta( sanitize_text_field($_POST['cual']), '_backorders', sanitize_text_field($_POST['val']) );        
        echo '$("#home-ajax").html("* backorder policy updated for '.esc_html($prod).'<br><br>");';
    }
    
    if($_POST["nom"] == 'soon'){
        $response = qsynced_checkbackorder(sanitize_text_field($_POST['cual']),$wpdb,$prefix);
        echo filter_var ($response['msg']);
        $i=0; foreach( $response['arrBack'] as $key=>$feach){  
            if(isset($feach['prod']) ){
                //$subject=$feach['backorder'];
                $template=qsynced_mailtemplate('soontemp',$feach['order_id'],$feach['name'],$feach['prod'],$wpdb,$prefix);
                //echo "$('#home-ajax').html(`* ".$template["temp"]."<br><br>`);";
                qsynced_mailsend($feach['mail'],$template["subj"],$template["temp"]);
            }
        $i++;}
    }
    if($_POST["nom"] == 'back'){
        $response = qsynced_checkbackorder(sanitize_text_field($_POST['cual']),$wpdb,$prefix);
        echo filter_var ($response['msg']);
        $i=0; foreach( $response['arrBack'] as $key=>$feach){  
            if(isset($feach['prod']) ){
                //$subject=$feach['backorder'];
                $template=qsynced_mailtemplate('backintemp',$feach['order_id'],$feach['name'],$feach['prod'],$wpdb,$prefix);
                //echo "$('#home-ajax').html(`* ".$template["temp"]."<br><br>`);";
                qsynced_mailsend($feach['mail'],$template["subj"],$template["temp"]);
                if(!empty($template["adminemail"]) ){
                qsynced_mailsend($template["adminemail"],"ADMIN: ".$feach['prod']." is back in stock for order ".$feach['order_id'].", units: ".$feach['backorder'],$template["temp"]);
                }
            }
        $i++;}
    }
    if($_POST["nom"] == 'out'){
        $response = qsynced_checkbackorder(sanitize_text_field($_POST['cual']),$wpdb,$prefix);
        echo filter_var ($response['msg']);
        $i=0; foreach( $response['arrBack'] as $key=>$feach){  
            if(isset($feach['prod']) ){
                //$subject=$feach['backorder'];
                $template=qsynced_mailtemplate('outoftemp',$feach['order_id'],$feach['name'],$feach['prod'],$wpdb,$prefix);
                //echo "$('#home-ajax').html(`* ".$template["temp"]."<br><br>`);";
                qsynced_mailsend($feach['mail'],$template["subj"],$template["temp"]);
            }
        $i++;}
    }
    
}

	wp_die(); // this is required to terminate immediately and return a proper response
}


function qsynced_checkbackorder($cual,$wpdb,$prefix){
    $prod=get_the_title($cual);
    //trae todas las ordenes...
    $sql='SELECT * FROM '.$prefix.'woocommerce_order_itemmeta WHERE meta_key="Backordered" AND meta_value>0;';
    $result = $wpdb->get_results( $sql, ARRAY_A );
    if(!empty($result)){
        $output="";
        $arrBack=array();
        $i=0; foreach( $result as $key=>$feach){  
            $order_item_id=$feach["order_item_id"];
            $backorder=$feach["meta_value"];

            $sql2='SELECT * FROM '.$prefix.'wc_order_product_lookup WHERE order_item_id="'.$order_item_id.'" AND product_id="'.$cual.'" ;';
            $result2 = $wpdb->get_results( $sql2, ARRAY_A );
            if(!empty($result2)){
                $order_id=$result2[0]['order_id'];
                $sql3='SELECT * FROM '.$prefix.'postmeta WHERE meta_key="_billing_email" AND post_id="'.$order_id.'" ;';
                $result3 = $wpdb->get_results( $sql3, ARRAY_A );
                if(!empty($result3)){
                    $mail=$result3[0]['meta_value'];                        
                }else{
                    $mail="No email found";
                }
                $sql3='SELECT * FROM '.$prefix.'postmeta WHERE meta_key="_billing_first_name" AND post_id="'.$result2[0]['order_id'].'" ;';
                $result3 = $wpdb->get_results( $sql3, ARRAY_A );
                if(!empty($result3)){
                    $name=$result3[0]['meta_value'];                        
                }else{
                    $name="No name found";
                }
                $arrBack[]=array('prod'=>$prod,'backorder'=>$backorder,'order_id'=>$order_id,'name'=>$name,'mail'=>$mail,'order_item_id'=>$order_item_id); 
                $output.='- '.$prod.' has a backorder of '.$backorder.' for order #'.$order_id." (".$name.' - '.$mail.' - sent )<br>';
            }
        $i++;}
        if($output == ''){$output.='* '.$prod.' is in order<br>';}
        return array('msg'=>'$("#home-ajax").html("'.$output.'<br>");','arrBack'=>$arrBack);
    }else{
        return array('msg'=>'$("#home-ajax").html("* No orders with pending backorder<br><br>");','arrBack'=>$arrBack);
    }    
}

//send email
function qsynced_mailsend($to,$subject,$msg){
    $headers = array('Content-Type: text/html; charset=UTF-8'); 
    wp_mail( $to, $subject, $msg, $headers );
}

//Create email template
function qsynced_mailtemplate($rowname,$order,$costumer,$product,$wpdb,$prefix){
//if(isset($_POST['outof']) or isset($_POST['backin']) or isset($_POST['soon']) ){
    $sql="SELECT * FROM ".$prefix."qsynced WHERE meta='".$rowname."' ORDER BY creacion DESC LIMIT 1;"; 
    $result = $wpdb->get_results( $sql, OBJECT );
    $subject=$result[0]->value;
    $title=$result[0]->value2;
    $greeting=$result[0]->value3;
    $message=$result[0]->value4;
    //echo "...<pre>";var_dump($result);echo "</pre>...";echo $message;

    $sql2="SELECT * FROM ".$prefix."qsynced WHERE meta='colortemp' ORDER BY creacion DESC LIMIT 1;"; 
    $result = $wpdb->get_results( $sql2, OBJECT );
    $color=$result[0]->value;
    //echo "...<pre>";var_dump($result);echo "</pre>...";echo $color;
  
    $sql2="SELECT * FROM ".$prefix."qsynced WHERE meta='adminemail' ORDER BY creacion DESC LIMIT 1;"; 
    $result = $wpdb->get_results( $sql2, OBJECT );
    $adminemail=$result[0]->value;
    //echo "...<pre>";var_dump($result);echo "</pre>...";echo $color;

    //$order="#ORDER";
    //$costumer="COSTUMER-NAME";
    //$product="PRODUCT-NAME";

    $template= file_get_contents(plugin_dir_path(__FILE__) . "/mail-template.php");

    $template= str_replace("GREEN", $color, $template);
    $template= str_replace("TITLE", $title, $template);
    $template= str_replace("GREETING", $greeting, $template);
    $template= str_replace("MESSAGE", $message, $template);
    $template= str_replace("{order}", $order, $template);
    $template= str_replace("{costumer}", $costumer, $template);
    $template= str_replace("{product}", $product, $template);

    $subject= str_replace("{order}", $order, $subject);
    $subject= str_replace("{costumer}", $costumer, $subject);
    $subject= str_replace("{product}", $product, $subject);
    return array("temp"=>$template,"subj"=>$subject,"adminemail"=>$adminemail);
}

/*los productos se guardan en 

los radio button deben guardarse con AJAX... el input agrega mas unidades menos backorder, y envia mail "ya va en camino" si esta configurado. 




lo que NO SE es si mandar info de regreso o solo bloquear si no esta activa... puede ser mas facil hacer todo en el plugin y luego que pague mas y alla clientes, hacer lo pro... si es que pega...
TAMBIEN como el plugin activado corre siempre, solo que este monitoreando la ultima orden (guarda la ultima orden o cambios de stock en la tabla) y que corra si cambia algo...




SELECT * FROM `qsynced_posts` WHERE post_type="product"
y sirve ID, post_content post_title y post_status = draft o publish

en meta esta
SELECT * FROM `qsynced_postmeta` WHERE post_id=18
y sirve


SELECT term_taxonomy_id FROM `qsynced_term_relationships` WHERE object_id= ID te dice que producto tiene que categoria tag o attr en 
SELECT * FROM `qsynced_terms`

SELECT * FROM `qsynced_woocommerce_attribute_taxonomies` se guardan los atributos que crea el usuario
SELECT * FROM `qsynced_wc_product_meta_lookup` //aqui se guardan los sku


Que hace SELECT * FROM `qsynced_wc_reserved_stock` ??


Me faltaria orden y los datos de cliente para saber a quien avisarle...??
SELECT * FROM `qsynced_wc_order_product_lookup`, es el mejor, trae cada orden el número de pedido y cuanto se pidio

SELECT * FROM `qsynced_wc_product_meta_lookup` trae el stock y el backorder...

SELECT * FROM `qsynced_wc_customer_lookup` trae los datos del cliente, los sacas con la orden...

SELECT * FROM `qsynced_posts` WHERE post_type="shop_order" guarda las ordenes...
SELECT * FROM `qsynced_postmeta` WHERE post_id=22 en where meta_key es
_billing_first_name _billing_last_name _billing_email


Ya no requieres:
SELECT * FROM `qsynced_wc_order_stats` trae las ordenes y status
SELECT * FROM `qsynced_woocommerce_order_items` trae el contenido de cada orden
y la cantidad en SELECT * FROM `qsynced_woocommerce_order_itemmeta`


/**/