<?php
add_action('wp_ajax_xyz_cfl_fetch_taxonomies_frst', 'xyz_cfl_fetch_taxonomies_frst');
function xyz_cfl_fetch_taxonomies_frst()
{
	global $wpdb;
	$post_type =  $_POST['post_type'] ;
	$cat=$_POST['tax'] ;
	$taxonomies = get_object_taxonomies($post_type,'objects');

	if ( $taxonomies )
	{
		$tax="";
		$tax.='<option value=""';
		if($cat=="")
		{
			$tax.='selected="selected"';
		}
		$tax.='>Select</option>';
		foreach ($taxonomies as $key=>$values)
		{
			if($taxonomies[$key]->hierarchical==1){
				$tax.='<option value ="'.$key.'"';
			if($cat==$key)
			{
				$tax.='selected="selected"';
			}
			$tax.='>'.$key.'</option>';}
		}
		echo $tax;
	}
	die;
}

add_action('wp_ajax_xyz_cfl_fetch_taxonomy_ids', 'xyz_cfl_fetch_taxonomy_ids');
function xyz_cfl_fetch_taxonomy_ids() 
{
		global $wpdb;
 		$taxonomy =  $_POST['taxonomy'] ;
 		$term = $_POST['term'] ;
 		$tax_id="";
 		$pid=0;$i=0;$cat_id="";
 		
 		$tax_id_lists=$wpdb->get_results($wpdb->prepare("SELECT name,".$wpdb->prefix."terms.term_id,".$wpdb->prefix."term_taxonomy.parent FROM ".$wpdb->prefix."term_taxonomy JOIN ".$wpdb->prefix."terms ON ".$wpdb->prefix."terms.term_id=".$wpdb->prefix."term_taxonomy.term_id WHERE ".$wpdb->prefix."term_taxonomy.taxonomy=%s ORDER BY term_id DESC",$taxonomy));
        foreach ($tax_id_lists as $categ_id)
        {
        	$cat_id=$categ_id->term_id;
       	}
        echo xyz_cfl_get_category_display($pid,$i,$cat_id,$taxonomy,$term);
         	
        die; 
}

add_action('wp_ajax_xyz_cfl_entry_delete', 'xyz_cfl_grp_delete');
function xyz_cfl_grp_delete() 
{
	global $wpdb;
	$id=$_POST['enable'];
	$type=$_POST['type'];
	$er="";
	$fields=$wpdb->get_results($wpdb->prepare("SELECT id FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_group_id=%d",$id));
	$count=count($fields);
	if($count>0)
	{
		echo "er";
	}
	else 
	{
		$wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->prefix.'xyz_cfl_group  WHERE id=%d',$id )) ;
		$order=$_POST['neworder'];
		$order1=str_replace('drag[]=', "", $order);
		$orders=explode("&",$order1);
		$c=count($orders);
		for ($k=0;$k<$c;$k++)
		{
			if($orders[$k]==$id)
			{
				unset($orders[$k]);
			}
		}
		$new_ord=array_values($orders);
		$str="";
		for($i=1;$i<=count($new_ord);$i++)
		{
			$norders=$new_ord[$i-1];
			$str.=$norders.",";
			$update_order = $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_cfl_group SET xyz_cfl_group_order=%d WHERE id=%d",$i,$norders));
		}
		$str=rtrim($str,",");
		$new_grp_count=count($new_ord);
		$no_grp_msg_div="";
		if($new_grp_count==0)
		{
			$no_grp_msg_div.='<table class="widefat  xyz_cfl_table" id="no_grp_tbl_'.$id.'" style="width: 100%; margin: 0 auto; " >
							<tr>
							<td colspan="5">No records found.
							</td>
							</tr>
							</table>';
		}
		echo $str.",".$new_grp_count."{explode_0090}".$no_grp_msg_div;
	}
	die;
}

add_action('wp_ajax_xyz_cfl_fld_entry_delete', 'xyz_cfl_fld_delete');
function xyz_cfl_fld_delete() 
{
	global $wpdb;
	$id=$_POST['enable'];
	$group_id=$_POST['group_id'];
	$wpdb->query( $wpdb->prepare( 'DELETE FROM '.$wpdb->prefix.'xyz_cfl_fields  WHERE id=%d',$id )) ;
	$taxonomy= $wpdb->get_row( $wpdb->prepare( 'SELECT xyz_cfl_group_taxonomy FROM '.$wpdb->prefix.'xyz_cfl_group  WHERE id=%d',$group_id )) ;
	
	$tab_name_str=strtolower($taxonomy->xyz_cfl_group_taxonomy);
	
	$tab_name=str_replace("-","_",$tab_name_str);
	$xyz_cfl_taxonomy_trmids=$wpdb->get_row($wpdb->prepare("SELECT xyz_cfl_group_taxonomy,xyz_cfl_group_taxonomy_term_id FROM ".$wpdb->prefix."xyz_cfl_group WHERE id=%d",$group_id));
	$term_selectedOptions=$xyz_cfl_taxonomy_trmids->xyz_cfl_group_taxonomy_term_id;
	$ps_tax=$xyz_cfl_taxonomy_trmids->xyz_cfl_group_taxonomy;
	if($term_selectedOptions!=0)
		$term_id=xyz_cfl_parent_term($term_selectedOptions,$ps_tax);
	if($tab_name!="")
	{
		if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term_id."'") ==$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term_id) 
		{
			$tab_fld_id=$id;
			$fld="";
	
			$tblcolums = $wpdb->get_results("SHOW COLUMNS FROM  ".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term_id." like 'field_".$tab_fld_id."'");
			foreach ($tblcolums as $row)
			{
				$fld=$row->Field;
				if($fld=="field_".$tab_fld_id."")
				{
					$wpdb->query("ALTER TABLE ".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term_id." DROP `field_".$tab_fld_id."`");
	
				}
			}
		}
	}
	else
	{
		if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."xyz_cfl_field_values'") ==$wpdb->prefix."xyz_cfl_field_values") 
		{
			$tab_fld_id=$id;
			$fld="";
		
			$tblcolums = $wpdb->get_results("SHOW COLUMNS FROM  ".$wpdb->prefix."xyz_cfl_field_values like 'field_".$tab_fld_id."'");
			foreach ($tblcolums as $row)
			{
				$fld=$row->Field;
				if($fld=="field_".$tab_fld_id."")
				{
					$wpdb->query("ALTER TABLE ".$wpdb->prefix."xyz_cfl_field_values DROP `field_".$tab_fld_id."`");
				}
			}
		}
	}
	
	$inc=1;
	$fld_orders=$wpdb->get_results($wpdb->prepare("SELECT xyz_cfl_field_order FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_group_id=%d ORDER BY xyz_cfl_field_order ASC",$group_id));
	foreach( $fld_orders as $fld_order ) 
	{
		$ordr[$inc-1]=$fld_order->xyz_cfl_field_order;
		$inc++;
	}
	$len_ord=count($ordr);
	for($i=0;$i<$len_ord;$i++)
	{
		$update_orders = $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_cfl_fields SET xyz_cfl_field_order=%d WHERE xyz_cfl_field_order=%d AND xyz_cfl_group_id=%d",$i+1,$ordr[$i],$group_id));
		$n_fld_orders=$wpdb->get_results($wpdb->prepare("SELECT id FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_group_id=%d ORDER BY xyz_cfl_field_order ASC",$group_id));
	}
	$str="";
	foreach( $n_fld_orders as $n_fld_order ) 
	{
		$str.=$n_fld_order->id.",";
	}
	$str=rtrim($str,",");
	$new_count=count($n_fld_orders);
	if($new_count==0)
	{
		$no_fld_msg_div="";
		$no_fld_msg_div.='<table class="widefat  xyz_cfl_table" style="width: 100%; margin: 0 auto; " id="no_fld_tbl_'.$group_id.'">
							<tr class="no_fields_message" id="no_fields_message_'.$group_id.'">
							<td colspan="5">No records found.
							</td>
							</tr>
							</table>';
	}
	else
		$no_fld_msg_div="";
	echo $str.",".$new_count."{explode_0090}".$no_fld_msg_div;
	die;
}

add_action('wp_ajax_xyz_cfl_grp_entry_set_status', 'xyz_cfl_grp_set_status');
function xyz_cfl_grp_set_status() 
{
	global $wpdb;
	$id=$_POST['grp_activate'];
	$status_val=$wpdb->get_results($wpdb->prepare("SELECT `xyz_cfl_group_status` FROM ".$wpdb->prefix."xyz_cfl_group WHERE `id`=%d",$id));
	foreach( $status_val as $entry ) 
	{
		$status=$entry->xyz_cfl_group_status;
	}
	if($status==1)
	{
		$status=0;$str="<img  name='statusimage_".$id."' id='statusimage_".$id."' class='img' title='Activate Field Group' src='".plugins_url(XYZ_CFL_DIR.'/admin/images/activate.png')."'/>   
			";$stat_str="Inactive";$clr="red";
	}
	else
	{
		$status=1;$str="<img  name='statusimage_".$id."' id='statusimage_".$id."' class='img' title='Deactivate Field Group' src='".plugins_url(XYZ_CFL_DIR.'/admin/images/blocked.png')."'/>   
			";$stat_str="Active";$clr="green";
	}
	$wpdb->query( $wpdb->prepare( 'UPDATE '.$wpdb->prefix.'xyz_cfl_group SET `xyz_cfl_group_status`=%d WHERE id=%d',$status,$id )) ;
	echo $str.','.$stat_str.','.$clr;
	die;
}

add_action('wp_ajax_xyz_cfl_fld_entry_set_status', 'xyz_cfl_fld_set_status');
function xyz_cfl_fld_set_status() 
{
	global $wpdb;
	$id=$_POST['fld_activate'];
	$status_val=$wpdb->get_results($wpdb->prepare("SELECT `xyz_cfl_field_status` FROM ".$wpdb->prefix."xyz_cfl_fields WHERE `id`=%d",$id));
	foreach( $status_val as $entry ) 
	{
		$status=$entry->xyz_cfl_field_status;
	}
	if($status==1)
		{$status=0;$str="<img  name='statusimage_".$id."' id='statusimage_".$id."' class='img' title='Activate Field Group' src='".plugins_url(XYZ_CFL_DIR.'/admin/images/activate.png')."'/>   
			";$stat_str_fld="Inactive";$clr_fld="red";}
	else
		{$status=1;$str="<img  name='statusimage_".$id."' id='statusimage_".$id."' class='img' title='Deactivate Field Group' src='".plugins_url(XYZ_CFL_DIR.'/admin/images/blocked.png')."'/>   
			";$stat_str_fld="Active";$clr_fld="green";}
	$wpdb->query( $wpdb->prepare( 'UPDATE '.$wpdb->prefix.'xyz_cfl_fields SET `xyz_cfl_field_status`=%d WHERE id=%d',$status,$id )) ;
	echo $str.','.$stat_str_fld.','.$clr_fld;
	die;
}

add_action('wp_ajax_xyz_cfl_drag_n_drop1', 'xyz_cfl_grp_drag_n_drop');
function xyz_cfl_grp_drag_n_drop()
{
	global $wpdb;
	$order=$_POST['neworder'];
	$order1=str_replace('drag[]=', "", $order);
	$orders=explode("&",$order1);
	$str="";
	for($i=1;$i<=count($orders);$i++)
	{
		$norders=$orders[$i-1];
		$str.=$norders.",";
		$update_order = $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_cfl_group SET xyz_cfl_group_order=%d WHERE id=%d",$i,$norders));
	}
	$str=rtrim($str,",");
	echo $str;
	die;
}

add_action('wp_ajax_xyz_cfl_drag_n_drop2', 'xyz_cfl_fld_drag_n_drop');
function xyz_cfl_fld_drag_n_drop()
{
	global $wpdb;
	$fld_order=$_POST['fld_neworder'];
	$fld_order1=str_replace('drag2[]=', "", $fld_order);
	$fld_orders=explode("&",$fld_order1);
	$str="";
	for($i=1;$i<=count($fld_orders);$i++)
	{
		$fld_norders=$fld_orders[$i-1];
		$str.=$fld_norders.",";
		$update_fld_order = $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_cfl_fields SET xyz_cfl_field_order=%d WHERE id=%d",$i,$fld_norders));
	}
	$str=rtrim($str,",");
	echo $str;
	die;
}

add_action('wp_ajax_xyz_cfl_save_new_field', 'xyz_cfl_save_new_field');
function xyz_cfl_save_new_field()
{
	global $wpdb;
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	$tbl="";$str="";
	$erf_add_fld="";
	$ms1_add_fld="";
	$xyz_cfl_field_group_id=$_POST['id'];
	$xyz_cfl_field_name=$_POST['xyz_cfl_field_name'];
	$xyz_cfl_field_type=$_POST['xyz_cfl_field_type'];
	$xyz_cfl_field_placeholder=$_POST['xyz_cfl_field_placeholder'];
	$xyz_cfl_field_default=$_POST['xyz_cfl_field_default'];
	$xyz_cfl_field_mandatory=$_POST['xyz_cfl_field_mandatory'];
	$xyz_cfl_field_status=0;
	$fld_count=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_field_name=%s AND xyz_cfl_group_id =%d",array($xyz_cfl_field_name,$xyz_cfl_field_group_id)));
	$fld_exist=count($fld_count);
	$fld_order=$wpdb->get_results($wpdb->prepare("SELECT xyz_cfl_field_order FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_group_id =%d",$xyz_cfl_field_group_id));
	if(count($fld_order)!=0)
	{
		$max_fld_orders=(max($fld_order));
		foreach( $max_fld_orders as $max_fld_order )
		{
			$xyz_cfl_field_order=$max_fld_order+1;
		}
	}
	else
		$xyz_cfl_field_order=1;
	if($xyz_cfl_field_name=="")
	{
		echo "er1";
		$erf_add_fld=1;
	}
	else if($fld_exist>0)
	{
		echo "er0";
		$erf_add_fld=1;
	}
	else if($xyz_cfl_field_type=="")
	{
		echo "er2";
		$erf_add_fld=1;
	}
	else if(($xyz_cfl_field_type=="Dropdown" || $xyz_cfl_field_mandatory==0) && $xyz_cfl_field_default=="")
	{
		echo "mand_check";
		$erf_add_fld=1;
	}
	else
	{
		$erf_add_fld=0;
		$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_cfl_fields
				(`xyz_cfl_group_id`, `xyz_cfl_field_name`,`xyz_cfl_field_type`, `xyz_cfl_field_order`, `xyz_cfl_field_status`, `xyz_cfl_field_mandatory`,`xyz_cfl_field_placeholder`, `xyz_cfl_field_default`)
				VALUES (%d,'%s','%s',%d,%d,%d,'%s','%s')",$xyz_cfl_field_group_id, $xyz_cfl_field_name, $xyz_cfl_field_type,$xyz_cfl_field_order,$xyz_cfl_field_status,$xyz_cfl_field_mandatory, $xyz_cfl_field_placeholder,$xyz_cfl_field_default ));
		$xyz_cfl_fld_ids=$wpdb->get_results($wpdb->prepare("SELECT id FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_group_id=%d ORDER BY id DESC LIMIT 0,1",$xyz_cfl_field_group_id));
		foreach($xyz_cfl_fld_ids as $xyz_cfl_fld_id)
		{
			$tab_fld_id=$xyz_cfl_fld_id->id;
		}
		$xyz_cfl_taxonomys=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_group WHERE id=%d",$xyz_cfl_field_group_id));
		foreach($xyz_cfl_taxonomys as $xyz_cfl_taxonomy)
		{
			$xyz_cfl_taxonomy=$xyz_cfl_taxonomy->xyz_cfl_group_taxonomy;
			
		}
		$tab_name_str=strtolower($xyz_cfl_taxonomy);
		$tab_name=str_replace("-","_",$tab_name_str);
		
		$xyz_cfl_taxonomy_trmids=$wpdb->get_row($wpdb->prepare("SELECT xyz_cfl_group_taxonomy,xyz_cfl_group_taxonomy_term_id FROM ".$wpdb->prefix."xyz_cfl_group WHERE id=%d",$xyz_cfl_field_group_id));
		$term_selectedOptions=$xyz_cfl_taxonomy_trmids->xyz_cfl_group_taxonomy_term_id;
		$ps_tax0=$xyz_cfl_taxonomy_trmids->xyz_cfl_group_taxonomy;
		$term_id=xyz_cfl_parent_term($term_selectedOptions,$ps_tax0);
		
		
		if($tab_name!="")
		{
			$queryMapping1 ="CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term_id." (
			`id` int(11) NOT NULL AUTO_INCREMENT,
		  	`post_id` int(11) NOT NULL,
		 	 PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

					$queryMapping2 ="ALTER TABLE `".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$term_id."` ADD `field_".$tab_fld_id."` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";

				$wpdb->query($queryMapping1);
				$wpdb->query($queryMapping2);
		}
		else 
		{
			$queryMapping1 ="CREATE TABLE IF NOT EXISTS ".$wpdb->prefix."xyz_cfl_field_values (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`post_id` int(11) NOT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

				$queryMapping2 ="ALTER TABLE `".$wpdb->prefix."xyz_cfl_field_values` ADD `field_".$tab_fld_id."` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL";

			$wpdb->query($queryMapping1);
			$wpdb->query($queryMapping2);
		}
		$l_field_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_group_id=%d",$xyz_cfl_field_group_id));
		$c=count($l_field_lists);
		$l_field_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_group_id=%d ORDER BY id DESC LIMIT 0,1",$xyz_cfl_field_group_id));
		
		foreach ($l_field_lists as $field_list)
		{
		 	$lid=$field_list->id;
			$lname=$field_list->xyz_cfl_field_name;
		}
	 	$last_ids_field_lists=$wpdb->get_results($wpdb->prepare("SELECT xyz_cfl_field_order FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_group_id=%d ",$xyz_cfl_field_group_id));
		$las_id=max($last_ids_field_lists);
		$ord=($las_id->xyz_cfl_field_order)-1;
		$p_field_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_field_order=%d AND xyz_cfl_group_id=%d",$ord,$xyz_cfl_field_group_id));
		$cnt_p_field_lists=count($p_field_lists);
		if($cnt_p_field_lists==0)
		{
			$pid=0;
		}
		else
		foreach ($p_field_lists as $field_list)
		{
			$pid=$field_list->id;
		}
		
		$str.='<div id="drag2_'.$lid.'" class="ui-widget-content" style="border:none;">
		<input type="hidden" id="group_id_hidden_'.$lid.'" name="group_id_hidden" value="'.$xyz_cfl_field_group_id.'">
		<table class="widefat  xyz_cfl_table"  style="width: 100%; margin: 0 auto; left:-50px;" class="edit_hover_class">
		<tbody id="clr_'.$lid.'"';
	    if($xyz_cfl_field_status==0)
		    $str.='class="xyz_cfl_dec"';
	    else 
		    $str.='class="xyz_cfl_ac"';
	    $str.='class="edit_hover_class">
		<tr class="edit_hover_class">
		<td scope="col" width="10%"></td>
		<td scope="col" width="10%" id="new_f_order_'.$lid.'">
		<span  class="xyz_cfl_circle" id="field_order_span_'.$lid.'">'.$xyz_cfl_field_order.'</span>
		</td>
		<td scope="col" width="30%" id="new_f_name_'.$lid.'">'.$xyz_cfl_field_name.'</td>
		<td scope="col" width="10%" id="new_f_type_'.$lid.'">'.$xyz_cfl_field_type.'</td>
		<td scope="col" width="10%" id="fld_stat_'.$lid.'"';
	    if($xyz_cfl_field_status==1){
	    $str.=' style="color: green;" ';
	    }else {
	    $str.=' style="color: red;"';
	    }
	    $str.='>';
		if($xyz_cfl_field_status==1)
			$str.='Active';
		else 
			$str.='Inactive';
		$str.='</td>
		<td scope="col" width="30%">
		<div id="field_edit_box_id_'.$lid.'" style="display:none;">
		
		<form method="post" name="field_edit_form" id="field_edit_form_'.$lid.'">
		<h2 style="text-align: center;">Update Field Details</h2>
		<table class="widefat  xyz_cfl_table" style="width: 100%; margin: 0 auto; ">
		<tr><td style="height: 5px;"></td></tr>
		<tr style="background-color: white; "><td>Field Name <span class="mandatory">*</span></td>
		<td>
		<input type="text" id="xyz_cfl_field_name_edit_'.$lid.'" name="xyz_cfl_field_name_edit" value="'.$xyz_cfl_field_name.'">
		<span class="mandatory"><blink><span id="field_name_er_msg_'.$lid.'"></span></blink></span>
		</td>
		</tr>
		<tr style="background-color: white; "><td>Field Type <span class="mandatory">*</span></td>
		<td>
		<select id="xyz_cfl_field_type_edit_'.$lid.'" name="xyz_cfl_field_type_edit" onchange="fieldtype_onchange_edit(this.value,'.$lid.')">
		';
		$field_type=$xyz_cfl_field_type;
		$str.='<option value ="" ';
		if($field_type=="") 
			$str.='selected="selected"';
		$str.='>Select</option>
		<option value ="Text Field" ';
		if($field_type=="Text Field") 
			$str.='selected="selected"';
		$str.='>Text Field</option>
		<option value ="Textarea" ';
		if($field_type=="Textarea") 
			$str.='selected="selected"';
		$str.='>Textarea</option>
		<option value ="Numeric" ';
		if($field_type=="Numeric") 
			$str.='selected="selected"';
		$str.='>Numeric</option>
		<option value ="Dropdown" ';
		if($field_type=="Dropdown") 
			$str.='selected="selected"';
		$str.='>Dropdown</option>
		</select>
		<span class="mandatory"><blink><span id="field_type_er_msg_'.$lid.'"></span></blink></span>
		</td>
		</tr>
		<tr style="display: none;" id="edit_plc_div_'.$lid.'"><td>Placeholder Text</td>
		<td>
		<input type="text" id="xyz_cfl_field_placeholder_edit_'.$lid.'" name="xyz_cfl_field_placeholder_edit" value="'.$xyz_cfl_field_placeholder.'">
		</td>
		</tr>
		<tr style="background-color: white; "><td>Required? </td>
		<td>
		<select id="xyz_cfl_field_mandatory_edit_'.$lid.'" name="xyz_cfl_field_mandatory_edit">
		<option value ="0" ';
		if($xyz_cfl_field_mandatory=="0") 
			$str.='selected="selected"';
		$str.='>Yes</option>
		<option value ="1" ';
		if($xyz_cfl_field_mandatory=="1") 
			$str.='selected="selected"';
		$str.='>No</option>
		</select>
		</td>
		</tr>
		<tr id="edit_dflt_div_'.$lid.'" style="background-color: white; display: none;"><td>Default Value</td>
		<td>
		<input type="text" id="xyz_cfl_field_default_edit_txt_'.$lid.'" name="xyz_cfl_field_default_edit_txt" value=""style="display: none;">
		<input type="text" id="xyz_cfl_field_default_edit_num_'.$lid.'" name="xyz_cfl_field_default_edit_num" style="display: none;" value="" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,\'\');">
		</td>
		</tr>
		<tr id="edit_option_div_'.$lid.'" style="background-color: white; display: none;"><td>Option Value <span class="mandatory">*</span></td>
		<td>
     	<textarea id="xyz_cfl_field_default_edit_drp_'.$lid.'" name="xyz_cfl_field_default_edit_drp" cols="18" rows="5" style="display: none;"></textarea>
		</td>
		</tr>
		<tr style="display: none;" id="edit_eg_msg_'.$lid.'">
		<td>&nbsp;</td>
		<td style="color: green; ">Eg1 : val1, val2, val3<br>Eg2 : key1:val1,key2:val2,key3:val3</td>
		</tr>
		<tr style="background-color: white; ">
		<td   id="bottomBorderNone">&nbsp;</td>
		<td   id="bottomBorderNone" style="height: 50px">
		<input type="button" id="update_fld_details_'.$lid.'" onclick="update_field_details('.$lid.');" class="submit_cfl_new" style=" margin-top: 10px; "  name="update_fld_details" value="Update" >
		</td>
		</tr>
		</table>
		</form>
		</div>
		<a href="#TB_inline?width=100&height=500&inlineId=field_edit_box_id_'.$lid.'" onclick="json_fld_info('.$lid.')" class="thickbox">
		<img  class="img" title="Edit Field" src="'.plugins_url(XYZ_CFL_DIR."/admin/images/edit.png").'"/>
				</a>&nbsp;
		<a onclick="fld_delete('.$lid.')" class="fld_delete" style="cursor: pointer;" id="'.$lid.'" >
		<img class="img" title="Delete Field" src="'.plugins_url(XYZ_CFL_DIR."/admin/images/delete.png").'"/>
				</a>&nbsp;';
		if($xyz_cfl_field_status==0)	
		{	
			$str.='<a href="javascript:void(0)" onclick="set_status('.$lid.')" style="cursor: pointer;" ><span id="act_'.$lid.'" style="display: inline;">
			<img  name="statusimage_'.$lid.'" id="statusimage_'.$lid.'" class="img" title="Activate Field" src="'.plugins_url(XYZ_CFL_DIR."/admin/images/activate.png").'">
					</span></a>';
			$str.='<img id="loading_fld_act_'.$lid.'" style="display: none;" src="'.plugins_url(XYZ_CFL_DIR."/admin/images/loading.gif").'"/>';
			
		}else if($xyz_cfl_field_status==1)
		{
			$str.='<a href="javascript:void(0)" onclick="set_status('.$lid.')" style="cursor: pointer;" ><span id="deact_'.$lid.'" style="display: inline;">
			<img  name="statusimage_'.$lid.'" id="statusimage_'.$lid.'" class="img" title="Deactivate Field" src="'.plugins_url(XYZ_CFL_DIR."/admin/images/blocked.png").'">
			</span></a>';		
			$str.='<img id="loading_fld_act_'.$lid.'" style="display: none;" src="'.plugins_url(XYZ_CFL_DIR."/admin/images/loading.gif").'"/>';
		}	
		$str.='</td>
		</tr>
		</tbody>
		</table>
		</div>';
		
		echo $pid.'{explode_0090}'.$str.'{explode_0090}'.$c;
	}
	die;
}

add_action('wp_ajax_xyz_cfl_updt_fld_grp', 'xyz_cfl_updt_fld_grp');
function xyz_cfl_updt_fld_grp()
{
	global $wpdb;
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	$erf="";
	$ms1="";
	$xyz_cfl_field_group_id_hidden=$_POST['xyz_cfl_field_group_id_hidden'];
	$xyz_cfl_field_group_name_edit=$_POST['xyz_cfl_field_group_name_edit'];
	$xyz_cfl_field_post_type_hidden=$_POST['xyz_cfl_field_post_type_hidden'];
	$field_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_group WHERE id!=%d AND xyz_cfl_group_name=%s AND xyz_cfl_group_post_type=%s",array($xyz_cfl_field_group_id_hidden,$xyz_cfl_field_group_name_edit,$xyz_cfl_field_post_type_hidden)));
	if($xyz_cfl_field_group_name_edit=="")
	{
		$ms1="Please fill field group name.";
		$erf=1;
	}
	else if(count($field_lists)>0)
	{
		$ms1="Field group name already exist.";
		$erf=1;
	}
	else
	{
		$erf=0;
		$update_name = $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_cfl_group SET xyz_cfl_group_name=%s WHERE id=%d",$xyz_cfl_field_group_name_edit,$xyz_cfl_field_group_id_hidden));
	}
	echo $ms1;
	die;
}

add_action('wp_ajax_xyz_cfl_updt_fld_details', 'xyz_cfl_updt_fld_details');
function xyz_cfl_updt_fld_details()
{
	global $wpdb;
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	$erf="";
	$ms1="";
	$xyz_cfl_field_id=$_POST['id'];
	$xyz_cfl_field_grp_id_hid=$_POST['xyz_cfl_field_grp_id_hid'];
	$xyz_cfl_field_name_edit=$_POST['xyz_cfl_field_name_edit'];
	$xyz_cfl_field_type_edit=$_POST['xyz_cfl_field_type_edit'];
	$xyz_cfl_field_placeholder_edit=$_POST['xyz_cfl_field_placeholder_edit'];
	$xyz_cfl_field_default_edit=$_POST['xyz_cfl_field_default_edit'];
	$xyz_cfl_field_mandatory_edit=$_POST['xyz_cfl_field_mandatory_edit'];
	$field_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE id!=%d AND xyz_cfl_field_name=%s AND xyz_cfl_group_id=%d",array($xyz_cfl_field_id,$xyz_cfl_field_name_edit,$xyz_cfl_field_grp_id_hid)));
	if($xyz_cfl_field_name_edit=="")
	{
		$erf=1;
		echo "er1";
	}
	else if(count($field_lists)>0)
	{
		$erf=1;
		echo "er2";
	}
	else if($xyz_cfl_field_type_edit=="")
	{
		$erf=1;
		echo "er3";
	}
	else if(($xyz_cfl_field_type_edit=="Dropdown" || $xyz_cfl_field_mandatory_edit==0) && $xyz_cfl_field_default_edit=="")
	{
		echo "mand_check";
		$erf=1;
	}
	else
	{
		$erf=0;
		$update_details = $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."xyz_cfl_fields SET xyz_cfl_field_name=%s,xyz_cfl_field_type=%s,xyz_cfl_field_placeholder=%s,xyz_cfl_field_default=%s,xyz_cfl_field_mandatory=%d WHERE id=%d",$xyz_cfl_field_name_edit,$xyz_cfl_field_type_edit,$xyz_cfl_field_placeholder_edit,$xyz_cfl_field_default_edit,$xyz_cfl_field_mandatory_edit,$xyz_cfl_field_id));
	}
	die;
}

add_action('wp_ajax_xyz_cfl_json_grp_edit', 'xyz_cfl_json_grp_updt');
function xyz_cfl_json_grp_updt()
{
	global $wpdb;
	$id=$_POST['id'];
	$field_group_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_group WHERE id=%s ",$id));
	foreach( $field_group_lists as $field_group_list )
	{
		$group_name=$field_group_list->xyz_cfl_group_name;
		$grp_details=array('group_name'=>$group_name);
		$json=json_encode($grp_details);
		echo $json;
	}
	die;
}

add_action('wp_ajax_xyz_cfl_json_fld_edit', 'xyz_cfl_json_fld_updt');
function xyz_cfl_json_fld_updt()
{
	global $wpdb;
	$id=$_POST['id'];
	$field_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE id=%d ORDER BY xyz_cfl_field_order ASC",$id));
	foreach( $field_lists as $field_list )
	{
		$field_name=$field_list->xyz_cfl_field_name;
		$field_type=$field_list->xyz_cfl_field_type;
		$field_placeholder=$field_list->xyz_cfl_field_placeholder;
		$field_default=$field_list->xyz_cfl_field_default;
		$field_mandatory=$field_list->xyz_cfl_field_mandatory;
		$field_grp_id=$field_list->xyz_cfl_group_id;
		$field_id=$field_list->id;
		$field_order=$field_list->xyz_cfl_field_order;
		$field_status=$field_list->xyz_cfl_field_status;
		$fld_details=array('field_name'=>$field_name, 'field_type'=>$field_type, 'placeholder'=>$field_placeholder,'defaulttxt'=> $field_default,'mandatory'=>$field_mandatory);
		$json=json_encode($fld_details);
		echo $json;
	}
	die;
}

add_action('wp_ajax_xyz_cfl_set_count', 'xyz_cfl_set_count');
function xyz_cfl_set_count()
{
	global $wpdb;
	$val=	$_POST['value'] ;
	$checked_field_lists_cnt=$wpdb->get_results($wpdb->prepare("SELECT id,xyz_cfl_group_name FROM ".$wpdb->prefix."xyz_cfl_group WHERE xyz_cfl_group_taxonomy=%s ",$val));
	$cnt_checked_field_lists=count($checked_field_lists_cnt);
	echo $cnt_checked_field_lists;
	die;
}

add_action('wp_ajax_xyz_cfl_ajax_backlink', 'xyz_cfl_ajax_backlink');
function xyz_cfl_ajax_backlink() 
{
	global $wpdb;
	if($_POST)
	{
		update_option('xyz_credit_link','cfl');
	}
	die();
}
?>