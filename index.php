<?php

//Plugin Name: Ali Export
//Plugin URI: http://www.10meijin.com
//Description: Export Data from aliexpress.com
//Version: 1.0
//Author: James Qian
//Author URI: http://www.10meijin.com
//License: GPL
//Activtor Action
include "ae.php";
register_activation_hook( __FILE__, 'ali_export_install');
register_deactivation_hook( __FILE__, 'ali_export_uninstall' );  
function ali_export_install() {
 
// Action at the beginning
global $wpdb;
 

 $table_name = $wpdb->prefix . "aliexport";
$charset_collate = $wpdb->get_charset_collate();
 
$sql = "CREATE TABLE  $table_name (
 `sku` VARCHAR( 20 ) NOT NULL DEFAULT  '',
 `parent_sku` VARCHAR( 20 ) NOT NULL DEFAULT  '',
 `title` VARCHAR( 200 ) NOT NULL DEFAULT  '',
 `startard_price` FLOAT( 10 ) NOT NULL DEFAULT  '0.00',
 `sale_price` FLOAT( 10 ) NOT NULL DEFAULT  '0.00',
 `color` VARCHAR( 20 ) NOT NULL DEFAULT  '',
 `description` TEXT NOT NULL ,
 `short_description` TEXT NOT NULL ,
 `image` TEXT NOT NULL ,
 `weight` FLOAT NOT NULL DEFAULT  '0.00',
 `length` FLOAT NOT NULL DEFAULT  '0.00',
 `width` FLOAT NOT NULL DEFAULT  '0.00',
 `height` FLOAT NOT NULL DEFAULT  '0.00'
)  $charset_collate;";
 
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

}
function ali_export_uninstall() {
 
global $wpdb;
$table_name = $wpdb->prefix . "aliexport";
$wpdb->query("DROP TABLE IF EXISTS " . $table_name);
}

add_action( 'admin_menu', 'ali_export_create_menu' );

function ali_export_create_menu() {
global $my_settings;
$my_settings=add_menu_page(
"Ali Export",
"Ali Export",
"manage_options",
"ali-export",
"test"
);
}

function test(){
    global $wpdb;
    $table_name = $wpdb->prefix . "aliexport";
     $count=$wpdb->get_var("select count(*) from ".$table_name."  where parent_sku like ''" );
    echo"<html>
        <div>临时表中已经导入了 {$count} 个产品</div>
        <div width='2500px'>
</br></br></br></br>

<table width='2500px'><tr><td>
<lable>产品链接：<input id='item_url' class='url' type='text' name='item_url' width='2000px'/></label></td></tr>
<tr><td><a class='add'>增加产品</a></td></tr>
<tr><td><a class='export'>导出临时表</a></td></tr>
</table>
</form>
</div>
</html>";

}

function aad_load_scripts($hook) {
global $my_settings;
if( $hook != $my_settings )
return;
/*载入ajax的js文件,也可以载入其他的javascript和/或css等*/
wp_enqueue_script('my-ajax', plugins_url( 'aliexport/js/index.js', __FILE ), array('jquery'));
 
//wp_register_style( 'my-style', plugins_url( 'my-mood/css/style.css', __FILE ), array(), '', 'all' );
//wp_enqueue_style( 'my-style' );
 

wp_localize_script('my-js', 'my_vars', array(
'my_nonce' => wp_create_nonce('aad-nonce')
)
);
}
add_action('admin_enqueue_scripts', 'aad_load_scripts');

function add_item(){
$itemurl=$_POST['itemurl'];
$msg=add($itemurl);
$return=array();
$return['success'] = '1';
$return['msg']=$msg;
echo json_encode($return);
die();
}
add_action('wp_ajax_add_item', 'add_item');

function export_table(){
$msg=export();
$return=array();
$return['success'] = '1';
$return['msg']=$msg;
echo json_encode($return);
die();
}
add_action('wp_ajax_export_table', 'export_table');






