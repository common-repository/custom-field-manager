<?php
global $wpdb;
$post_ID="";
if(isset($params['id']))
	$post_ID=$params['id'];

$tax="";
$post_type=get_post_type();
$nulterm=0;

$grp_dtls=$wpdb->get_results($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."xyz_cfl_group` WHERE `xyz_cfl_group_post_type`=%s AND xyz_cfl_group_taxonomy_term_id=%d ORDER BY `xyz_cfl_group_order` ASC",$post_type,$nulterm));

xyz_cfl_display($post_ID,$post_type,$tax,$grp_dtls);
$taxonomies = get_object_taxonomies($post_type,'objects');
if ( $taxonomies )
{
	foreach ($taxonomies as $key1=>$values1)
	{
		if($values1->hierarchical==1)
			$tax.=$key1.",";
	}
}
$taxs=explode(',', $tax);
$count_tax=count($taxs);
for($j=0;$j<$count_tax;$j++)
{
	$term_lists=get_the_terms( $post_ID, $taxs[$j]);
	if ( $term_lists )
	{
		foreach ($term_lists as $key=>$values)
		{
			$checked_term=$values->term_id;
			
			$parnts_ed="";
			$t_id_ed= $checked_term;
			if($t_id_ed!=0){
				$par_ed=1;
				while ($par_ed==1 )
				{
					$get_parent_ed=$wpdb->get_row($wpdb->prepare("SELECT `parent`,`term_id` FROM `".$wpdb->prefix."term_taxonomy` WHERE `term_id` =%d AND taxonomy=%s",$t_id_ed,$taxs[$j]));
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
			for($l=$cnt_p-1;$l>=0;$l--)
			{
				$grp_dtls=$wpdb->get_results($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."xyz_cfl_group` WHERE `xyz_cfl_group_post_type`=%s AND xyz_cfl_group_taxonomy_term_id=%d ORDER BY `xyz_cfl_group_order` ASC",$post_type,$exp_parent[$l]));
				xyz_cfl_display($post_ID,$post_type,$taxs[$j],$grp_dtls);
			}
		}
	}
}
	

function xyz_cfl_display($pid,$p,$taxonomy,$grp_dtls)
{
	global $wpdb;
	$post_ID=$pid;
	$post_type=$p;
	if(isset($grp_dtls))
	{
		foreach ($grp_dtls as $grp_dtl)
		{
			$group_id=$grp_dtl->id;
			$grp_status=$grp_dtl->xyz_cfl_group_status;
			$grp_name=$grp_dtl->xyz_cfl_group_name;
			$term_selectedOptions=$grp_dtl->xyz_cfl_group_taxonomy_term_id;
			$term_id=xyz_cfl_parent_term($term_selectedOptions,$taxonomy);
			
			if($grp_status==1)
			{
				$fld_dtls=$wpdb->get_results($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."xyz_cfl_fields` WHERE `xyz_cfl_group_id`=%d ORDER BY xyz_cfl_field_order ASC",$group_id));
				?>
				<h4><?php echo $grp_name;?></h4>
				<?php
				
				foreach ($fld_dtls as $fld_dtl)
				{
					$fld_id=$fld_dtl->id;
					$fld_name=$fld_dtl->xyz_cfl_field_name;
					$fld_type=$fld_dtl->xyz_cfl_field_type;
					$fld_mand=$fld_dtl->xyz_cfl_field_mandatory;
					$fld_default=$fld_dtl->xyz_cfl_field_default;
					$fld_status=$fld_dtl->xyz_cfl_field_status;
					
					$fld_xyz_cfl_mandatory_opt=get_option('xyz_cfl_mandatory_field');
					
					
					$tab_name_str=strtolower($taxonomy);
					$tab_name=str_replace("-","_",$tab_name_str);
					if($taxonomy=='')
						$tab_name=$wpdb->prefix."xyz_cfl_field_values";
					else
						$tab_name=$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term_id;
					
					$fld_show_lists=$wpdb->get_results($wpdb->prepare("SELECT `field_".$fld_id."` FROM `".$tab_name."` WHERE `post_id`=%d",$post_ID));
					$col="field_".$fld_id;
					$data="";
					foreach($fld_show_lists as $fld_show_list)
					{
						$star="";
						$data=$fld_show_list->$col;
						if($data=="" && $fld_xyz_cfl_mandatory_opt==0 && $fld_mand==0)
						$data=$fld_default;
						else if($data=="")
						$data="NA";
						
						if($fld_status==1)
						{
							if($fld_mand==0)
							$star="*";
							?>
							<table id="tab_id_<?php echo $group_id?>"  style="width: 99%; margin: 0 auto; margin-top : 10px;">
							<tr><td width="20%"><?php echo $fld_name;?>  <span style="color: red;"><?php echo $star;?></span>
							</td><td width="5%">:</td>
							<td><?php echo esc_html($data);?>
							</td>
							</tr>
							</table>
							<?php
						}
					}
				}
			}
		}
	}
}
?>