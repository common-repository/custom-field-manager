<?php 
function xyz_cfl_network_install($networkwide) {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				xyz_cfl_install();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	xyz_cfl_install();
}
function xyz_cfl_install()
{
	global $wpdb;
	$pluginName= 'custom-field-manager/custom-field-manager.php';
	
	if(get_option('xyz_credit_link')=="")
	{
		add_option("xyz_credit_link", '0');
	}
	add_option('xyz_cfl_mandatory_field', '0');
	add_option('xyz_cfl_shortcode_field', '0');
	
	
	$queryMapping ="CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."xyz_cfl_group (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xyz_cfl_group_name` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `xyz_cfl_group_post_type` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `xyz_cfl_group_taxonomy` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `xyz_cfl_group_taxonomy_term_id` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `xyz_cfl_group_order` int(11) NOT NULL,
  `xyz_cfl_group_status` int(11) NOT NULL COMMENT '0-Deactivate,1-Activate',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='custom field group' AUTO_INCREMENT=1";

	$wpdb->query($queryMapping);
	
	$queryMapping ="CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."xyz_cfl_fields (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xyz_cfl_group_id` int(11) NOT NULL,
  `xyz_cfl_field_name` text COLLATE utf8_unicode_ci NOT NULL,
  `xyz_cfl_field_type` text COLLATE utf8_unicode_ci NOT NULL,
  `xyz_cfl_field_order` int(11) NOT NULL,
  `xyz_cfl_field_status` int(11) NOT NULL,
  `xyz_cfl_field_mandatory` int(11) NOT NULL,
  `xyz_cfl_field_placeholder` text COLLATE utf8_unicode_ci NOT NULL,
  `xyz_cfl_field_default` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='custom fields' AUTO_INCREMENT=1";
	
	$wpdb->query($queryMapping);
}

add_action( 'init', 'xyz_cfl_create_book_tax1' );

function xyz_cfl_create_book_tax1() {
	register_taxonomy(
			'test',
			'post',
			array(
					'label' => __( 'test' ),
					'rewrite' => array( 'slug' => 'test' ),
					'hierarchical' => true,
			)
	);
}

add_action( 'init', 'xyz_cfl_create_book_tax5' );

function xyz_cfl_create_book_tax5() {
	register_taxonomy(
			'good',
			'book',
			array(
					'label' => __( 'good' ),
					'rewrite' => array( 'slug' => 'good' ),
					'hierarchical' => true,
			)
	);
}

add_action( 'init', 'xyz_cfl_create_post_type2' );
function xyz_cfl_create_post_type2() {
	register_post_type( 'book',
			array(
					'labels' => array(
							'name' => __( 'Book' ),
							'singular_name' => __( 'Book' )
					),
					'public' => true,
					'has_archive' => true,
			)
	);
}
add_action( 'init', 'xyz_cfl_create_book_tax2' );

function xyz_cfl_create_book_tax2() {
	register_taxonomy(
			'check',
			'book',
			array(
					'label' => __( 'check' ),
					'rewrite' => array( 'slug' => 'check' ),
					'hierarchical' => true,
			)
	);
}
register_activation_hook(XYZ_CFL_PLUGIN_FILE,'xyz_cfl_network_install');
?>
