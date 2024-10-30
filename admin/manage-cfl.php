<?php 
global $wpdb;

$args=array(
		'public'   => true,
		'_builtin' => false
);
$output = 'names'; // names or objects, note names is the default
$operator = 'and'; // 'and' or 'or'
$post_types=get_post_types($args,$output,$operator);
$errMessage='';
$xyz_cfl_grpMessage = '';
if(isset($_GET['msg']))
	$xyz_cfl_grpMessage = $_GET['msg'];

if($xyz_cfl_grpMessage==1)
{
	?>
	<div class="system_notice_area_style1" id="system_notice_area">
	New Field Group added successfully.!
	&nbsp;&nbsp;&nbsp;<span id="system_notice_area_dismiss">Dismiss</span>
	</div>
	<script type="text/javascript">
		jQuery('#system_notice_area_dismiss').click(function() {
			   window.history.pushState('obj', '', 'admin.php?page=custom-field-manager-customfields');
			   return false;
			});
	</script>
	<?php
}
else if($xyz_cfl_grpMessage==2)
{
	if(isset($_GET['err']))
		$errMessage = $_GET['err'];
	if($errMessage=="err1")
		$xyz_cfl_errMessage="Please fill field group name.";
	if($errMessage=="err2")
		$xyz_cfl_errMessage="Please select term taxonomy.";
	if($errMessage=="err3")
		$xyz_cfl_errMessage="Field group name already exist.";
	?>
	<div class="system_notice_area_style0" id="system_notice_area">
	<?php echo $xyz_cfl_errMessage;?>
	&nbsp;&nbsp;&nbsp;<span id="system_notice_area_dismiss">Dismiss</span>
	</div>
	<script type="text/javascript">
	jQuery('#system_notice_area_dismiss').click(function() {
	window.history.pushState('obj', '', 'admin.php?page=custom-field-manager-customfields');
	return false;
	});
	</script>
	<?php
}
?>

<fieldset style="margin-top:20px; width: 99%; border: 1px solid #F7F7F7; padding: 10px 0px;">
  <legend>
		<span class="xyz_cfl_h2">Manage Custom Field Groups</span>
  </legend>
  <div style="margin-top:20px;  margin-bottom: 25px; ">
 	 <a id="xyz_cfl_add_field_group" onclick="add_grp_new() " class="xyz-button thickbox" style="color: white;" href="#TB_inline?width=100&height=400&inlineId=add_new_field_grp" >+ Add Field Group</a>
  </div>
  
  <form method="post" name="manage" id="manage">
	<table class="widefat  xyz_cfl_table" style="width: 100%; margin: 0 auto; border-bottom:none;">
	<tr><td colspan="3" style="height: 5px;"></td></tr>
	
 	<tr>
    	<td width="20%" style="font-size: 14px;">Choose Post Type</td>
    	<td width="25%">
    		<select id="xyz_cfl_post_type_sel" name="xyz_cfl_post_type_sel" onchange="grp_dropdown_post_type_select_init(this.value,'')">
    			<option value ="post">post</option> 
    			<?php 
    			$post_type_sel="post";
			    	foreach( $post_types as $post_type ) {
			    		 if(isset($_POST['xyz_cfl_post_type_sel']))
				    		$post_type_sel=$_POST['xyz_cfl_post_type_sel'];
				    	 else if(isset($_GET['posttype']))
			    			$post_type_sel=$_GET['posttype'];
    			?>
				<option value ="<?php echo $post_type;?>"<?php if($post_type ==$post_type_sel) echo "selected";?>><?php  echo $post_type; ?></option>
				<?php }?>
			</select>
			<img id="loading_posttype_sel" style="display: none;" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/loading.gif")?>"/>
		
		</td>
	</tr>
	<tr>
		<td width="20%" style="font-size: 14px;">Choose Taxonomy</td>
		
		<td width="25%"><p id="null_term_init" style="display: none;">NA</p>
		<input type="hidden" name="zeroval" id="zeroval" value="">
		<?php 
			$taxonomies = get_object_taxonomies($post_type_sel,'objects');
			$cat='';
			if(isset($_POST['xyz_cfl_taxonomy_init']))
				$cat=$_POST['xyz_cfl_taxonomy_init'];
			else if(isset($_GET['taxonomy']))
				$cat=$_GET['taxonomy'];
			?>
			<select id="xyz_cfl_taxonomy_init"  name="xyz_cfl_taxonomy_init" onchange="dropdown_taxonomy_select_init(this.value,'')">
			<option value=""<?php if($cat==""){ ?> selected="selected" <?php } ?>>Select</option>
			<?php 	foreach ($taxonomies as $key=>$values)
			{
			if($taxonomies[$key]->hierarchical==1){
			?><option value ="<?php echo $key; ?>"<?php if($cat==$key)	{?> selected="selected"<?php }?>><?php echo $key;?></option>
			<?php } }?>			
			</select>
			<img id="loading_taxonomy_init" style="display: none;" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/loading.gif")?>"/>
		
		</td>
	</tr>
	<tr >
		<td width="20%" id="tax_id_sel_span_init_hd" style="font-size: 14px;">Choose Taxonomy Term</td>
		<td width="25%" id="tax_id_sel_span_init"><p id="null_term_id_init" style="display: none;">NA</p>
		<select id="xyz_cfl_taxonomy_term_id_init" name="xyz_cfl_taxonomy_term_id_init" >
		<?php 
		$pid=0;$i=0;$cat_id="";$term="";
		
		if(isset($_POST['xyz_cfl_taxonomy_term_id_init']))
			$term=$_POST['xyz_cfl_taxonomy_term_id_init'];
		else if(isset($_GET['term']))
			$term=$_GET['term'];
		echo xyz_cfl_get_category_display($pid,$i,$cat_id,$cat,$term);
		?>
		</select>
		</td>
	</tr>
	<tr>
		<td width="20%">&nbsp;</td>
		<td width="25%">
			<input type="submit" id="submit_fld_grp" class="xyz-button"	name="submit_fld_grp" value="Display Field Group" />
		</td>
  	</tr>
  	
  	<tr><td colspan="3" style="height: 3px;"></td></tr>
  </table>
</form>
<div style="height: 1px;"></div>
<div class="field_meta">
 
<?php 
$term_selectedOptions="";$xyz_cfl_taxonomy_init="";$selected_taxonomies="";$fl_for_taxsel=0;$notselected_taxonomies="";
if(isset($_POST['submit_fld_grp']))
{
	$zeroval="";$fl_for_taxsel=1;$pid=0;$i=0;
	$xyz_cfl_post_type_sel=$_POST['xyz_cfl_post_type_sel'];
	
	if(isset($_POST['xyz_cfl_taxonomy_init']))
	{
		$xyz_cfl_taxonomy_init=$_POST['xyz_cfl_taxonomy_init'];
		if(isset($_POST['xyz_cfl_taxonomy_term_id_init']))
		{
			if($xyz_cfl_taxonomy_init!="")
				$term_selectedOptions=$_POST['xyz_cfl_taxonomy_term_id_init'];
		}
	}
	if(isset($_POST['zeroval']))
	{
		$zeroval=$_POST['zeroval'];
		if($zeroval==1)
			$term_selectedOptions="";
	}
	?>
	<script type="text/javascript">
	var fla_g="<?php echo $fl_for_taxsel;?>";
	var chckid="<?php echo $term_selectedOptions;?>";
	jQuery('#xyz_cfl_taxonomy_term_id_init').val(chckid);
	var x = document.getElementById("xyz_cfl_taxonomy_init").length;
	if(x==1)
	{
		jQuery('#null_term_init').show();
		jQuery('#xyz_cfl_taxonomy_init').hide();
	}
	</script>
	<?php 
}
else 
{
	$xyz_cfl_post_type_sel=$post_type_sel;
	$xyz_cfl_taxonomy_init="";
}

if(isset($_POST['add_fld_grp']))
{
	$flag_for_tid=0;
	$xyz_cfl_field_group_name_inrst=$_POST['xyz_cfl_field_group_name'];
	$xyz_cfl_post_type_insrt=$_POST['xyz_cfl_post_type'];
	$xyz_cfl_taxonomy_insrt=$_POST['xyz_cfl_taxonomy'];
	$xyz_cfl_taxonomy_term_id_insrt='';
	if($xyz_cfl_taxonomy_insrt!='')
		$xyz_cfl_taxonomy_term_id_insrt=$_POST['xyz_cfl_taxonomy_term_id'];
	$xyz_cfl_status_insrt=0;
	
	if($xyz_cfl_field_group_name_inrst=="")
	{
		header("Location:".admin_url('admin.php?page=custom-field-manager-customfields&posttype='.$xyz_cfl_post_type_insrt.'&taxonomy='.$xyz_cfl_taxonomy_insrt.'&term='.$xyz_cfl_taxonomy_term_id_insrt.'&msg=2&err=err1'));
	}
	else
	{
		$field_lists_insrt=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_group WHERE xyz_cfl_group_name=%s AND xyz_cfl_group_post_type=%s",array($xyz_cfl_field_group_name_inrst,$xyz_cfl_post_type_insrt)));
		if(count($field_lists_insrt)>0)
		{
			header("Location:".admin_url('admin.php?page=custom-field-manager-customfields&posttype='.$xyz_cfl_post_type_insrt.'&taxonomy='.$xyz_cfl_taxonomy_insrt.'&term='.$xyz_cfl_taxonomy_term_id_insrt.'&msg=2&err=err3'));
		}
		else
		{
			if($xyz_cfl_taxonomy_insrt=="")
			{
				$order_val=$wpdb->get_results($wpdb->prepare("SELECT xyz_cfl_group_order FROM ".$wpdb->prefix."xyz_cfl_group WHERE xyz_cfl_group_post_type=%s AND xyz_cfl_group_taxonomy=%s",$xyz_cfl_post_type_insrt,$xyz_cfl_taxonomy_insrt));
				if(count($order_val)!=0)
				{
					$max_order_vals=(max($order_val));
					foreach( $max_order_vals as $max_order_val )
					{
						$xyz_cfl_order=$max_order_val+1;
					}
				}
				else
					$xyz_cfl_order=1;
			}
			else
			{
				if($xyz_cfl_taxonomy_term_id_insrt=="")
				{
					$flag_for_tid=1;
					header("Location:".admin_url('admin.php?page=custom-field-manager-customfields&posttype='.$xyz_cfl_post_type_insrt.'&taxonomy='.$xyz_cfl_taxonomy_insrt.'&term='.$xyz_cfl_taxonomy_term_id_insrt.'&msg=2&err=err2'));
				}
				$order_val=$wpdb->get_results($wpdb->prepare("SELECT xyz_cfl_group_order FROM ".$wpdb->prefix."xyz_cfl_group WHERE xyz_cfl_group_post_type=%s AND xyz_cfl_group_taxonomy=%s AND xyz_cfl_group_taxonomy_term_id=%s",$xyz_cfl_post_type_insrt,$xyz_cfl_taxonomy_insrt,$xyz_cfl_taxonomy_term_id_insrt));
	
				if(count($order_val)!=0)
				{
					$max_order_vals=(max($order_val));
					foreach( $max_order_vals as $max_order_val )
					{
						$xyz_cfl_order=$max_order_val+1;
					}
				}
				else
					$xyz_cfl_order=1;
			}
			if($flag_for_tid==0)
			{
				$wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."xyz_cfl_group
						(`xyz_cfl_group_name`, `xyz_cfl_group_post_type`, `xyz_cfl_group_taxonomy`, `xyz_cfl_group_taxonomy_term_id`, `xyz_cfl_group_order`, `xyz_cfl_group_status`)
						VALUES ('%s','%s','%s',%d,%d,%d)",$xyz_cfl_field_group_name_inrst, $xyz_cfl_post_type_insrt, $xyz_cfl_taxonomy_insrt, $xyz_cfl_taxonomy_term_id_insrt, $xyz_cfl_order, $xyz_cfl_status_insrt));
				header("Location:".admin_url('admin.php?page=custom-field-manager-customfields&posttype='.$xyz_cfl_post_type_insrt.'&taxonomy='.$xyz_cfl_taxonomy_insrt.'&term='.$xyz_cfl_taxonomy_term_id_insrt.'&msg=1'));
			}
		}
	}
}
				
if(isset($_GET['taxonomy']))
	$xyz_cfl_taxonomy_init=$_GET['taxonomy'];

if(isset($_GET['term']))
	$term_selectedOptions=$_GET['term'];
			
$parnts="";
$ts=$xyz_cfl_taxonomy_init;$par=1;$catval="";
$selectd_termid=$term_selectedOptions;
$t_id=$term_selectedOptions;
if($t_id=="")
{
	$count_for_max=1;
	$split[0]="";
}
$max_array="";
if($t_id!=0)
{
	while ($par==1 ) 
	{
		$get_parent=$wpdb->get_row($wpdb->prepare("SELECT `parent`,`term_id` FROM `".$wpdb->prefix."term_taxonomy` WHERE `term_id` =%d AND taxonomy=%s",$t_id,$ts));
		$pi= $get_parent->parent;
		if($pi==0)
		{
			$par=0;
			$catval.=$get_parent->term_id."{explode_009}";
		}
		else
		{
			$catval.=$get_parent->term_id."{explode_009}";
			$t_id=$pi;
		}
	}
	$catval=substr_replace($catval, '',-13,13);
	$split=explode('{explode_009}', $catval);
	$count_for_max=count($split);
}
	?>
<div id="add_new_field_grp" class="add_new_field_grp" style="display: none;">
<?php 
global $wpdb;
$ms1="";
$xyz_cfl_field_group_name="";
$xyz_cfl_post_type="";
$xyz_cfl_taxonomy="";
$xyz_cfl_taxonomy_term_id="";
$post_types=$xyz_cfl_post_type_sel;
?>
<form name="add_new_fld_grp_form" method="post" id="add_new_fld_grp_form" >
<h2 style="text-align: center;">Add New Field Group</h2>
		<table class="widefat xyz_cfl_table"  style="width: 100%; margin: 0 auto; left:-50px;">
			<tr>
			<td><div  style=" margin-top: 30px; ">Field Group Name <span class="mandatory">*</span></div>
				
			</td>
			<td>
				<input type="text" value="<?php echo $xyz_cfl_field_group_name;?>" id="xyz_cfl_field_group_name" name="xyz_cfl_field_group_name" style=" margin-top: 30px; ">
			</td>
		</tr>
		<tr>	
			<td>
				Post Type <span class="mandatory">*</span>
			</td>
			<td><?php $post_type_for_addgrp=get_post_types($args,$output,$operator); ?>
			<select id="xyz_cfl_post_type" name="xyz_cfl_post_type" onchange="grp_dropdown_post_type_select_for_addgrp(this.value,'')">
    			<option value ="post">post</option> 
    			<?php 
    			$ptype_for_grp_sel="post";
    			
			    	foreach( $post_type_for_addgrp as $ptype_for_grp ) {
			    		 if(isset($_POST['xyz_cfl_post_type']))
				    		$ptype_for_grp_sel=$_POST['xyz_cfl_post_type'];
    			?>
				<option value ="<?php echo $ptype_for_grp;?>"<?php if($ptype_for_grp ==$ptype_for_grp_sel) echo "selected";?>><?php  echo $ptype_for_grp; ?></option>
				<?php }?>
			</select>
			<img id="loading_posttype_sel_for_add" style="display: none;" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/loading.gif")?>"/>
		
			</td>
		</tr>
		
		 <tr id="tax_sel_span">
			<td>
				Taxonomy 
			</td>
			<td><p id="null_term" style="display: none;">NA</p>
			<?php 
			$taxonomies_pop = get_object_taxonomies($ptype_for_grp_sel,'objects');
			$cat_pop='';
			if(isset($_POST['xyz_cfl_taxonomy']))
				$cat_pop=$_POST['xyz_cfl_taxonomy'];
			?>
				<select id="xyz_cfl_taxonomy"  name="xyz_cfl_taxonomy" onchange="dropdown_taxonomy_select(this.value)" >
				<option value=""<?php if($cat_pop==""){ ?> selected="selected" <?php } ?>>Select</option>
				<?php 	foreach ($taxonomies_pop as $key=>$values)
				{
				if($taxonomies_pop[$key]->hierarchical==1){
				?><option value ="<?php echo $key; ?>"<?php if($cat_pop==$key)	{?> selected="selected"<?php }?>><?php echo $key;?></option>
				<?php } }?>	
				</select>
				<img id="loading" style="display: none; width:auto !important" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/loading.gif")?>"/>
			</td> 
		</tr> 
				
		<tr id="tax_id_sel_span">
			<td>
				Taxonomy Term 
			</td>
			<td><p id="null_term_id" style="display: none;">NA</p>
			<select id="xyz_cfl_taxonomy_term_id" name="xyz_cfl_taxonomy_term_id"  >
			
				</select>
			</td>
		</tr>
		<tr>
				<td id="bottomBorderNone">&nbsp;</td>
						<td   id="bottomBorderNone" style="height: 50px">
								<input type="submit" id="add_fld_grp" class="xyz-button"	style=" margin-top: 10px; "	name="add_fld_grp" value="Save" />
						</td>
					</tr>
		</table>
</form>
</div>
	<?php 		
for ($k=$count_for_max-1;$k>=0;$k--)
{	
	$flag=0;
	$tid=$split[$k];
	if($tid=="")
		$term_name="NA";
	else
	{
		$term_names=$wpdb->get_row($wpdb->prepare("SELECT `name` FROM `".$wpdb->prefix."terms` WHERE `term_id`=%d",$split[$k]));
		$term_name=$term_names->name;
	}
	$field_group_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_group WHERE xyz_cfl_group_post_type=%s AND xyz_cfl_group_taxonomy=%s AND xyz_cfl_group_taxonomy_term_id=%d ORDER BY xyz_cfl_group_order ASC",$xyz_cfl_post_type_sel,$xyz_cfl_taxonomy_init,$split[$k]));
	?>
	<span style="font-size: 13px; font-weight: bold;"><?php echo "TERM - ".$term_name;?></span>
	<table class="widefat  xyz_cfl_table"  style="width: 100%; margin: 0 auto; border-bottom:none;">
	 <thead style="background-color:  ">
		<tr >
			<th style="text-indent: 1px;color: #555555;" scope="col" width="10%">Group Order</th>
			<th scope="col" width="30%" style="color: #555555">Group Name</th>
			<th scope="col" width="10%" style="color: #555555">Fields</th>
			<th scope="col" width="10%" style="color: #555555">Status</th>
			<th scope="col" width="15%" style="color: #555555"></th>
			<th scope="col" width="20%" style="color: #555555">Action</th>
			<th scope="col" width="5%" style="color: #555555"></th>
		</tr>
	 </thead>
	</table>
	<?php
	if( count($field_group_lists)>0 )
	{
		?>
		<div id="draggable1" class="ui-widget-content"  style="border:none;">
		<?php
		
		$flag=1;
		$count=count($field_group_lists);
		$i=1;

		foreach( $field_group_lists as $field_group_list ) 
		{
			?>
			<div id="drag_<?php echo $field_group_list->id; ?>" class="ui-widget-content" style="border:none;">
			<table id="drag_table_<?php echo $field_group_list->id; ?>" class="widefat xyz_cfl_table"  style="width: 100%; margin: 0 auto; ">
			<tr id="bg_colr_<?php echo $field_group_list->id; ?>" style="cursor: move;">
			<td scope="col" width="10%">
			<span class="xyz_cfl_circle" id="order_span_<?php echo $field_group_list->id; ?>"><?php echo $field_group_list->xyz_cfl_group_order; ?></span>
			</td>
			<td scope="col" width="30%" id="group_name_<?php echo $field_group_list->id; ?>">
			<?php
			echo esc_html($field_group_list->xyz_cfl_group_name) ;
			?>
			</td>
			<td scope="col" width="10%" id="fld_count_<?php echo $field_group_list->id; ?>">
			<?php
			$grp_id=$field_group_list->id;
			$no_of_fields=$wpdb->get_results($wpdb->prepare("SELECT `id` FROM ".$wpdb->prefix."xyz_cfl_fields WHERE `xyz_cfl_group_id`=%d",$grp_id));
			$count=count($no_of_fields);
			echo $count;
			?>
			</td>
			<td scope="col" width="10%" id="grp_stat_<?php echo $field_group_list->id; ?>" <?php if($field_group_list->xyz_cfl_group_status==1){?>style="color: green;" <?php }else {?>style="color: red;" <?php }?>>
			<?php 
			if($field_group_list->xyz_cfl_group_status==1)
				echo "Active";
			else 
				echo "Inactive";
				
			?>
			</td><td scope="col" width="15%"></td>
			<td scope="col" width="20%">
			<div id="action_div_<?php echo $field_group_list->id; ?>" >
			<div id="edit_box_id_<?php echo $field_group_list->id; ?>" style="display:none;">
			
			<form method="post" name="edit_form" id="edit_form_<?php echo $field_group_list->id; ?>">
			<h2 style="text-align: center;" >Update Field Group Name</h2>
			<table class="widefat xyz_cfl_table" style="width: 100%; margin: 0 auto; ">
			<tr><td style="height: 5px;">
			</td></tr>
			<tr style="background-color: white; "><td>Name <span class="mandatory">*</span></td>
			<td>
			<input type="hidden" id="xyz_cfl_field_post_type_hidden_<?php echo $field_group_list->id ;?>" name="xyz_cfl_field_post_type_hidden" value="<?php echo $field_group_list->xyz_cfl_group_post_type ;?>">
			<input type="hidden" id="xyz_cfl_field_group_id_hidden_<?php echo $field_group_list->id ;?>" name="xyz_cfl_field_group_id_hidden" value="<?php echo $field_group_list->id ;?>">
			<input type="text" id="xyz_cfl_field_group_name_edit_<?php echo $field_group_list->id ;?>" name="xyz_cfl_field_group_name_edit" value="">
			<span class="mandatory"><blink><span id="er_msg_<?php echo $field_group_list->id ;?>"></span></blink></span>
			</td>
			</tr>
			<tr style="background-color: white; ">
			<td   id="bottomBorderNone">&nbsp;</td>
			<td   id="bottomBorderNone" style="height: 50px">
			<input type="button" id="update_fld_grp_<?php echo $field_group_list->id ;?>" onclick="update_fld_group(<?php echo $field_group_list->id ;?>);" class="submit_cfl_new" style=" margin-top: 10px; "  name="update_fld_grp" value="Update" >
			
			</td>
			</tr>
			</table>
			</form>
			
			</div>
			<a onclick="json_group_info(<?php echo $field_group_list->id; ?>)" href="#TB_inline?width=100&height=250&inlineId=edit_box_id_<?php echo $field_group_list->id; ?>" class="thickbox">
			<img class="img" title="Edit Field Group" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/edit.png")?>"/>
			</a>&nbsp;
			<script type="text/javascript">
				
			function json_group_info(id) 
			{
				var data ={
				action: 'xyz_cfl_json_grp_edit',
				id: id,
				};
			
				jQuery.post(ajaxurl, data, function(response) 
						{
					
							var json_x = jQuery.parseJSON(response);
							group_name=json_x.group_name;
							jQuery("#xyz_cfl_field_group_name_edit_"+id).val(group_name);
						});
			}
			</script>
			<a class="delete" onclick="grp_delete(<?php echo $field_group_list->id; ?>);" style="cursor: pointer;" id="grp_del_<?php echo $field_group_list->id; ?>" >
			<img class="img" title="Delete Field Group" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/delete.png")?>"/>
			</a>&nbsp;
			<?php
			$status=$field_group_list->xyz_cfl_group_status;
			$id=$field_group_list->id;
			if($status==0)
			{
			?>
			<a onclick="set_status_grp(<?php echo $field_group_list->id; ?>)" style="cursor: pointer;" href="javascript:void(0)"><span id="grp_act_<?php echo $field_group_list->id; ?>" style="display: inline;">
			<img  name='statusimage_<?php echo $field_group_list->id; ?>' id='statusimage_<?php echo $field_group_list->id; ?>' class="img" title="Activate Field Group" src="<?php echo plugins_url('images/activate.png',__FILE__);?>">
			</span></a>
			<?php
			} else if($status==1)
			{
			?>
			<a onclick="set_status_grp(<?php echo $field_group_list->id; ?>)" style="cursor: pointer;" href="javascript:void(0)">
			<span id="grp_act_<?php echo $field_group_list->id; ?>" style="display: inline;"><img name='statusimage_<?php echo $field_group_list->id; ?>' id='statusimage_<?php echo $field_group_list->id; ?>' class="img" title="Deactivate Field Group" src="<?php echo plugins_url('images/blocked.png',__FILE__);?>">
			</span></a>
			<?php
			}
			?>
			&nbsp;
			<a id="xyz_cfl_add_new_field" onclick="add_fld_new(<?php echo $field_group_list->id; ?>)" style="cursor: pointer;" href="#TB_inline?width=100&height=415&inlineId=add_new_field_form_<?php echo $field_group_list->id; ?>" class="thickbox">
			<img id="ad_fld_<?php echo $field_group_list->id; ?>" class="img" title="Add Field" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/add.png")?>"/>
			</a>
			<img id="loading_grp_act_<?php echo $field_group_list->id; ?>" style="display: none;" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/loading.gif")?>"/>
			</div>
			</td>
			<td scope="col" width="5%" id="arrow_actions_div_<?php echo $field_group_list->id; ?>" class="arrow_actions">
			<p id="down_arrow_<?php echo $field_group_list->id;?>" style="cursor: pointer; " onclick="clicky_arrow(<?php echo $field_group_list->id;?>)"><span title="View Fields" class="xyz_cfl_arrow_size">&#9660;</span></p>
			</td>
			</tr>
			</table>
			<div class="xyz_cfl_field_form_mask" id="field_form_mask_<?php echo $field_group_list->id; ?>">
			<div class="xyz_cfl_field_form" id="field_form_<?php echo $field_group_list->id; ?>">
			<table class="widefat xyz_cfl_table"  style="width: 100%; margin: 0 auto; left:-50px; background-color: #85B1CE;">
			<thead>
			<tr >
			<th scope="col" width="10%"></th>
			<th scope="col" width="10%">Field Order</th>
			<th scope="col" width="30%">Field Name</th>
			<th scope="col" width="10%">Field Type</th>
			<th scope="col" width="10%">Status</th>
			<th scope="col" width="30%">Action</th>
			</tr>
			</thead>
			</table>
				
			<div id="draggable2_<?php echo $field_group_list->id;?>" style="border:none;" >
			<div  id="draggable2" style="cursor: move;" class="ui-widget-content" style="border:none;" >
			<?php
			$group_id=$field_group_list->id;
			$field_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE xyz_cfl_group_id=%d ORDER BY xyz_cfl_field_order ASC",$group_id));
			if( count($field_lists)>0 )
			{
			$count=count($field_lists);
			$i=1;
			
			foreach( $field_lists as $field_list ) 
			{
				$set_deflt_txt="";
				$set_deflt_num="";
				$set_deflt_drp="";
				$field_name=$field_list->xyz_cfl_field_name;
				$field_type=$field_list->xyz_cfl_field_type;
				$field_placeholder=$field_list->xyz_cfl_field_placeholder;
				$field_default=$field_list->xyz_cfl_field_default;
				
				if($field_type=="Text Field")
					$set_deflt_txt=$field_default;
				else if($field_type=="Textarea")
					$set_deflt_txt=$field_default;
				else if($field_type=="Numeric")
					$set_deflt_num=$field_default;
				else if($field_type=="Dropdown")
					$set_deflt_drp=$field_default;
				
				$field_mandatory=$field_list->xyz_cfl_field_mandatory;
				$field_grp_id=$field_list->xyz_cfl_group_id;
				$field_id=$field_list->id;
				$field_order=$field_list->xyz_cfl_field_order;
				$field_status=$field_list->xyz_cfl_field_status;
					
				?>
				<div id="drag2_<?php echo $field_id; ?>" class="ui-widget-content" style="border:none;">
				<input type="hidden" id="group_id_hidden_<?php echo $field_id ;?>" name="group_id_hidden" value="<?php echo $field_grp_id ;?>">
				
				<table class="widefat xyz_cfl_table"  style="width: 100%; margin: 0 auto; left:-50px;" class='edit_hover_class' id="fld_clr_<?php echo $field_id ;?>">
				<tbody id="clr_<?php echo $field_id; ?>" <?php if($field_status==0){?>  class="xyz_cfl_dec" <?php }else {?> class="xyz_cfl_ac" <?php } ?>>
				<tr class='edit_hover_class'>
				<td scope="col" width="10%"></td>
				<td scope="col" width="10%" id="new_f_order_<?php echo $field_id ?>">
				<span  class="xyz_cfl_circle" id="field_order_span_<?php echo $field_id; ?>"><?php echo $field_order; ?></span>
				</td>
				<td scope="col" width="30%" id="new_f_name_<?php echo $field_id; ?>">
				<?php
				echo esc_html($field_name);
				?>
				</td>
				<td scope="col" width="10%" id="new_f_type_<?php echo $field_id; ?>">
				<?php
				echo $field_type;
				?>
				</td>
				<td scope="col" width="10%" id="fld_stat_<?php echo $field_id; ?>"  <?php if($field_status==1){?>style="color: green;" <?php }else {?>style="color: red;" <?php }?>>
				<?php 
				if($field_status==1)
					echo "Active";
				else 
					echo "Inactive";
				?>
				</td>
				
				<td scope="col" width="30%">
				<div id="field_edit_box_id_<?php echo $field_id; ?>" style="display:none;">
				
				<form method="post" name="field_edit_form" id="field_edit_form_<?php echo $field_id; ?>">
				<h2 style="text-align: center;">Update Field Details</h2>
				<table class="widefat xyz_cfl_table" style="width: 100%; margin: 0 auto; ">
				<tr><td style="height: 5px;"></td></tr>
				<tr style="background-color: white; "><td>Field Name <span class="mandatory">*</span></td>
				<td>
				<input type="text" id="xyz_cfl_field_name_edit_<?php echo $field_id ;?>" name="xyz_cfl_field_name_edit" value="<?php echo esc_html($field_name) ;?>">
				<span class="mandatory"><blink><span id="field_name_er_msg_<?php echo $field_id ;?>"></span></blink></span>
				</td>
				
				</tr>
				
				<tr style="background-color: white; "><td>Field Type <span class="mandatory">*</span></td>
				<td>
				<select id="xyz_cfl_field_type_edit_<?php echo $field_id ;?>" name="xyz_cfl_field_type_edit" onchange="fieldtype_onchange_edit(this.value,<?php echo $field_id ;?>)">
				<option value ="" <?php if($field_type=="") {?> selected="selected" <?php }?>>Select</option>
				<option value ="Text Field" <?php if($field_type=="Text Field") {?> selected="selected" <?php }?>>Text Field</option>
				<option value ="Textarea" <?php if($field_type=="Textarea") {?> selected="selected" <?php }?>>Textarea</option>
				<option value ="Numeric" <?php if($field_type=="Numeric") {?> selected="selected" <?php }?>>Numeric</option>
				<option value ="Dropdown" <?php if($field_type=="Dropdown") {?> selected="selected" <?php }?>>Dropdown</option>
				</select>
				<span class="mandatory"><blink><span id="field_type_er_msg_<?php echo $field_id ;?>"></span></blink></span>
				</td>
				
				</tr>
				
				<tr style="display: none;" id="edit_plc_div_<?php echo $field_id; ?>">
				<td>Placeholder Text</td>
				<td>
				<input  type="text" id="xyz_cfl_field_placeholder_edit_<?php echo $field_id ;?>" name="xyz_cfl_field_placeholder_edit" value="<?php echo esc_html($field_placeholder) ;?>" >
				</td>
				</tr>
				
				<tr style="background-color: white; "><td>Required? </td>
				<td>
				<select id="xyz_cfl_field_mandatory_edit_<?php echo $field_id ;?>" name="xyz_cfl_field_mandatory_edit">
				<option value ="0" <?php if($field_mandatory=="0") {?> selected="selected" <?php }?>>Yes</option>
				<option value ="1" <?php if($field_mandatory=="1") {?> selected="selected" <?php }?>>No</option>
				</select>
				</td>
				</tr>
				
				<tr id="edit_dflt_div_<?php echo $field_id; ?>"style="background-color: white; display: none;"><td>Default Value</td>
				<td>
				<input type="text" id="xyz_cfl_field_default_edit_txt_<?php echo $field_id ;?>" name="xyz_cfl_field_default_edit_txt" value="<?php echo $set_deflt_txt ;?>" style="display: none;">
				<input type="text" id="xyz_cfl_field_default_edit_num_<?php echo $field_id ;?>" name="xyz_cfl_field_default_edit_num" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" style="display: none;" value="<?php echo $set_deflt_num ;?>">
				
				</td>
				</tr>
				<tr id="edit_option_div_<?php echo $field_id; ?>"style="background-color: white; display: none;"><td>Option Value <span class="mandatory">*</span></td>
				<td>
				<textarea id="xyz_cfl_field_default_edit_drp_<?php echo $field_id; ?>" name="xyz_cfl_field_default_edit_drp" cols="18" rows="5" style="display: none;"><?php echo $set_deflt_drp ;?></textarea>
				</td>
				</tr>
				<tr style="display: none;" id="edit_eg_msg_<?php echo $field_id; ?>">
				<td>&nbsp;</td>
				<td style="color: green; ">Eg1 : val1, val2, val3<br>Eg2 : key1:val1,key2:val2,key3:val3</td>
				</tr>
				
				
				
				<tr style="background-color: white; ">
				<td   id="bottomBorderNone">&nbsp;</td>
				<td   id="bottomBorderNone" style="height: 50px">
				<input type="button" id="update_fld_details_<?php echo $field_id ;?>" onclick="update_field_details(<?php echo $field_id ;?>);" class="submit_cfl_new" style=" margin-top: 10px; "  name="update_fld_details" value="Update" >
				
				</td>
				</tr>
				</table>
				</form>
				
				</div>
				
				
				<a href="#TB_inline?width=100&height=500&inlineId=field_edit_box_id_<?php echo $field_id; ?>" onclick="json_fld_info(<?php echo $field_id; ?>)"  class="thickbox">
				<img  class="img" title="Edit Field" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/edit.png")?>"/>
				</a>&nbsp;
				<a onclick="fld_delete(<?php echo $field_id; ?>)" class="fld_delete" style="cursor: pointer;" id="fld_del_<?php echo $field_id; ?>" >
				<img class="img" title="Delete Field" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/delete.png")?>"/>
				</a>&nbsp;
				
				<?php
				$status=$field_status;
				if($status==0)
				{
					?>
					<a href="javascript:void(0)" onclick="set_status(<?php echo $field_id; ?>)" style="cursor: pointer; " ><span id="act_<?php echo $field_id; ?>" style="display:inline;">
					<img  name='statusimage_<?php echo $field_id; ?>' id='statusimage_<?php echo $field_id; ?>' class="img" title="Activate Field" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/activate.png")?>"/>
					</span></a>
					<img id="loading_fld_act_<?php echo $field_id; ?>" style="display: none;" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/loading.gif")?>"/>
					<?php
				} 
				else if($status==1)
				{
					?>
					<a href="javascript:void(0)" onclick="set_status(<?php echo $field_id; ?>)" style="cursor: pointer;" ><span id="act_<?php echo $field_id; ?>" style="display: inline;">
					<img name='statusimage_<?php echo $field_id; ?>' id='statusimage_<?php echo $field_id; ?>' class="img" title="Deactivate Field" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/blocked.png")?>"/>
					</span></a>
					<img id="loading_fld_act_<?php echo $field_id; ?>" style="display: none;" src="<?php echo plugins_url(XYZ_CFL_DIR."/admin/images/loading.gif")?>"/>
					<?php
				}
				?>
				</td>
				</tr>
				</tbody>
				</table>
				</div>
				<?php
			}
		}
		else
		{
			?>
			<table class="widefat xyz_cfl_table" style="width: 100%; margin: 0 auto; "  id="no_fld_tbl_<?php echo $field_group_list->id; ?>">
			<tr class="no_fields_message" id="no_fields_message_<?php echo $field_group_list->id; ?>">
			<td colspan="5">No records found.
			</td>
			</tr>
			</table>
			<?php
		}
		?>
				
		</div>
		</div>
		
		<div id="add_new_field_<?php echo $field_group_list->id; ?>" class="add_new_field" style="display: none;">
		<form method="post" name="add_new_field_form" id="add_new_field_form_<?php echo $field_group_list->id; ?>" style="display: none;">
		<h2 style="text-align: center;">Add New Field</h2>
		<table class="widefat xyz_cfl_table"  style="width: 100%; margin: 0 auto; left:-50px;">
		<tr><td>
		<input type="hidden" id="xyz_cfl_field_group_id_hidden" name="xyz_cfl_field_group_id_hidden" value="<?php echo $field_group_list->id; ?>">
		</td></tr>
		<tr>
		<td>Field Name <span class="mandatory">*</span></td>
		<td><input type="text" id="xyz_cfl_field_name_<?php echo $field_group_list->id; ?>" name="xyz_cfl_field_name"></td>
		</tr>
		<tr>
		<td>Field Type <span class="mandatory">*</span></td>
		<td>
		<select id="xyz_cfl_field_type_<?php echo $field_group_list->id; ?>" name="xyz_cfl_field_type"  onchange="fieldtype_onchange_add(this.value,<?php echo $field_group_list->id; ?>)">
		<option value ="" >Select</option>
		<option value ="Text Field" >Text Field</option>
		<option value ="Textarea" >Textarea</option>
		<option value ="Numeric" >Numeric</option>
		<option value ="Dropdown" >Dropdown</option>
		</select></td>
		</tr>
		<tr style="display: none;" id="plc_div_<?php echo $field_group_list->id; ?>">
		<td>Placeholder Text</td>
		<td><input type="text" id="xyz_cfl_field_placeholder_<?php echo $field_group_list->id; ?>"  name="xyz_cfl_field_placeholder"></td>
		</tr>
		
		<tr>
		<td>Required?</td>
		<td>
		<select id="xyz_cfl_field_mandatory_<?php echo $field_group_list->id; ?>" name="xyz_cfl_field_mandatory">
		<option value ="0" >Yes</option>
		<option value ="1" selected="selected" >No</option>
		</select>
		</td>
		</tr>
		
		<tr style="display: none;" id="dflt_div_<?php echo $field_group_list->id; ?>">
		<td>Default Value</td>
		<td>
		<input type="text" id="xyz_cfl_field_default_txt_<?php echo $field_group_list->id; ?>" name="xyz_cfl_field_default_txt" style="display: none;">
		<input type="text" id="xyz_cfl_field_default_num_<?php echo $field_group_list->id; ?>" name="xyz_cfl_field_default_num" onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');" style="display: none;">
		</td>
		</tr>
		<tr style="display: none;" id="option_div_<?php echo $field_group_list->id; ?>">
		<td>Option Value <span class="mandatory">*</span></td>
		<td>
		<textarea id="xyz_cfl_field_default_drp_<?php echo $field_group_list->id; ?>" name="xyz_cfl_field_default_drp" cols="18" rows="5" style="display: none;"></textarea>
		</td>
		</tr>
		<tr style="display: none;" id="eg_msg_<?php echo $field_group_list->id; ?>">
		<td>&nbsp;</td>
		<td style="color: green; ">Eg1 : val1, val2, val3<br>Eg2 : key1:val1,key2:val2,key3:val3</td>
		</tr>

		<tr>
		<td id="bottomBorderNone">&nbsp;</td>
		<td id="bottomBorderNone" style="height: 50px">
		<input type="button" id="submit_cfl_new_field_<?php echo $field_group_list->id; ?>" onclick="save_hide_fld_div(<?php echo $field_group_list->id; ?>);" class="xyz-button" style="margin-top: 10px;" name="submit_cfl_new_field" value="Save" />
		</td>
		</tr>
		</table>
		</form>
		</div>
		
		<table class="widefat xyz_cfl_table"  style="width: 100%; margin: 0 auto; left:-50px;">
			<tr class="table_footer">
				<td>
					<div id="grp_drg_msg_<?php echo $field_group_list->id; ?>"  <?php if( count($field_lists)>1 && $selectd_termid==$split[$k] ){?>class="order_message" style="width: 200px"<?php }else {?>style="width: 273px" <?php }?>>&nbsp;&nbsp;
					<?php if( count($field_lists)>1 && $selectd_termid==$split[$k])
					{?>
					Drag and drop to reorder
					<?php }?></div>
				</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
		
		</div>
		</div>
		</div>
			
		<?php 
		}
		?>
		</div>
			<table <?php if($selectd_termid==$split[$k]&&count($field_group_lists)>0){  ?> style="width: 100%; margin: 0 auto; " class="widefat xyz_cfl_table"<?php  } else {?> style="display: none; " <?php } ?>>
			<tr class="table_footer">
				<td>
					<div id="drg_msg" <?php if(count($field_group_lists)>1){ ?>class="order_message_grp" <?php }?> style="width: 273px">&nbsp;&nbsp;<?php 
					if(count($field_group_lists)>1)
						echo "Drag and drop to reorder";
					?>
					</div>
				</td>
			</tr>
		</table>
		<?php
		
	}
	

	if($flag==0)
	{
		?>
		<table class="widefat xyz_cfl_table" style="width: 100%; margin: 0 auto; " id="no_grp_tbl_<?php echo $field_group_list->id ;?>">
		<tr>
		<td colspan="5">No records found.
		</td>
		</tr>
		</table>
		<?php if($selectd_termid==$split[$k]){
			?>
		<table style="width: 100%; margin: 0 auto; " class="widefat xyz_cfl_table">
				<tr class="table_footer">
					<td>
						<div <?php if(count($field_group_lists)>1){ ?>class="order_message_grp" <?php }?> style="width: 273px">&nbsp;&nbsp;
						
						</div>
					</td>
				</tr>
		</table>
		<?php
		}
	}
}
?>

			
</div>
<div style="margin-top:20px;  margin-bottom: 25px; ">
 		 <a id="xyz_cfl_add_field_group" onclick="add_grp_new() " class="xyz-button thickbox" style="color: white;" href="#TB_inline?width=100&height=400&inlineId=add_new_field_grp" >+ Add Field Group</a>
</div>
</fieldset>	

	
<script type="text/javascript">
	    jQuery(document).ready(function() 
  				{

					var err_for_shw_add_grp_div="<?php echo $errMessage;?>";
					jQuery(function() 
					{
						jQuery("#draggable1 ").sortable(
						{ 
							opacity: 0.5, cursor: 'move', update: function() 
							{
						
								var order1 = jQuery(this).sortable("serialize") ; 
								var dataString = { 
													action: 'xyz_cfl_drag_n_drop1', 
													neworder: order1,
												 };
								 
							
								jQuery.post(ajaxurl, dataString, function(response) 
								{
									
									response=jQuery.trim(response);
									var res=response.split(",");
										var j=1;
										for(var i=0;i<res.length;i++)
										{
											jQuery("#order_span_"+res[i]).html(j);
											j++;
										};
								});
							}
						});	

						jQuery("#draggable2 ").sortable(
								{ 
									opacity: 0.5, cursor: 'move', update: function() 
									{
								
										var fld_order1 = jQuery(this).sortable("serialize") ; 
										var dataString = { 
															action: 'xyz_cfl_drag_n_drop2', 
															fld_neworder: fld_order1,
														 };
										 
									
										jQuery.post(ajaxurl, dataString, function(response) 
										{
											response=jQuery.trim(response);
											var res=response.split(",");
											
												var j=1;
												for(var i=0;i<res.length;i++)
												{
													jQuery("#field_order_span_"+res[i]).html(j);
													j++;
												};
										});
									}
								});					
					});	

					jQuery("#add_fld_grp").click(function(){
						
						if(jQuery("#xyz_cfl_field_group_name").val()=="")
					    {	
						    alert("Please fill field group name.");
						    jQuery("#xyz_cfl_field_group_name").focus();
					     	return false;
					    }
						else if(jQuery("#xyz_cfl_post_type").val()=="")
					    {	
						    alert("Please select post type.");
						    jQuery("#xyz_cfl_post_type").focus();
					     	return false;
					    }
						else if(jQuery("#xyz_cfl_taxonomy").val()!="" && jQuery("#null_term_id").is(':visible'))
					    {	
							    alert("Please select term taxonomy.");
							    jQuery("#xyz_cfl_taxonomy").focus();
						     	return false;
					    }
						else
							return true;
					});
							
   	 				
				}); 

	    function set_status_grp(id) 
		{
	    	document.getElementById("loading_grp_act_"+id).style.display="";
			var dataString = { 
								action: 'xyz_cfl_grp_entry_set_status', 
								grp_activate: id ,
							 };
			jQuery.post(ajaxurl, dataString, function(response) 
					{
				document.getElementById("loading_grp_act_"+id).style.display="none";
				response=jQuery.trim(response);
				var clr=response.split(",");
						jQuery("#grp_act_"+id).html(clr[0]);
						jQuery("#grp_stat_"+id).html(clr[1]);
						jQuery("#grp_stat_"+id).css('color', clr[2]);
					});
	 	} 

	    function add_fld_new(id)
		{
			jQuery('#add_new_field_form_'+id).trigger("reset");
			jQuery("#dflt_div_"+id).hide(); 
			jQuery("#option_div_"+id).hide(); 
			  jQuery("#eg_msg_"+id).hide();
			  jQuery("#plc_div_"+id).hide();					
			
			if(jQuery("#add_new_field_"+id).is(':visible'))
			{
				jQuery( "#no_fields_message_"+id ).show( "slow");
				jQuery( "#add_new_field_"+id ).hide( "slow");
			}					  
			else
			{
				jQuery( "#no_fields_message_"+id ).hide( "slow");
	 	 		jQuery( "#add_new_field_"+id ).show( "slow");
			}
		    jQuery("#field_form_mask_"+id).show();
			jQuery( "#down_arrow_"+id).html("<span title='Hide Fields' class='xyz_cfl_arrow_size'>&#9650;</span>");
		}
	    function add_grp_new() 
	    {
						jQuery("#tax_id_sel_span").hide();
						jQuery('#add_new_fld_grp_form').trigger("reset");
	    }
	   
		
	    function grp_delete(id)
		{
			var type=jQuery("#xyz_cfl_field_post_type_hidden_"+id).val();
			if (confirm("Are you sure you want to delete this row?")) 
            { 
						var order1 = jQuery('#draggable1').sortable("serialize") ; 
						var dataString = { 
											action: 'xyz_cfl_entry_delete', 
											type : type ,
											enable: id ,
											neworder:order1,
										 };

						jQuery.post(ajaxurl, dataString, function(response) 
						{
							response=jQuery.trim(response);
									if(response=="er")
										alert("Delete custom fields then try again.");
									else
									{

										var msg=response.split("{explode_0090}");
										var respon=msg[0];
										
										var res=respon.split(",");
										var orginal_len=(res.length)-1;
										var count_grp=res[orginal_len];
										
										var j=1;
										if(count_grp==0)	
											jQuery( "#drag_"+id ).replaceWith(msg[1]); 
										else
											jQuery("#drag_"+id).remove();
										
										for(var i=0;i<orginal_len;i++)
										{
											jQuery("#order_span_"+res[i]).html(j);
											j++;
										};
										
										if(count_grp<2)
										 {
											 jQuery("#drg_msg").removeClass( "order_message_grp" );
											 jQuery("#drg_msg").text("");
										 }
										
									}
						});
            };     
		}

	    function fld_delete(id) 
		{
			if (confirm("Are you sure you want to delete this field?")) 
            {
                var group_id=jQuery("#group_id_hidden_"+id).val();
						var dataString = { 
											action: 'xyz_cfl_fld_entry_delete', 
											enable: id ,
											group_id: group_id,
										 };
						jQuery.post(ajaxurl, dataString, function(response) 
						{
							response=jQuery.trim(response);
							var msg=response.split("{explode_0090}");
							var respon=msg[0];
							var res=respon.split(",");
							var orginal_len=(res.length)-1;
							var count_fld=res[orginal_len];
							
							var j=1;
							if(count_fld==0)	
								jQuery( "#drag2_"+id ).replaceWith(msg[1]); 
							else
								jQuery("#drag2_"+id).remove();
							jQuery("#fld_count_"+group_id).html(count_fld);
								
							for(var i=0;i<orginal_len;i++)
							{
								jQuery("#field_order_span_"+res[i]).html(j);
								j++;
							};

							 if(count_fld<2)
							 {
								 jQuery("#grp_drg_msg_"+group_id).removeClass( "order_message" );
								 jQuery("#grp_drg_msg_"+group_id).text("");
							 }
							 			
						});
            };     
		}
				
	    function set_status(id) 
		{
	    	document.getElementById("loading_fld_act_"+id).style.display="";
			var dataString = { 
								action: 'xyz_cfl_fld_entry_set_status', 
								fld_activate: id ,
							 };
			jQuery.post(ajaxurl, dataString, function(response) 
					{
						document.getElementById("loading_fld_act_"+id).style.display="none";
						response=jQuery.trim(response);
						var clr_fld=response.split(",");
						jQuery("#act_"+id).html(clr_fld[0]);
								jQuery("#fld_stat_"+id).html(clr_fld[1]);
								jQuery("#fld_stat_"+id).css('color', clr_fld[2]);

						
					});
	 	} 

	    function save_hide_fld_div(id)
		{
		  var id=id;
		  var xyz_cfl_field_name=jQuery("#xyz_cfl_field_name_"+id).val();
		  var xyz_cfl_field_type=jQuery("#xyz_cfl_field_type_"+id).val();
		  if(xyz_cfl_field_type=="Text Field"||xyz_cfl_field_type=="Textarea")
		  {
			  var xyz_cfl_field_placeholder=jQuery("#xyz_cfl_field_placeholder_"+id).val();
		      var xyz_cfl_field_default=jQuery("#xyz_cfl_field_default_txt_"+id).val();
		  }
		  else if(xyz_cfl_field_type=="Numeric")
		  {
			  var xyz_cfl_field_placeholder=jQuery("#xyz_cfl_field_placeholder_"+id).val();
			   var xyz_cfl_field_default=jQuery("#xyz_cfl_field_default_num_"+id).val();
		  }
		  else if(xyz_cfl_field_type=="Dropdown")
		  {
			  var xyz_cfl_field_placeholder="";
			   var xyz_cfl_field_default=jQuery("#xyz_cfl_field_default_drp_"+id).val(); 
		  }
		  else
		  {
			  var xyz_cfl_field_placeholder="";
			  var xyz_cfl_field_default="";
		  }
		  var xyz_cfl_field_mandatory=jQuery("#xyz_cfl_field_mandatory_"+id).val();
		  var dataString = { 
					action: 'xyz_cfl_save_new_field', 
					id: id ,
					xyz_cfl_field_name: xyz_cfl_field_name,
					xyz_cfl_field_type: xyz_cfl_field_type,
					xyz_cfl_field_placeholder: xyz_cfl_field_placeholder,
					xyz_cfl_field_default: xyz_cfl_field_default,
					xyz_cfl_field_mandatory: xyz_cfl_field_mandatory,
					
				 };
		  jQuery.post(ajaxurl, dataString, function(response) 
				{
					response=jQuery.trim(response);
					if(response=="er1")
					  {
						alert('Please fill field name.');
						jQuery('#xyz_cfl_field_name_'+id).focus();
					  }
					else if(response=="er0")
					  {
						alert('Field name already exist.');
						jQuery('#xyz_cfl_field_name_'+id).focus();
					  }
					else if(response=="er2")
					  {
						alert('Please fill field type.');
						jQuery('#xyz_cfl_field_type_'+id).focus();
					  }
					else if(response=="mand_check")
					  {
						alert('Please fill default value.');
						jQuery('#xyz_cfl_field_default_txt_'+id).focus();
						jQuery('#xyz_cfl_field_default_num_'+id).focus();
						jQuery('#xyz_cfl_field_default_drp_'+id).focus();
					  }
					else 
					  { 
						 var res=response.split('{explode_0090}');
						if((res[0])==0)
						  jQuery( "#no_fld_tbl_"+id).replaceWith(res[1]);
						else
						  jQuery("#drag2_"+res[0]).after(res[1]);
						  jQuery( "#add_new_field_"+id ).hide( "slow"); 
						  jQuery("#fld_count_"+id).html(res[2]);
						  jQuery('#add_new_field_form_'+id).trigger("reset");
						 if(res[2]>1)
						 {
							 jQuery("#grp_drg_msg_"+id).addClass( "order_message" );
							 jQuery("#grp_drg_msg_"+id).text(" Drag and drop to reorder ");
						 }
						 tb_remove();
					 } 
				});
		  }
		  
		  function update_fld_group(id)
		  {
		      var id=id;
			  var xyz_cfl_field_group_name_edit=jQuery("#xyz_cfl_field_group_name_edit_"+id).val();
			  var xyz_cfl_field_group_id_hidden=jQuery("#xyz_cfl_field_group_id_hidden_"+id).val();
			  var xyz_cfl_field_post_type_hidden=jQuery("#xyz_cfl_field_post_type_hidden_"+id).val();
			 
			  var dataString = { 
						action: 'xyz_cfl_updt_fld_grp', 
						id: id ,
						xyz_cfl_field_group_name_edit: xyz_cfl_field_group_name_edit,
						xyz_cfl_field_group_id_hidden: xyz_cfl_field_group_id_hidden,
						xyz_cfl_field_post_type_hidden: xyz_cfl_field_post_type_hidden,
			  };
			  jQuery.post(ajaxurl, dataString, function(response) 
						{
				 			  response=jQuery.trim(response);
							  if(response!="") 
							  {
								  jQuery('#edit_form_'+id).trigger("reset");
								  
								  jQuery("#er_msg_"+id).html(response);

								  jQuery('#xyz_cfl_field_group_name_edit_'+id).focus();
								  jQuery("#er_msg_"+id).show();	
								  jQuery('#xyz_cfl_field_group_name_edit_'+id).click(function() 
										  {
											  if(jQuery("#er_msg_"+id).is(':visible'))
												{
												  jQuery("#er_msg_"+id).hide();	
												}
										  });
							  }
							  else 
							  {
								  jQuery("#group_name_"+id).html(xyz_cfl_field_group_name_edit);
								  tb_remove();
							  } 
							  
						});
		  }


		  function update_field_details(id) 
		  {
			  var id=id;
			  var xyz_cfl_field_grp_id_hid=jQuery("#group_id_hidden_"+id).val(); 
			  var xyz_cfl_field_name_edit=jQuery("#xyz_cfl_field_name_edit_"+id).val();
			  var xyz_cfl_field_type_edit=jQuery("#xyz_cfl_field_type_edit_"+id).val();
			 

			  if(xyz_cfl_field_type_edit=="Text Field"||xyz_cfl_field_type_edit=="Textarea")
			  {
				   var xyz_cfl_field_placeholder_edit=jQuery("#xyz_cfl_field_placeholder_edit_"+id).val();
			   	   var xyz_cfl_field_default_edit=jQuery("#xyz_cfl_field_default_edit_txt_"+id).val();
			  }
			  else if(xyz_cfl_field_type_edit=="Numeric")
			  {
				   var xyz_cfl_field_placeholder_edit=jQuery("#xyz_cfl_field_placeholder_edit_"+id).val();
				   var xyz_cfl_field_default_edit=jQuery("#xyz_cfl_field_default_edit_num_"+id).val();
			  }
			  else if(xyz_cfl_field_type_edit=="Dropdown")
			  {
				   var xyz_cfl_field_placeholder_edit="";
				   var xyz_cfl_field_default_edit=jQuery("#xyz_cfl_field_default_edit_drp_"+id).val(); 
			  }
			  else
			  {
				   var xyz_cfl_field_placeholder_edit="";
				   var xyz_cfl_field_default_edit="";
			  }
			   
			  var xyz_cfl_field_mandatory_edit=jQuery("#xyz_cfl_field_mandatory_edit_"+id).val();
			  
			  var dataString = { 
						action: 'xyz_cfl_updt_fld_details', 
						id: id ,
						xyz_cfl_field_grp_id_hid : xyz_cfl_field_grp_id_hid,
						xyz_cfl_field_name_edit : xyz_cfl_field_name_edit,
					  	xyz_cfl_field_type_edit : xyz_cfl_field_type_edit,
					  	xyz_cfl_field_placeholder_edit : xyz_cfl_field_placeholder_edit,
					 	xyz_cfl_field_default_edit : xyz_cfl_field_default_edit,
					  	xyz_cfl_field_mandatory_edit : xyz_cfl_field_mandatory_edit
			  };
			  jQuery.post(ajaxurl, dataString, function(response) 
						{
				  			  response=jQuery.trim(response);
							  if(response!="") 
							  {
								  jQuery('#field_edit_form_'+id).trigger("reset");
								  if(response=="er1")
								  {
									  alert("Please fill field name.");
									  jQuery('#xyz_cfl_field_name_edit_'+id).focus();
								  }
								  else if(response=="er2")
								  {
									  alert("Field name already exist.");
									  jQuery('#xyz_cfl_field_name_edit_'+id).focus();
								  }
								  else if(response=="er3")
								  {
									  alert("Please fill field type.");
									  jQuery('#xyz_cfl_field_type_edit_'+id).focus();
									  jQuery('#xyz_cfl_field_type_edit_'+id).val("");
								  }
								  else if(response=="mand_check")
								  {
									alert('Please fill default value.');
									jQuery('#xyz_cfl_field_default_edit_txt_'+id).focus();
									jQuery('#xyz_cfl_field_default_edit_num_'+id).focus();
									jQuery('#xyz_cfl_field_default_edit_drp_'+id).focus();
								  }
							  }
							  else 
							  {
								  jQuery("#new_f_name_"+id).html(xyz_cfl_field_name_edit);
								  jQuery("#new_f_type_"+id).html(xyz_cfl_field_type_edit);
								  tb_remove();
							  } 
							  
						});
			  
			
		  }
		  
		  

		  function fieldtype_onchange_add(type,id)
		  {
			  if(type=="Text Field"||type=="Textarea"||type=="Numeric")
			  {
				  jQuery("#eg_msg_"+id).hide();
				  jQuery("#plc_div_"+id).show();
				  if(type=="Text Field"||type=="Textarea")
				  {
				  	jQuery("#dflt_div_"+id).show();
				  	jQuery("#option_div_"+id).hide(); 
				    jQuery("#xyz_cfl_field_default_txt_"+id).show();
				    jQuery("#xyz_cfl_field_default_num_"+id).hide();
				    jQuery("#xyz_cfl_field_default_drp_"+id).hide();
				  }
				  else if(type=="Numeric")
				  {
				  	jQuery("#dflt_div_"+id).show();
				  	jQuery("#option_div_"+id).hide(); 
				  	jQuery("#xyz_cfl_field_default_num_"+id).show();
				  	jQuery("#xyz_cfl_field_default_txt_"+id).hide();
				  	jQuery("#xyz_cfl_field_default_drp_"+id).hide();
				   
				  }
			  }
			  else
			  {
				  jQuery("#plc_div_"+id).hide();
				  if(type=="Dropdown")
				  {
				  	jQuery("#dflt_div_"+id).hide();
				  	jQuery("#option_div_"+id).show(); 
				  	jQuery("#xyz_cfl_field_default_drp_"+id).show();
				  	jQuery("#xyz_cfl_field_default_txt_"+id).hide();
				    jQuery("#xyz_cfl_field_default_num_"+id).hide();
				    jQuery("#eg_msg_"+id).show();
				    
				  }
				  else
				  {
					  jQuery("#dflt_div_"+id).hide(); 
					  jQuery("#option_div_"+id).hide(); 
					  jQuery("#eg_msg_"+id).hide();
				  }
			  }
		  }
			function fieldtype_onchange_edit(type,id)
			{
			  	
				if(type=="Text Field"||type=="Textarea"||type=="Numeric")
				  {
					  jQuery("#edit_eg_msg_"+id).hide();
					  jQuery("#edit_option_div_"+id).hide(); 
					  jQuery("#edit_plc_div_"+id).show();
					  if(type=="Text Field"||type=="Textarea")
					  {
					  	jQuery("#edit_dflt_div_"+id).show();
					    jQuery("#xyz_cfl_field_default_edit_txt_"+id).show();
					    jQuery("#xyz_cfl_field_default_edit_num_"+id).hide();
					    jQuery("#xyz_cfl_field_default_edit_drp_"+id).hide();
					  }
					  else if(type=="Numeric")
					  {
					  	jQuery("#edit_dflt_div_"+id).show();
					  	jQuery("#xyz_cfl_field_default_edit_num_"+id).show();
					  	jQuery("#xyz_cfl_field_default_edit_txt_"+id).hide();
					  	jQuery("#xyz_cfl_field_default_edit_drp_"+id).hide();
					  }
					  
				  }
				  else
				  {
					  jQuery("#edit_plc_div_"+id).hide();
					  if(type=="Dropdown")
					  {
					  	jQuery("#edit_dflt_div_"+id).hide();
					  	jQuery("#edit_option_div_"+id).show(); 
					  	jQuery("#xyz_cfl_field_default_edit_drp_"+id).show();
					  	jQuery("#xyz_cfl_field_default_edit_txt_"+id).hide();
					    jQuery("#xyz_cfl_field_default_edit_num_"+id).hide();
					    jQuery("#edit_eg_msg_"+id).show();
					    
					  }
					  else
					  {
						  jQuery("#edit_dflt_div_"+id).hide();
						  jQuery("#edit_option_div_"+id).hide();  
						  jQuery("#edit_eg_msg_"+id).hide();
					  }
				  }
				
			}	  
			jQuery(document).ready(function() {
				window.history.pushState('obj', '', 'admin.php?page=custom-field-manager-customfields');
				jQuery("#tax_id_sel_span").hide();
				var xyz_cfl_post_type_val=jQuery("#xyz_cfl_post_type_sel").val();
				var xyz_cfl_taxonomy_val="<?php echo $xyz_cfl_taxonomy;?>";
				var tax_rd="<?php echo $xyz_cfl_taxonomy_init;?>";
				var chckid="<?php echo $term_selectedOptions;?>";
				var fl_for_taxsel="<?php echo $fl_for_taxsel;?>";
				if(tax_rd=="")
				{
					jQuery("#tax_id_sel_span_init").hide();
					jQuery("#tax_id_sel_span_init_hd").hide();	
				}
				else if(tax_rd!="" && chckid=="")
			    {
			    	jQuery("#null_term_id_init").show();
			    	jQuery("#xyz_cfl_taxonomy_term_id_init").hide();
			    }
				
			});

			function grp_dropdown_post_type_select(act)
			{
				var data = {
						action: 'xyz_cfl_fetch_taxonomies_frst',
						post_type: act,
					};
					
				jQuery.post(ajaxurl, data, function(response) 
						{
							jQuery("#xyz_cfl_taxonomy").html(response);
							jQuery("#xyz_cfl_taxonomy").show();
							jQuery("#null_term").hide();
							if(response=="")
							{
								jQuery("#null_term").show();
								jQuery("#xyz_cfl_taxonomy").hide();
							}
						});
			}
			
			function grp_dropdown_post_type_select_init(act,tax)
			{
					document.getElementById("loading_posttype_sel").style.display="";
					jQuery("#tax_id_sel_span_init").hide();	
					jQuery("#tax_id_sel_span_init_hd").hide();	
					var data = {
									action: 'xyz_cfl_fetch_taxonomies_frst',
									post_type: act,
									tax :tax,
								};
					
					jQuery.post(ajaxurl, data, function(response) 
							{
								document.getElementById("loading_posttype_sel").style.display="none";
								jQuery("#xyz_cfl_taxonomy_init").html(response);
								jQuery("#xyz_cfl_taxonomy_init").show();
								jQuery("#null_term_init").hide();
								if(response=="")
								{
									jQuery("#null_term_init").show();
									jQuery("#xyz_cfl_taxonomy_init").hide();
									jQuery("#zeroval").val(1);
								}
								else
									jQuery("#zeroval").val("");
							});
			}
			function grp_dropdown_post_type_select_for_addgrp(act,tax)
			{
					document.getElementById("loading_posttype_sel_for_add").style.display="";
					jQuery("#tax_id_sel_span").hide();	
					var data = {
									action: 'xyz_cfl_fetch_taxonomies_frst',
									post_type: act,
									tax :tax,
								};
					
					jQuery.post(ajaxurl, data, function(response) 
							{
								document.getElementById("loading_posttype_sel_for_add").style.display="none";
								if(response=="")
								{
									jQuery("#null_term").show();
									jQuery("#xyz_cfl_taxonomy").hide();
									
								}
								else
								{
									jQuery("#xyz_cfl_taxonomy").html(response).show();
									jQuery("#null_term").hide();
								}
							});
			}
			
			function dropdown_taxonomy_select(act)
			{
				document.getElementById("loading").style.display="";
				if(act!="")
					jQuery("#tax_id_sel_span").show();
				else
					jQuery("#tax_id_sel_span").hide();

				var data = {
								action: 'xyz_cfl_fetch_taxonomy_ids',
								taxonomy: act,
								term : "",
						   };
				jQuery.post(ajaxurl, data, function(response) 
						{ 

							document.getElementById("loading").style.display="none";
							jQuery("#xyz_cfl_taxonomy_term_id").show().html(response);
							jQuery("#null_term_id").hide();
							
							if(response=="")
							{
								jQuery("#null_term_id").show();
								jQuery("#xyz_cfl_taxonomy_term_id").hide();
								
							}
						}); 
			}

			function dropdown_taxonomy_select_init(act,chckid)
			{
				document.getElementById("loading_taxonomy_init").style.display="";
				
				if(act!="")
				{
					jQuery("#tax_id_sel_span_init").show();
					jQuery("#tax_id_sel_span_init_hd").show();	
				}
				else
				{
					jQuery("#tax_id_sel_span_init").hide();	
					jQuery("#tax_id_sel_span_init_hd").hide();	
				} 
				var data = {
								action: 'xyz_cfl_fetch_taxonomy_ids',
								taxonomy: act,
								term :chckid,
							};
				
				jQuery.post(ajaxurl, data, function(response) 
						{
							document.getElementById("loading_taxonomy_init").style.display="none";
							jQuery("#xyz_cfl_taxonomy_term_id_init").show().html(response);
							jQuery("#null_term_id_init").hide();
							if(response=="")
							{
								jQuery("#null_term_id_init").show();
								jQuery("#xyz_cfl_taxonomy_term_id_init").hide();
								
							}
						}); 
			}
			

		    function clicky_arrow(id)
		    {
		    	if(jQuery("#add_new_field_"+id).is(':visible'))
		   			jQuery("#add_new_field_"+id).hide();
			    if(jQuery("#field_form_mask_"+id).is(':visible'))
			    {
				    jQuery("#field_form_mask_"+id).hide();
					jQuery( "#down_arrow_"+id).html("<span title='View Fields' class='xyz_cfl_arrow_size'>&#9660;</span>");
 			    }
			    else
			    {
				    jQuery("#field_form_mask_"+id).show();
					jQuery( "#down_arrow_"+id).html("<span title='Hide Fields' class='xyz_cfl_arrow_size'>&#9650;</span>");
			    }
		    }
			     	
			function json_fld_info(id) 
			{
				var data ={
							  action: 'xyz_cfl_json_fld_edit',
							  id:id,
						  };
				jQuery.post(ajaxurl, data, function(response) 
						{
							response=jQuery.trim(response);
							var json_x = jQuery.parseJSON(response);
							field_name=json_x.field_name;
							field_type=json_x.field_type;
							placeholder=json_x.placeholder;
							defaulttxt=json_x.defaulttxt;
							mandatory=json_x.mandatory;
							jQuery("#xyz_cfl_field_name_edit_"+id).val(field_name);
							jQuery("#xyz_cfl_field_type_edit_"+id).val(field_type);

						  	if(field_type=="Text Field"||field_type=="Textarea")
						  	{
								  jQuery("#edit_eg_msg_"+id).hide();
								  jQuery("#edit_plc_div_"+id).show();
								  jQuery("#edit_dflt_div_"+id).show();
								  jQuery("#edit_option_div_"+id).hide(); 
								  jQuery("#xyz_cfl_field_default_edit_txt_"+id).show();
								  jQuery("#xyz_cfl_field_default_edit_num_"+id).hide().val('');
								  jQuery("#xyz_cfl_field_default_edit_drp_"+id).hide().val('');
							   	  jQuery("#xyz_cfl_field_default_edit_txt_"+id).val(defaulttxt);
							   	  jQuery("#xyz_cfl_field_placeholder_edit_"+id).val(placeholder);
							}
						    else if(field_type=="Numeric")
							{
								  jQuery("#edit_eg_msg_"+id).hide();
								  jQuery("#edit_plc_div_"+id).show();
								  jQuery("#edit_dflt_div_"+id).show();
								  jQuery("#edit_option_div_"+id).hide(); 
								  jQuery("#xyz_cfl_field_default_edit_num_"+id).show();
								  jQuery("#xyz_cfl_field_default_edit_txt_"+id).hide().val('');
								  jQuery("#xyz_cfl_field_default_edit_drp_"+id).hide().val('');
								  jQuery("#xyz_cfl_field_default_edit_num_"+id).val(defaulttxt);
								  jQuery("#xyz_cfl_field_placeholder_edit_"+id).val(placeholder);
							}
							else if(field_type=="Dropdown")
							{
								  jQuery("#edit_dflt_div_"+id).hide();
								  jQuery("#edit_option_div_"+id).show(); 
								  jQuery("#edit_plc_div_"+id).hide();
								  jQuery("#xyz_cfl_field_default_edit_drp_"+id).show();
								  jQuery("#xyz_cfl_field_default_edit_txt_"+id).hide().val('');
								  jQuery("#xyz_cfl_field_default_edit_num_"+id).hide().val('');
								  jQuery("#edit_eg_msg_"+id).show();
								  jQuery("#xyz_cfl_field_placeholder_edit_"+id).val('');
								  jQuery("#xyz_cfl_field_default_edit_drp_"+id).val(defaulttxt); 
							}
							else
							{
								  jQuery("#edit_dflt_div_"+id).hide(); 
								  jQuery("#edit_eg_msg_"+id).hide();
							}
							jQuery("#xyz_cfl_field_mandatory_edit_"+id).val(mandatory);
						});
				}
</script>
