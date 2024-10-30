<?php
global $wpdb;
$flag=1; $msg1=""; 
$xyz_cfl_mandatory_field="";$xyz_cfl_add_sc="";
if(isset($_POST['bsettngs']))
{
	
	$xyz_cfl_mandatory_field=$_POST['xyz_cfl_mandatory_field'];
	$xyz_cfl_add_sc=$_POST['xyz_cfl_shortcode_field'];
	$xyz_credit_link=$_POST['xyz_credit_link'];
	if($xyz_cfl_mandatory_field=="")
	{
		$flag=0;
		$msg1="Please select mandatory field.";
	}
	else if($xyz_cfl_add_sc=="")
	{
		$flag=0;
		$msg1="Please select add shortcode field.";
	}
	if($flag==1)
	{
		update_option('xyz_cfl_mandatory_field',$xyz_cfl_mandatory_field);
		update_option('xyz_cfl_shortcode_field',$xyz_cfl_add_sc);
		update_option('xyz_credit_link', $xyz_credit_link);
	}
}
$xyz_credit_link=esc_html(get_option('xyz_credit_link'));
$xyz_cfl_mandatory_field= get_option('xyz_cfl_mandatory_field') ;
$xyz_cfl_add_sc= get_option('xyz_cfl_shortcode_field') ;
?>
<fieldset style="width: 99%; border: 1px solid #F7F7F7; padding: 10px 0px;">
<legend>
<span class="xyz_cfl_h2">Basic Settings</span>
</legend>
<form method="post" >

<table  class="widefat  xyz_cfl_table" style="width:98%;padding-top: 10px;" class="xyz_cfl_table">


<tr valign="top">

<td scope="row" colspan="1" width="50%">If mandatory field is not filled <span class="mandatory">*</span>	</td><td>
<select name="xyz_cfl_mandatory_field" >

<option value ="0" <?php if($xyz_cfl_mandatory_field=='0') echo 'selected'; ?> >Set default value for the field</option>

<option value ="1" <?php if($xyz_cfl_mandatory_field=='1') echo 'selected'; ?> >Save post as draft </option>
</select> 
</td></tr>
 <?php if($xyz_cfl_add_sc=='0') $display="none";
 else $display="";?>
<tr valign="top">

<td scope="row" colspan="1" width="50%">Add shortcode for displaying custom field values<span class="mandatory">*</span>	</td><td>
<select name="xyz_cfl_shortcode_field"  id="xyz_cfl_shortcode_field">

<option value ="0" <?php if($xyz_cfl_add_sc=='0') echo 'selected'; ?> >Automatically</option>

<option value ="1" <?php if($xyz_cfl_add_sc=='1') echo 'selected'; ?> >Manually </option>
</select> 
<span id="shortcode" style="display: <?php echo $display;?>"><b>Use shortcode : [xyz_cfl_shortcode id= "{POST_ID}"]</b></span></td></tr>
<tr valign="top">
	<td scope="row" colspan="1"><label for="xyz_credit_link">Enable credit link to author ? </label><span class="mandatory">*</span></td>
	<td>
		<select name="xyz_credit_link" id="xyz_credit_link" >
			<option value ="cfl" <?php if($xyz_credit_link=='cfl') echo 'selected'; ?> >Yes </option>
			<option value ="<?php echo $xyz_credit_link!='cfl'?$xyz_credit_link:0;?>" <?php if($xyz_credit_link!='cfl') echo 'selected'; ?> >No </option>
		</select> 
	</td>
</tr>
<tr>
 <td   id="bottomBorderNone">&nbsp;</td>
 <td   id="bottomBorderNone" style="height: 50px">
	<input type="submit" class="submit_cfl_new" style=" margin-top: 10px;" name="bsettngs" value="Update Settings" />
 </td>
</tr>



</table>
</form>
</fieldset>	
<script>
jQuery(document).ready(function(){


	jQuery("#xyz_cfl_shortcode_field").change(function(){
		if(jQuery("#xyz_cfl_shortcode_field").val()==0 ){
			jQuery("#shortcode").hide();		}
		else jQuery("#shortcode").show();	
	});
});
 </script>
