<?php
add_action( 'save_post', 'xyz_cfl_save_tax_meta',101 );
function xyz_cfl_save_tax_meta( $post_ID ) 
{
// 	if ( false !== wp_is_post_revision( $post_ID ) )
// 		return;
	
	global $wpdb;
	$set_flag=0;
	$xyz_cfl_mandatory_field= get_option('xyz_cfl_mandatory_field');
	$where_array=array( 'post_id' => $post_ID );
	
	$_POST_CPY=$_POST;
	$_POST = stripslashes_deep($_POST);
	
	$tax="";$terms="";$fldval1=array();
	$post_type=get_post_type($post_ID);
	$taxonomies = get_object_taxonomies($post_type,'objects');
	if ( $taxonomies )
	{
		foreach ($taxonomies as $key=>$values1)
		{
			if($values1->hierarchical==1)
				$tax.=$key.",";
		}
	}
		
	$tax=rtrim($tax,",");
	$tax_array=explode(',', $tax);
	$count_tax=count($tax_array);
	$GLOBALS['flg_for_tax_sv_drft']=0;
	
	
	for($i=0;$i<$count_tax;$i++)
	{
		$fldval=array();
		$term_lists=get_the_terms( $post_ID, $tax_array[$i]);
		
		if ( $term_lists>0 )
		{
			foreach ($term_lists as $key=>$values)
			{
				$checked_term=$values->term_id;
				$parnts_ed="";
				$t_id_ed= $checked_term;
				if($t_id_ed!=0)
				{
					$par_ed=1;
					while ($par_ed==1 )
					{
						$get_parent_ed=$wpdb->get_row($wpdb->prepare("SELECT `parent`,`term_id` FROM `".$wpdb->prefix."term_taxonomy` WHERE `term_id` =%d AND taxonomy=%s",$t_id_ed,$tax_array[$i]));
						$pi_ed= $get_parent_ed->parent;
						if($pi_ed==0)
						{
							$par_ed=0;
							$parnts_ed.=$get_parent_ed->term_id.",";
						}
						else
						{
							$parnts_ed.=$get_parent_ed->term_id.",";
							$t_id_ed=$pi_ed;
						}
					}
				}
				$parnts_ed=rtrim($parnts_ed,",");
				$exp_parent=explode(',', $parnts_ed);
				$cnt_p=count($exp_parent);
				$term_id=xyz_cfl_parent_term($checked_term,$tax_array[$i]);
				$tab_name_str=strtolower($tax_array[$i]);
				$tab_name=str_replace("-","_",$tab_name_str);
				$tab_name=$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term_id;
				
				for($l=$cnt_p-1;$l>=0;$l--)
				{
					$fldval=xyz_cfl_fetch_data($exp_parent[$l],$xyz_cfl_mandatory_field,$tax_array[$i],$post_ID,$fldval);
				}
				xyz_cfl_insert_data($post_ID,$fldval,$tab_name,$where_array);
			}
		}
	}
	$fldval1=xyz_cfl_fetch_data('',$xyz_cfl_mandatory_field,'',$post_ID,$fldval1);
	xyz_cfl_insert_data($post_ID,$fldval1,$wpdb->prefix."xyz_cfl_field_values",$where_array);
	
	if($GLOBALS['flg_for_tax_sv_drft']==1)
	{
		$wpdb->update($wpdb->prefix.'posts',array('post_status'=>'draft'),array('id'=>$post_ID));
	}
	
	$_POST=$_POST_CPY;
}

function xyz_cfl_insert_data($post_ID,$fldval,$tab_name,$where_array)
{
	global $wpdb;
	$data_ar=array_merge($where_array,$fldval);
	$all_ids=$wpdb->get_results($wpdb->prepare("SELECT `post_id` FROM `".$tab_name."` WHERE post_id=%d",$post_ID));
	if(count($all_ids)==0)
		$wpdb->insert($tab_name, $data_ar);
	else
		$wpdb->update( $tab_name, $data_ar, $where_array);
}

function xyz_cfl_fetch_data($exp_parent,$xyz_cfl_mandatory_field,$tax_array,$post_ID,$fldval)
{
	
	global $wpdb;
	$checked_field_lists=$wpdb->get_results($wpdb->prepare("SELECT id,xyz_cfl_group_name FROM ".$wpdb->prefix."xyz_cfl_group WHERE xyz_cfl_group_taxonomy_term_id=%d ",$exp_parent));
	foreach ($checked_field_lists as $checked_field_list)
	{
		$grp_id= $checked_field_list->id;
		$checked_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE `xyz_cfl_group_id`=%d ORDER BY `xyz_cfl_field_order` ASC",$grp_id));
		foreach ($checked_lists as $list)
		{
			$tab_fld_id=$list->id;
			$def_val=$list->xyz_cfl_field_default;
			$xyz_cfl_field_mandatory=$list->xyz_cfl_field_mandatory;
			if(isset($_POST["fld_id_$tab_fld_id"]))
			{
				$insert_fld_val=$_POST["fld_id_$tab_fld_id"];
				if($insert_fld_val=="" && $xyz_cfl_mandatory_field==0 && $xyz_cfl_field_mandatory==0)
					$insert_fld_val=$def_val;
				$fldval["field_$tab_fld_id"]=$insert_fld_val;

				
				$fld_id="fld_id_".$tab_fld_id."";
				if($xyz_cfl_field_mandatory==0)
				{
					if(isset($_POST[$fld_id]))
					{
						$fld_id_set=$_POST[$fld_id];
						if($fld_id_set=="" && $xyz_cfl_mandatory_field==1)
						{
							$GLOBALS['flg_for_tax_sv_drft']=1;
						}
					}
				}
			}
		}
	}
	return $fldval;
}
?>