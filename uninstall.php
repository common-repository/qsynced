<?php
/*
* Execute uninstall
*
*/

if ( ! defined('ABSPATH') ){ echo 'lero lero no puedes acceder'; die;}/*esto es por seguridad por si alguien intenta acceder remotamente*/

//clear database data
global $wpdb;
$prefix= $wpdb->prefix;
$tabla = $prefix.'qsynced';
$wpdb->query( 'DROP TABLE '.$tabla.';' );