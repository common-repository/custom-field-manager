<?php
global $wpdb;
add_action( 'delete_post', 'xyz_cfl_postdtls_dlt_actions' );
add_action( 'add_meta_boxes', 'xyz_cfl_cust_meta_box' );

function xyz_cfl_cust_meta_box()
{
	global $wpdb;
	$post_type=get_post_type();
	$flag_for_show_fld=0;
	$nul_taxonomies = get_object_taxonomies($post_type,'objects');
	if ( $nul_taxonomies )
	{
		foreach ($nul_taxonomies as $key=>$values)
		{
			if($values->hierarchical==1)
			{
				$null_tax_status=$wpdb->get_results($wpdb->prepare("SELECT  `xyz_cfl_group_status` FROM `".$wpdb->prefix."xyz_cfl_group` WHERE `xyz_cfl_group_taxonomy`=%s AND xyz_cfl_group_post_type=%s",$key,$post_type));
				foreach ( $null_tax_status  as  $null_tax_stat )
				{
					if($null_tax_stat->xyz_cfl_group_status==1)
						$flag_for_show_fld=1;
				}
			}
		}
	}
	
	$nul_taxonomy="";
	$null_tax_status=$wpdb->get_results($wpdb->prepare("SELECT  `xyz_cfl_group_status` FROM `".$wpdb->prefix."xyz_cfl_group` WHERE `xyz_cfl_group_taxonomy`=%s AND xyz_cfl_group_post_type=%s",$nul_taxonomy,$post_type));
	foreach ( $null_tax_status  as  $null_tax_stat )
	{
		if($null_tax_stat->xyz_cfl_group_status==1)
		{
			$flag_for_show_fld=1;
		}
	}
	if($flag_for_show_fld==1)
		add_meta_box('Custom Fields',__( 'Fields'),'xyz_add_cust_field',$post_type );
}
if(!function_exists('xyz_add_cust_field'))
{
	function xyz_add_cust_field()
	{
		$tax="";$terms="";
		global $wpdb;
		$post_type=get_post_type();
		$taxonomies = get_object_taxonomies($post_type,'objects');
		if ( $taxonomies )
		{
			foreach ($taxonomies as $key=>$values1)
			{
				if($values1->hierarchical==1)
					$tax.=$key.",";
			}
		}
		?>

		<input type="hidden" id="tax_array" name="tax_array" value="<?php echo $tax;?>">
		<script type="text/javascript">
					jQuery(document).ready(function()
					{
						<?php 
						$tax_array=explode(',', $tax);
						$count_tax=count($tax_array);
						for($i=0;$i<$count_tax;$i++)
						{
							$all_options_cls=$wpdb->get_results($wpdb->prepare("SELECT `term_id` FROM `".$wpdb->prefix."term_taxonomy` WHERE `taxonomy` = %s",$tax_array[$i]));
							foreach ( $all_options_cls as $option_cls )
							{
								$hide_id=$option_cls->term_id;
								?>
								var hide_id="<?php echo $hide_id?>";					
								var ta_id="<?php echo $tax_array[$i]?>";
								jQuery(".xyz_cfl_data_"+hide_id+"_"+ta_id).hide();
								<?php
							}
						}
						?>	
					});
		</script>
		<?php
		xyz_cfl_show_meta();
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() 
			{	
				jQuery('#category-all').bind("DOMSubtreeModified",function()
						{
							get_categorylist(1);
						});
				get_categorylist(1);
				jQuery('#category-all').on("click",'input[name="post_category[]"]',function() 
						{
							get_categorylist(1);
						});
				jQuery('#category-pop').on("click",'input[type="checkbox"]',function() 
						{
							get_categorylist(2);
						});
				
				var tax=jQuery('#tax_array').val();
				if (tax.indexOf(",category,") >= 0)
					tax=tax.replace(",category,",",");
				var taxonomy=tax.split(",");
				var i="";
				var len=taxonomy.length;
			    for(i=0;i<len;i++)
			    {
			    	var cust_tax=taxonomy[i];
					cust_tax_check(cust_tax);
					if(cust_tax!="xyz_cls_category")
						jQuery("#tax_head_"+taxonomy[i] ).hide();
			    }
			    jQuery("#tax_head_category").hide();
			
			    if(jQuery(".xyz_cfl_data__").is(':visible'))
			    jQuery("#tax_head_").show();
			    
			});
			
			function cust_tax_check(taxonomy) 
			{
				
				jQuery('#'+taxonomy+'-all').bind("DOMSubtreeModified",function()
						{
							get_cust_list(1,taxonomy);
						});
				get_cust_list(1,taxonomy);
				jQuery('#'+taxonomy+'-all').on("click",'input[name="tax_input['+taxonomy+'][]"]',function() 
						{
							get_cust_list(1,taxonomy);
						});
				jQuery('#'+taxonomy+'-pop').on("click",'input[type="checkbox"]',function() 
						{
							get_cust_list(2,taxonomy);
						});
			
				if(jQuery("#"+taxonomy).is(':visible'))
				{
					jQuery( "#"+taxonomy).on("change",function() {
			
						var optVals=[];
						var tx_id1=this.value; 
						jQuery("#tax_head_"+taxonomy ).hide();
							
						jQuery("#"+taxonomy+" option").each(function()
						{
							 optVals.push( jQuery(this).attr('value'));
						});
						
						for(var check1=0;check1<optVals.length;check1++)
						{
							if(optVals[check1]!=tx_id1)
							{
								var parents=jQuery("#hdn_parent_"+optVals[check1]+"_"+taxonomy).val();
								if(parents)
								 {
									 var split_par=parents.split(','); 
									for(var i=0;i<split_par.length;i++)
									 {
										 jQuery(".xyz_cfl_data_"+split_par[i]+"_"+taxonomy).hide();
									 }
								 }
							}
						}
						var parents=jQuery("#hdn_parent_"+this.value+"_"+taxonomy).val();
						if(parents)
						 {
							 var split_par=parents.split(',');  
							 for(var i=0;i<split_par.length;i++)
							 {
								 jQuery(".xyz_cfl_data_"+split_par[i]+"_"+taxonomy).show();
								 if(jQuery(".xyz_cfl_data_"+split_par[i]+"_"+taxonomy).is(':visible'))
									 jQuery("#tax_head_"+taxonomy ).show();
							 }
				 		 }
				 		 
					 	jQuery(".xyz_cfl_data__").show();
					 	
					});
					var edit_cls_term=jQuery("#xyz_cls_category").val();
					var parents=jQuery("#hdn_parent_"+edit_cls_term+"_"+taxonomy).val();
					if(parents)
					 {
						 var split_par=parents.split(',');  
						 jQuery("#tax_head_"+taxonomy ).show();
						 for(var i=0;i<split_par.length;i++)
						 {
							 jQuery(".xyz_cfl_data_"+split_par[i]+"_"+taxonomy).show();
							 if(jQuery(".xyz_cfl_data_"+split_par[i]+"_"+taxonomy).is(':visible'))
								 jQuery("#tax_head_"+taxonomy ).show();
							 else
								 jQuery("#tax_head_"+taxonomy ).hide();
						 }
			 		 }
					 
				}
				else
					jQuery("#tax_head_"+taxonomy ).hide();
				
			}
			
			function get_cust_list(val,taxonomy)
			{ 
				 var dataString = { 
										action : 'xyz_cfl_set_count', 
										value : taxonomy
								  };
				jQuery.post(ajaxurl, dataString, function(response) 
				{	
					 var cat_list1="";
					 if(val==1)
						 {
						 	 jQuery('input[name="tax_input['+taxonomy+'][]"]:not(:checked)').each(function() 
						 		 	 {
						 				 jQuery("#tax_head_"+taxonomy).hide();
										 for(var j=0;j<response;j++){
										 jQuery(".xyz_cfl_data_"+this.value+"_"+taxonomy).hide();
										 jQuery(".xyz_cfl_data__").show();}
									});
						 	  jQuery('input[name="tax_input['+taxonomy+'][]"]:checked').each(function() 
							  {
								 cat_list1+=this.value+",";
								 var i="";
								 var parents=jQuery("#hdn_parent_"+this.value+"_"+taxonomy).val();
								 if(parents)
									 {
										 var split_par=parents.split(',');  
										 for( i=0;i<split_par.length;i++)
										 {
											 jQuery(".xyz_cfl_data_"+split_par[i]+"_"+taxonomy).show();
											 if(jQuery(".xyz_cfl_data_"+split_par[i]+"_"+taxonomy).is(':visible'))
												 jQuery("#tax_head_"+taxonomy ).show();
										 }
							 		 }
								 
								 jQuery(".xyz_cfl_data__").show();
							 });
					  
						}
						else if(val==2)
						{
							
							jQuery('#'+taxonomy+'-pop input[type="checkbox"]:not(:checked)').each(function() 
									{
										 jQuery("#tax_head_"+taxonomy).hide();
										 for(var j=0;j<response;j++)
											 {
												 jQuery(".xyz_cfl_data_"+this.value+"_"+taxonomy).hide();
												 jQuery(".xyz_cfl_data__").show();
											 }
									});
							jQuery('#'+taxonomy+'-pop input[type="checkbox"]:checked').each(function() 
							{
								cat_list1+=this.value+",";
								var i="";
								var parents=jQuery("#hdn_parent_"+this.value+"_"+taxonomy).val();
								if(parents)
									{
										var split_par=parents.split(',');  
										for( i=0;i<split_par.length;i++)
										{
											jQuery(".xyz_cfl_data_"+split_par[i]+"_"+taxonomy).show();
											if(jQuery(".xyz_cfl_data_"+split_par[i]+"_"+taxonomy).is(':visible'))
											{
											 jQuery("#tax_head_"+taxonomy ).show();
											}
										}
									}
								jQuery(".xyz_cfl_data__").show();
							});
						}
						if (cat_list1.charAt(cat_list1.length - 1) == ',') 
							cat_list1 = cat_list1.substr(0, cat_list1.length - 1);
						jQuery('#cat_list').val(cat_list1);
				    });
			}
			
			function get_categorylist(val)
			{
				 var dataString = { 
										action : 'xyz_cfl_set_count',     //for hide groups of unchecked terms when checking category terms  
										value : 'category'
								  };
				jQuery.post(ajaxurl, dataString, function(response) 
					{	
						var cat_list="";
						if(val==1)
						{
			    				jQuery('input[name="post_category[]"]:not(:checked)').each(function() 
			    	    				{
			    							jQuery("#tax_head_category" ).hide();
											for(var j=0;j<response;j++)
												{
													jQuery(".xyz_cfl_data_"+this.value+"_category").hide();
													jQuery(".xyz_cfl_data__").show();
												}
				 						});
			    				 jQuery('input[name="post_category[]"]:checked').each(function() 
								 { 
									 cat_list+=this.value+",";
									 var i="";
									 var parents=jQuery("#hdn_parent_"+this.value+"_category").val(); //alert("#hdn_parent_"+this.value+"_category");
									 if(parents)
									 {
									 	var split_par=parents.split(',');  
										for( i=0;i<split_par.length;i++)
										{
											jQuery(".xyz_cfl_data_"+split_par[i]+"_category").show();
											if(jQuery(".xyz_cfl_data_"+split_par[i]+"_category").is(':visible'))
											 jQuery("#tax_head_category").show();
										}
								 	 }
									 
									 jQuery(".xyz_cfl_data__").show();
								});
						}
						else if(val==2)
						{
							
							jQuery('#category-pop input[type=checkbox]:not(:checked)').each(function() {
								jQuery("#tax_head_category" ).hide();
								 for(var j=0;j<response;j++){
								jQuery(".xyz_cfl_data_"+this.value+"_category").hide();
								 jQuery(".xyz_cfl_data__").show();}
							});
							jQuery('#category-pop input[type="checkbox"]:checked').each(function() 
							{
								cat_list+=this.value+",";
								var i="";
								var parents=jQuery("#hdn_parent_"+this.value+"_category").val();
								if(parents)
								{
									var split_par=parents.split(',');  
									for( i=0;i<split_par.length;i++)
									{
										jQuery(".xyz_cfl_data_"+split_par[i]+"_category").show();
										if(jQuery(".xyz_cfl_data_"+split_par[i]+"_category").is(':visible'))
										{
										 jQuery("#tax_head_category").show();
										}
									}
								}
								jQuery(".xyz_cfl_data_"+this.value+"_category").show();
								jQuery(".xyz_cfl_data__").show();
							});
					}
					if (cat_list.charAt(cat_list.length - 1) == ',') 
						 cat_list = cat_list.substr(0, cat_list.length - 1);
					jQuery('#cat_list').val(cat_list);
				});
			}
		</script>
		<input type="hidden" name="cat_list" id="cat_list" value="">
		<div id="data"></div>
		<?php
	}
}
if(!function_exists('xyz_cfl_show_meta'))
{
	function xyz_cfl_show_meta()
	{
		global $wpdb;
		$post_type=get_post_type();
		xyz_cfl_show_distinct('');
		$taxnms = get_object_taxonomies($post_type,'objects');
		if ( $taxnms )
		{
			foreach ($taxnms as $key=>$values2)
			{
				if($values2->hierarchical==1)
				{
					xyz_cfl_show_distinct($key);
				}
			}
		}
	}
}

if(!function_exists('xyz_cfl_show_distinct'))
{
	function xyz_cfl_show_distinct($dist_tax)
	{ 
		global $wpdb;
		$flag_for_hid_head=0;
		$cur_post_type = get_post_type();
		$f3=0;$tterm_id="";$term_id="";$taxonomy_hd="";
		$parenid=0;
		$taxonomy_hd=$dist_tax;
		
		if($taxonomy_hd=='')
		{
			?>
			<div id="tax_head_<?php echo $taxonomy_hd;?>">
				<span style="font-size: 14;"><br> <br> <?php
				echo "Details";
				?> </span>
			</div>
			<?php
			$field_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_group WHERE xyz_cfl_group_post_type=%s AND xyz_cfl_group_taxonomy_term_id=%d AND xyz_cfl_group_taxonomy=%s ORDER BY xyz_cfl_group_order  ASC",$cur_post_type,$term_id,$taxonomy_hd));
			foreach ($field_lists as $field_list)
			{
				$grp_id= $field_list->id;
				$grp_name= $field_list->xyz_cfl_group_name;
				$grp_status=$field_list->xyz_cfl_group_status;
				$checked_field_lists0=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE `xyz_cfl_group_id`=%d ORDER BY `xyz_cfl_field_order` ASC",$grp_id));
				
				xyz_cfl_show_par_meta($grp_id,$grp_name,$grp_status,$term_id,$taxonomy_hd,'',$checked_field_lists0);
			}
		}
		else
		{
			?>
			<div id="tax_head_<?php echo $taxonomy_hd;?>">
			<span style="font-size: 14;"><br> <br> <?php
			echo "Details - ".$taxonomy_hd;
			?> </span>
			</div>
			<?php
			$hd_par='';
			xyz_cfl_recurs_func($parenid,$cur_post_type,$taxonomy_hd,$tterm_id,$hd_par);
		}
	}
}
if(!function_exists('xyz_cfl_recurs_func'))
{
	function xyz_cfl_recurs_func($parenid,$cur_post_type,$taxonomy_hd,$tterm_id,$hd_par)
	{
		global $wpdb;
		$sel_par_0='';$par_tid='';$term_id=$tterm_id;
		if($hd_par=='')
			$hd_par=$parenid;
		else 
			$hd_par.=','.$parenid;
		
		$sel_par_0=$wpdb->get_results($wpdb->prepare("SELECT * FROM `".$wpdb->prefix."term_taxonomy` WHERE `parent` =%d AND `taxonomy`=%s ORDER BY `term_taxonomy_id`",$parenid,$taxonomy_hd));
		if(count($sel_par_0)>0)
		{	
			foreach ($sel_par_0 as $sel_par)
			{
				$par_tid=$sel_par->term_id;
				$checked_field_lists1='';
				$field_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_group WHERE xyz_cfl_group_post_type=%s AND xyz_cfl_group_taxonomy_term_id=%d AND xyz_cfl_group_taxonomy=%s ORDER BY xyz_cfl_group_order  ASC",$cur_post_type,$par_tid,$taxonomy_hd));
				if(count($field_lists)>0)
				{
					foreach ($field_lists as $field_list)
					{
						$grp_id= $field_list->id;
						$grp_name= $field_list->xyz_cfl_group_name; 
						$grp_status=$field_list->xyz_cfl_group_status;
						$checked_field_lists1=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE `xyz_cfl_group_id`=%d ORDER BY `xyz_cfl_field_order` ASC",$grp_id));
						xyz_cfl_show_par_meta($grp_id,$grp_name,$grp_status,$par_tid,$taxonomy_hd,$hd_par,$checked_field_lists1);
					}
				}
				else 
					xyz_cfl_show_par_meta('','','',$par_tid,$taxonomy_hd,$hd_par,$checked_field_lists1);
				xyz_cfl_recurs_func($par_tid,$cur_post_type,$taxonomy_hd,$par_tid,$hd_par);
			}
		}
	}
}

if(!function_exists('xyz_cfl_show_par_meta'))
{
	function xyz_cfl_show_par_meta($grp_id,$grp_name,$grp_status,$term_id,$taxonomy_hde,$hd_par,$al_checked_field_lists)
	{
		global $wpdb;$post_ID='';
		$xyz_cfl_mandatory_field_opt=get_option('xyz_cfl_mandatory_field');
		if(isset($_GET['action']) && $_GET['action']=="edit")
		{
			$post_ID=$_GET['post'];
			$tab_name_str=strtolower($taxonomy_hde);
			$tab_name=str_replace("-","_",$tab_name_str);
			$par_tab_id=xyz_cfl_parent_term($term_id,$taxonomy_hde);
			if (is_array($al_checked_field_lists))
			{
				foreach ($al_checked_field_lists as $list)
				{
					$tab_fld_id=$list->id;
					if($tab_name!="")
					{
						if($wpdb->get_var("SHOW TABLES LIKE '".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$par_tab_id."'") ==$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$par_tab_id)
						{
							$fld="";
							$tblcolums = $wpdb->get_results("SHOW COLUMNS FROM  ".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$par_tab_id." like 'field_".$tab_fld_id."'");
							foreach ($tblcolums as $row)
							{
								$fld=$row->Field;
								if($fld=="field_".$tab_fld_id."")
								{
									$fld_edit_lists=$wpdb->get_results($wpdb->prepare("SELECT `field_".$tab_fld_id."` FROM `".$wpdb->prefix."xyz_cfl_field_values_".$tab_name."_".$par_tab_id."` WHERE `post_id`=%d",$post_ID));
									$col="field_".$tab_fld_id;
									$data="";
									foreach($fld_edit_lists as $fld_edit_list)
									{
										$data=$fld_edit_list->$col;
										$edit_datas[$tab_fld_id]=$data;
									}
								}
							}
						}
					}
					else
					{
						$fld1="";
						$tblcolums1 = $wpdb->get_results("SHOW COLUMNS FROM  ".$wpdb->prefix."xyz_cfl_field_values like 'field_".$tab_fld_id."'");
						foreach ($tblcolums1 as $row1)
						{
							$fld1=$row1->Field;
							if($fld1=="field_".$tab_fld_id."")
							{
								$fld_edit_lists1=$wpdb->get_results($wpdb->prepare("SELECT `field_".$tab_fld_id."` FROM `".$wpdb->prefix."xyz_cfl_field_values` WHERE `post_id`=%d",$post_ID));
								$col1="field_".$tab_fld_id;
								$data1="";
								foreach($fld_edit_lists1 as $fld_edit_list1)
								{
									$data1=$fld_edit_list1->$col1;
									$edit_datas[$tab_fld_id]=$data1;
								}
							}
						}
					}
				}
			}
		}
		$parnts='';
		if($hd_par!='' && $term_id!='')
			$parnts=$hd_par.",".$term_id;
		if($parnts=='')
			$parnts=$term_id;
		?>
		<input
			type="hidden" name="hdn_parent_<?php echo $term_id;?>_<?php echo $taxonomy_hde;?>"
			id="hdn_parent_<?php echo $term_id;?>_<?php echo $taxonomy_hde;?>" value="<?php echo $parnts;?>">
		<?php
		if($grp_status==1)
		{
			$show_lists=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."xyz_cfl_fields WHERE `xyz_cfl_group_id`=%d ORDER BY `xyz_cfl_field_order` ASC",$grp_id));
			?>
			<div id="xyz_cfl_data_<?php echo $term_id;?>_<?php echo $taxonomy_hde;?>"
			class="xyz_cfl_data_<?php echo $term_id;?>_<?php echo $taxonomy_hde;?> xyz_cfl_table" style="margin-top: 20px;">
			<h3 style="cursor: auto;">
				<?php echo $grp_name;?>
			</h3>
			<?php
		
			foreach ($show_lists as $list)
			{
				$flag_for_hid_head=1;
				$xyz_cfl_field_id=$list->id;
				$xyz_cfl_field_name=$list->xyz_cfl_field_name;
				$xyz_cfl_field_type=$list->xyz_cfl_field_type;
				$xyz_cfl_field_mandatory=$list->xyz_cfl_field_mandatory;
				$xyz_cfl_field_placeholder=$list->xyz_cfl_field_placeholder;
				$xyz_cfl_field_default=$list->xyz_cfl_field_default;
				$xyz_cfl_field_status=$list->xyz_cfl_field_status;
				$val="";
				if($xyz_cfl_field_status==1)
				{
					$val=isset($edit_datas[$xyz_cfl_field_id])?$edit_datas[$xyz_cfl_field_id]:'';
					?>
				<table id="tab_id_<?php echo $xyz_cfl_field_id;?>"
					style="width: 99%; margin: 0 auto; margin-top: 10px;">
					<?php
					if($xyz_cfl_field_type=="Text Field")
					{
						$fl_01=0;$fl=0;$fl_0=0;
						?>
						<tr>
							<td width="20%"><?php echo esc_html($xyz_cfl_field_name);?>&nbsp;<span
								class="mandatory"> <?php
								if($xyz_cfl_field_mandatory==0)
								{
									echo '*';
								}
								?>
							</span>
							</td>
							<td width="5%">:</td>
							<td>
							<input type="text" <?php if($val=="" && $xyz_cfl_mandatory_field_opt==0 && $xyz_cfl_field_mandatory==1){?>
								value="<?php echo esc_html($xyz_cfl_field_default)?>" <?php } else{?>
								value="<?php echo esc_html($val);?>"				<?php } ?>
								id="fld_id_<?php echo $xyz_cfl_field_id;?>"
								name="fld_id_<?php echo $xyz_cfl_field_id;?>"
								placeholder="<?php echo $xyz_cfl_field_placeholder;?>">
							</td>
						</tr>
						<?php
					}
					else if($xyz_cfl_field_type=="Textarea")
					{
						?>
						<tr>
							<td width="20%"><?php echo esc_html($xyz_cfl_field_name);?>&nbsp;<span
								class="mandatory"> <?php
								if($xyz_cfl_field_mandatory==0)
									echo '*';
								?>
							</span>
							</td>
							<td width="5%">:</td>
							<td><textarea id="fld_id_<?php echo $xyz_cfl_field_id;?>"
									name="fld_id_<?php echo $xyz_cfl_field_id;?>"
									placeholder="<?php echo $xyz_cfl_field_placeholder;?>"><?php if($val=="" && $xyz_cfl_mandatory_field_opt==0 && $xyz_cfl_field_mandatory==1){echo esc_html($xyz_cfl_field_default);} else{echo esc_html($val);}?></textarea>
							</td>
						</tr>
						<?php
					}
					else if($xyz_cfl_field_type=="Dropdown")
					{
						$val1="";
						?>
						<tr>
							<td width="20%"><?php echo esc_html($xyz_cfl_field_name);?>&nbsp;<span
								class="mandatory"> <?php
								if($xyz_cfl_field_mandatory==0)
									echo '*';
								?>
							</span>
							</td>
							<td width="5%">:</td>
							<?php
							$sepr_commas=explode(',', $xyz_cfl_field_default);
							$count=count($sepr_commas);
							?>
							<td><select id="fld_id_<?php echo $xyz_cfl_field_id;?>"
								name="fld_id_<?php echo $xyz_cfl_field_id;?>">
									<?php
										for($i=0;$i<$count;$i++)
										{
											$key='';$value="";
											$val1=$sepr_commas[$i];
											if (strpos($val1,':') !== false)
											{
												$key_val=explode(':', $val1);
												$key=$key_val[0];
												$value=$key_val[1];
											}
											else
												$key=$value=$val1;
				
											?>
									<option value="<?php echo $value;?>" <?php if($value==$val) {?>
										selected="selected" <?php }?>>
										<?php echo $value;?>
									</option>
									<?php
										}
				
									?>
							</select>
							</td>
						</tr>
						<?php
					}
					else if($xyz_cfl_field_type=="Numeric")
					{
						?>
						<tr>
							<td width="20%"><?php echo esc_html($xyz_cfl_field_name);?>&nbsp;<span
								class="mandatory"> <?php
								if($xyz_cfl_field_mandatory==0)
									echo '*';
								?>
							</span>
							</td>
							<td width="5%">:</td>
							<td><input type="text" <?php if($val=="" && $xyz_cfl_mandatory_field_opt==0 && $xyz_cfl_field_mandatory==1){?>
								value="<?php echo esc_html($xyz_cfl_field_default)?>" <?php } else{?>
								value="<?php echo esc_html($val);?>"				<?php } ?>
								id="fld_id_<?php echo $xyz_cfl_field_id;?>"
								name="fld_id_<?php echo $xyz_cfl_field_id;?>"
								placeholder="<?php echo $xyz_cfl_field_placeholder;?>"
								onkeyup="this.value = this.value.replace(/[^0-9\.]/g,'');">
							</td>
						</tr>
						<?php
					}
					?>
					</table>
					<?php
				}
			}
			?>
			</div>
			<?php
		}
	}
}
if(!function_exists('xyz_cfl_postdtls_dlt_actions'))
{	
	function xyz_cfl_postdtls_dlt_actions()
	{
		global $post,$wpdb;
	
		if(isset($_GET['action']) && $_GET['action']=='delete')
		{
			$postid=$_GET['post'];
			$dbname="Tables_in_".DB_NAME;
			$post_tables = $wpdb->get_results('SHOW TABLES');
			
			foreach($post_tables as $key => $value)
			{
				$p_table=$value->$dbname;
				$pre=$wpdb->prefix."xyz_cfl_field_values";
				
				$pos=strpos($p_table, $pre);
				if ($pos !== false)
				{
					$posted_datas=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$p_table." WHERE post_id=%d", $postid));
					foreach($posted_datas as $posted_data)
					{
						$wpdb->get_results($wpdb->prepare("DELETE FROM ".$p_table." WHERE id=%d",$posted_data->id));
					}
				}
			}
		}
	}
}
?>