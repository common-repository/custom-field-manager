<?php 
add_action( 'admin_menu', 'xyz_custom_field_manager_menu' );
function xyz_custom_field_manager_menu()
{
	add_menu_page('XYZ Custom Field Manager', 'Custom Fields', 'manage_options', 'custom-field-manager-customfields', 'xyz_cfl_manage_custom_fields');
	// Add a submenu to the Dashboard:
	$submenu_for_script = add_submenu_page('custom-field-manager-customfields', 'XYZ Custom Field Manager - Manage Custom Field Groups', 'Custom Fields', 'manage_options', 'custom-field-manager-customfields', 'xyz_cfl_manage_custom_fields');
	add_submenu_page('custom-field-manager-customfields', 'XYZ Custom Field Manager - Settings', 'Settings', 'manage_options', 'custom-field-manager-settings' ,'xyz_cfl_settings');

	add_action( 'admin_print_scripts-' . $submenu_for_script, 'xyz_cfl_admin_custom_js' );

}

function xyz_cfl_admin_custom_js()
{
	wp_enqueue_script("jquery");wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-droppable');
	wp_enqueue_script('jquery-ui-sortable');
}


function xyz_cfl_manage_custom_fields()
{
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	add_thickbox(); 
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/manage-cfl.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}

function xyz_cfl_settings()
{
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/settings.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}

function xyz_custom_field_manager_admin_scripts()
{
	wp_register_style('xyz_custom_field_manager_style', plugins_url(XYZ_CFL_DIR.'/admin/style.css'));
	wp_enqueue_style('xyz_custom_field_manager_style');
	wp_register_script( 'xyz_custom_field_manager_script', plugins_url(XYZ_CFL_DIR.'/admin/notice.js') );
	wp_enqueue_script( 'xyz_custom_field_manager_script' );
}
add_action("admin_enqueue_scripts","xyz_custom_field_manager_admin_scripts");
?>