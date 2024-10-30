<?php 
function xyz_cfl_network_destroy($networkwide) {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				xyz_cfl_destroy();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	xyz_cfl_destroy();
}

function xyz_cfl_destroy()
{
	global $wpdb;
	delete_option('xyz_cfl_mandatory_field');
	delete_option('xyz_cfl_shortcode_field');
	if(get_option('xyz_credit_link')=="cfl")
	{
		update_option("xyz_credit_link", '0');
	}
	$wpdb->query("DROP TABLE ".$wpdb->prefix."xyz_cfl_group");
	$wpdb->query("DROP TABLE ".$wpdb->prefix."xyz_cfl_fields");
	
	$tables =  $wpdb->get_results('SHOW TABLES');
	$dbname="Tables_in_".DB_NAME;
	foreach($tables as $key => $value)
	{
		$table=$value->$dbname;
		$pre=$wpdb->prefix."xyz_cfl_field_values";
	
		$pos=strpos($table, $pre);
		if ($pos !== false)
			$wpdb->query("DROP TABLE ".$table);
	}
	
}
register_uninstall_hook(XYZ_CFL_PLUGIN_FILE,'xyz_cfl_network_destroy');
?>