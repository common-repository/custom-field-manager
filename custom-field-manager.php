<?php 
/*
Plugin Name: Custom Field Manager
Plugin URI: http://xyzscripts.com/wordpress-plugins/custom-field-manager/details
Description: The Custom Field Manager lets you create and manage multiple custom fields and custom field groups. It supports custom field elements such as text field, numeric field, textarea and dropdown list. You can create different custom field groups for posts, pages as well as custom post types and under each group you can create multiple fields.   
Version: 1.0
Author: xyzscripts.com
Author URI: http://xyzscripts.com/
License: GPLv2 or later
*/

if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

ob_start();
//error_reporting(E_ALL);
define('XYZ_CFL_DIR',dirname( plugin_basename( __FILE__ ) ));
define('XYZ_CFL_PLUGIN_FILE',__FILE__);
global $wpdb;
$wpdb->query('SET SQL_MODE=""');
require_once( dirname( __FILE__ ) . '/admin/install.php' );
require_once( dirname( __FILE__ ) . '/admin/menu.php' );
require_once( dirname( __FILE__ ) . '/admin/uninstall.php' );
require_once( dirname( __FILE__ ) . '/xyz-functions.php' );
require_once( dirname( __FILE__ ) . '/admin/metabox.php' );
require_once( dirname( __FILE__ ) . '/admin/save-fields.php' );
require( dirname( __FILE__ ) . '/shortcode-handler.php' );
require( dirname( __FILE__ ) . '/ajax-handler.php' );

if(get_option('xyz_credit_link')=="cfl")
{
	add_action('wp_footer', 'xyz_cfl_credit');
}
function xyz_cfl_credit() {
	$content = '<div style="clear:both;width:100%;text-align:center; font-size:11px; "><a target="_blank" title="Custom Field Manager" href="http://xyzscripts.com/wordpress-plugins/custom-field-manager/details" >Custom Field Manager</a> Powered By : <a target="_blank" title="PHP Scripts & Wordpress Plugins" href="http://www.xyzscripts.com" >XYZScripts.com</a></div>';
	echo $content;
}
?>