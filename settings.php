<?php
if ( ! defined('ABSPATH') ){ echo 'lero lero no puedes acceder'; die;}/*esto es por seguridad por si alguien intenta acceder remotamente*/
require_once plugin_dir_path(__FILE__) . '/css.php'; 
global $wpdb;
$prefix= $wpdb->prefix;
$tabla = $prefix.'qsynced';
?>
<div class="qsync">
<?php 
if( isset($_POST['email']) ){
    $sql = "UPDATE ".$tabla." SET creacion= CURRENT_TIMESTAMP, value=%s WHERE meta='email';";
    $sql= $wpdb->prepare( $sql, $_POST['email']);    
    $wpdb->query( $sql );?>    
    <!--<h1>That's all! Enjoy QSynced</h1>-->
    <h2>Your email has been updated to: <?php echo esc_html($_POST['email']);?></h2>
<?php }else{?>    
    <h1>Type your email used for your subscription at <a href="https://qsynced.com/user" target="_blank">www.qsynced.com</a> :</h1>
<?php }
    $result = $wpdb->get_results( 'SELECT * FROM '.$tabla." WHERE meta='email' LIMIT 1;", OBJECT );
    $email=$result[0]->value;
    if ( !empty( $email ) ) {$placeholder=$email;}else{$placeholder="E-mail";}
//    echo "...<pre>".var_dump($result)."</pre>..."; echo $result[0]->value;
    $status = wp_remote_retrieve_body( wp_remote_get( 'https://qsynced.com/qsynced-api.php?email='.$email ) );
    $stat = json_decode($status,true);if(!isset($stat['status']) ){$stat['status']="";}
    //echo "...<pre>";var_dump($stat);echo "</pre>...";
?>
    <form method='post'>
        <input type='email' name='email' placeholder='<?php echo esc_html($placeholder);?>' required>
        <button type='submit' name='boton'>Save</button>
    </form>
    <h3>
    <?php if($stat['status'] == 'paying') { ?>
        Your subscription is <span class='success'>Active</span>.</h3><h3>Your selected plan is: <?php echo esc_html($stat['plan']);?> until <?php echo esc_html(date('l dS \o\f F Y', strtotime($stat['expire']) ));?>
    <?php }else{?>
        Your subscription is <span class='error'>NOT Active</span>.</h3><h3>
    Please enter a valid e-mail or visit <a href='https://qsynced.com/' target='_blank'>www.qsynced.com</a> to subscribe
    <?php }?>     
    </h3>
    <hr>
    <!--<h1>Here we can create an instruction vídeo later, and upload it to youtube:</h1>
    <iframe width="560" height="300" src="https://www.youtube.com/embed/hEm_ZOnfyZk" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe-->>    
</div>
<?php