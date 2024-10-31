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
if($stat['status'] != 'paying'){?>
<div class="qsync">
    <h1>Your subscription is <span class='error'>NOT Active</span>.</h1>
    <h2>Go to <a href='/wp-admin/admin.php?page=qsynced-settings'>settings</a></h2>
</div>
<?php }else{
?>
<div class="qsync left">
    <h1>Preview e-mail templates:</h1>    
                    <?php 
$rowname="";
if(isset($_POST['outof']) ){$rowname="outoftemp";}
if(isset($_POST['backin']) ){$rowname="backintemp";}
if(isset($_POST['soon']) ){$rowname="soontemp";}
    
if(isset($_POST['outof']) or isset($_POST['backin']) or isset($_POST['soon']) ){
    $sql="SELECT * FROM ".$tabla." WHERE meta='".$rowname."' ORDER BY creacion DESC LIMIT 1;"; 
    $result = $wpdb->get_results( $sql, OBJECT );
    $subject=$result[0]->value;
    $title=$result[0]->value2;
    $greeting=$result[0]->value3;
    $message=$result[0]->value4;
    //echo "...<pre>";var_dump($result);echo "</pre>...";echo $message;

    $sql="SELECT * FROM ".$tabla." WHERE meta='colortemp' ORDER BY creacion DESC LIMIT 1;"; 
    $result = $wpdb->get_results( $sql, OBJECT );
    $color=$result[0]->value;
    //echo "...<pre>";var_dump($result);echo "</pre>...";echo $color;
  
    $order="ORDER-NUMBER";
    $costumer="COSTUMER-NAME";
    $product="PRODUCT-NAME";

    $template= file_get_contents(plugin_dir_path(__FILE__) ."/mail-template.php");

    $template= str_replace("GREEN", $color, $template);
    $template= str_replace("TITLE", $title, $template);
    $template= str_replace("GREETING", $greeting, $template);
    $template= str_replace("MESSAGE", $message, $template);
    $template= str_replace("{order}", $order, $template);
    $template= str_replace("{costumer}", $costumer, $template);
    $template= str_replace("{product}", $product, $template);/**/
    
    /*/en vez de traer el template del archivo lo estoy mostrando directo en html
    $message= str_replace("{order}", $order, $message);//convierte el mensaje
    $message= str_replace("{costumer}", $costumer, $message);
    $message= str_replace("{product}", $product, $message);/*Solo para WP team que te acepte*/
?>
    <table>
        <tr>
            <td>
                <form method="post" class="previewmail">
                    <button type="submit" name="outof">Out of stock</button><br><br>
                    <button type="submit" name="backin">Back in stock</button><br><br>
                    <button type="submit" name="soon">Back soon</button>
                </form>
            </td>
            <td>
                <div class="previewtemplate">    
                    
                    
<?php 
    echo filter_var ($template);    
    //mail_send("alejandrocastanondiaz@gmail.com",$subject,$template);
                    ?>
<!--<div>
    <div>
        <div>
            <div marginwidth="0" marginheight="0" style="padding:0">
                <div style="background-color:#f7f7f7;margin:0;padding:70px 0 0 0;width:100%">
                    <table class="adjust" border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
                        <tbody>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff;border:1px solid #dedede;border-radius:3px">
                                        <tbody>
                                            <tr>
                                                <td align="center" valign="top">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:<?php echo esc_html($color);?>;color:#ffffff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;border-radius:3px 3px 0 0">
                                                        <tbody>
                                                            <tr>
                                                                <td class="adjust" style="padding:36px 48px;display:block">
                                                                    <h1 style="font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:30px;font-weight:300;line-height:150%;margin:0;text-align:left;color:#ffffff;background-color:inherit"><?php echo esc_html($title);?></h1>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="center" valign="top">
                                                    <table border="0" cellpadding="0" cellspacing="0" width="600">
                                                        <tbody>
                                                            <tr>
                                                                <td valign="top" style="background-color:#ffffff">
                                                                    <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td class="adjust" valign="top" style="padding:48px 48px 32px">
                                                                                    <div style="color:#636363;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left">
                                                                                        <p style="margin:0 0 16px"><?php echo esc_html($greeting);?></p>
                                                                                        <p style="margin:0 0 16px"><?php echo esc_html($message);?></p>

                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="10" cellspacing="0" width="600">
                                        <tbody>
                                            <tr>
                                                <td valign="top" style="padding:0;border-radius:6px">
                                                    <table border="0" cellpadding="10" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2" valign="middle" style="border-radius:6px;border:0;color:#8a8a8a;font-family:&quot;Helvetica Neue&quot;,Helvetica,Roboto,Arial,sans-serif;font-size:12px;line-height:150%;text-align:center;padding:24px 0">
                                                                    <p style="margin:0 0 16px">Notification by <a href="https://qsynced.com/" style="color:GREEN;font-weight:normal;text-decoration:underline" target="_blank">QSynced</a></p>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>-->
                
                </div>
            </td>
        </tr>
    </table>
    <br>
<?php 
}else{
?>
                <form method="post" class="previewmailstart">
                    <button type="submit" name="outof">Out of stock</button>&nbsp;&nbsp;
                    <button type="submit" name="backin">Back in stock</button>&nbsp;&nbsp;
                    <button type="submit" name="soon">Back soon</button>
                </form><br>
<?php 
}
if(isset($_POST['save']) ){
    //echo "...<pre>";var_dump($_POST);echo "</pre>...";
    $sql = "UPDATE ".$tabla." SET creacion= CURRENT_TIMESTAMP, value='off'  WHERE meta='outofsend' OR meta='backinsend' ;";            
    $wpdb->query( $sql );    
    //echo $sql;echo "<br>";
    $i=0; foreach( $_POST as $key=>$feach){  
        $rowname=str_replace("3", "",str_replace("2", "",str_replace("1", "",str_replace("0", "", $key))));        
        $colname="value";
        if(strpos($key, '1') !== false){$colname="value2";
        }elseif(strpos($key, '2') !== false){$colname="value3";
        }elseif(strpos($key, '3') !== false){$colname="value4";
        }
        
        //$sql = "UPDATE ".$tabla." SET creacion= CURRENT_TIMESTAMP, ".$colname."='".sanitize_text_field($feach)."'  WHERE meta='".$rowname."';";
        $sql = "UPDATE ".$tabla." SET creacion= CURRENT_TIMESTAMP, ".$colname."='%s'  WHERE meta='%s';";
        $sql= $wpdb->prepare( $sql, $feach, $rowname );
        $wpdb->query( $sql );    
        //echo $sql;echo "<br>";
    $i++;}
}
//now echo stored values
$sql="SELECT * FROM ".$tabla." WHERE meta='adminemail' ORDER BY creacion DESC LIMIT 1;"; 
$result = $wpdb->get_results( $sql, OBJECT );
$adminemail=$result[0]->value;
$sql="SELECT * FROM ".$tabla." WHERE meta='colortemp' ORDER BY creacion DESC LIMIT 1;"; 
$result = $wpdb->get_results( $sql, OBJECT );
$color0=$result[0]->value;

$sql="SELECT * FROM ".$tabla." WHERE meta='outofsend' ORDER BY creacion DESC LIMIT 1;"; 
$result = $wpdb->get_results( $sql, OBJECT );
if($result[0]->value == 'on'){$outsend="checked";}else{$outsend="";}

$sql="SELECT * FROM ".$tabla." WHERE meta='backinsend' ORDER BY creacion DESC LIMIT 1;"; 
$result = $wpdb->get_results( $sql, OBJECT );
if($result[0]->value == 'on'){$backsend="checked";}else{$backsend="";}
    
$sql="SELECT * FROM ".$tabla." WHERE meta='outoftemp' ORDER BY creacion DESC LIMIT 1;"; 
$result = $wpdb->get_results( $sql, OBJECT );
$out0=$result[0]->value;
$out1=$result[0]->value2;
$out2=$result[0]->value3;
$out3=$result[0]->value4;

$sql="SELECT * FROM ".$tabla." WHERE meta='backintemp' ORDER BY creacion DESC LIMIT 1;"; 
$result = $wpdb->get_results( $sql, OBJECT );
$back0=$result[0]->value;
$back1=$result[0]->value2;
$back2=$result[0]->value3;
$back3=$result[0]->value4;

$sql="SELECT * FROM ".$tabla." WHERE meta='soontemp' ORDER BY creacion DESC LIMIT 1;"; 
$result = $wpdb->get_results( $sql, OBJECT );
$soon0=$result[0]->value;
$soon1=$result[0]->value2;
$soon2=$result[0]->value3;
$soon3=$result[0]->value4;
?>
    <form method="post">
    <h1>Custom e-mail templates:</h1>
    <span class="help">Write custom messages here, they can be plain text or HTML</span><br>
    <span class="help">&nbsp;&nbsp;* Use {order} to add the order number dynamically</span><br>
    <span class="help">&nbsp;&nbsp;* Use {product} to add the product name dynamically</span><br>
    <span class="help">&nbsp;&nbsp;* Use {costumer} to add the costumer name dynamically</span><br><br>
    <table>
        <tr>
            <td>Theme color</td>
            <td>
                <input type="color" name="colortemp" value="<?php echo esc_html($color0);?>">
                <span class="help">* The main color for the emails templates</span> 
            </td>
        </tr>
        <tr>
            <td>Admin's email</td>
            <td>
                <input type="email" name="adminemail" value="<?php echo esc_html($adminemail);?>"><br>
                <span class="help">* This email will receive a copy of back in stock emails, in order to process shipments</span> 
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <tr>
            <td width="20%">Template for out of stock</td>
            <td width="80%"><textarea name="outoftemp3" rows="3" placeholder='Example:&#10;"Sorry, we are out of stock of {product}, but we will ship it to you as soon as it is available again. Sorry for any inconvenients."' ><?php echo esc_html($out3);?></textarea></td>
        </tr>
        <tr>
            <td width="20%">Title</td>
            <td width="80%"><textarea name="outoftemp1" rows="1" placeholder='Example: "Out of stock notification"' ><?php echo esc_html($out1);?></textarea></td>
        </tr>
        <tr>
            <td width="20%">Greeting</td>
            <td width="80%"><textarea name="outoftemp2" rows="1"  placeholder='Example: "Hello {costumer}, bad news..."' ><?php echo esc_html($out2);?></textarea></td>
        </tr>
        <tr>
            <td width="20%">Subject</td>
            <td width="80%"><textarea name="outoftemp0" rows="1" placeholder='Example: "Message from YOUR PAGE regarding {product}"' ><?php echo esc_html($out0);?></textarea></td>
        </tr>
        <!--<tr>
            <td>Send email automatically?</td>
            <td>
                <input type="checkbox" name="outofsend" <?php echo esc_html($outsend);?>>
                <span class="help">* Costumer will be notified if it's order will be temporarily incomplete</span> 
            </td>
            
        </tr>-->
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <tr>
            <td>Template for back in stock and to be shipped soon</td>
            <td><textarea name="backintemp3" rows="3" placeholder='Example:&#10;"We have {product} back in stock, and we are shipping it to you right away. Thanks for your patience."' ><?php echo esc_html($back3);?></textarea></td>
        </tr>
        <tr>
            <td width="20%">Title</td>
            <td width="80%"><textarea name="backintemp1" rows="1" placeholder='Example: "Notification of {product} back in stock"' ><?php echo esc_html($back1);?></textarea></td>
        </tr>
        <tr>
            <td width="20%">Greeting</td>
            <td width="80%"><textarea name="backintemp2" rows="1"  placeholder='Example: "Hello {costumer}, bad news..."' ><?php echo esc_html($back2);?></textarea></td>
        </tr>
        <tr>
            <td width="20%">Subject</td>
            <td width="80%"><textarea name="backintemp0" rows="1" placeholder='Example: "Message from YOUR PAGE regarding {product}"' ><?php echo esc_html($back0);?></textarea></td>
        </tr>
        <tr>
            <td>Send email automatically?</td>
            <td>
                <input type="checkbox" name="backinsend" <?php echo esc_html($backsend);?>>
                <span class="help">* Costumer will be notified when product is back on stock, if replenish via the <a href="/wp-admin/admin.php?page=qsynced">main plugin tab</a></span> 
            </td>
        </tr>
        <tr>
            <td colspan="2"><hr></td>
        </tr>
        <tr>
            <td>Template for emails sent during wait</td>
            <td><textarea name="soontemp3" rows="3" placeholder='Example:&#10;"We are deeply sorry, but we are out of {product} still. We are working hard to have it available again and ship it to you. Thanks for your patience."' ><?php echo esc_html($soon3);?></textarea></td>
        <tr>
            <td width="20%">Title</td>
            <td width="80%"><textarea name="soontemp1" rows="1" placeholder='Example: "Notification of {product} still not available"' ><?php echo esc_html($soon1);?></textarea></td>
        </tr>
        <tr>
            <td width="20%">Greeting</td>
            <td width="80%"><textarea name="soontemp2" rows="1"  placeholder='Example: "Hello {costumer}, bad news..."' ><?php echo esc_html($soon2);?></textarea></td>
        </tr>
        <tr>
            <td width="20%">Subject</td>
            <td width="80%"><textarea name="soontemp0" rows="1" placeholder='Example: "Message from YOUR PAGE regarding {product}"' ><?php echo esc_html($soon0);?></textarea></td>
        </tr>
        <!--<tr>
            <td>Important</td>
            <td>
                <span class="help">* This type of email needs to be send manually inside the <a href="/wp-admin/admin.php?page=qsynced">main plugin tab</a>
                <br>by clickingthe "back soon" button on the desired product.</span>             
            </td>
        </tr>-->
    </table>
    <br><br>
    <button type="submit" name="save">Save</button>
    </form>
</div>
<?php }