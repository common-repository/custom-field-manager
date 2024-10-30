<?php ?>
<div style="margin-top: 10px">
	<table style="float:right; ">
		<tr>
			<td style="float:right;">
				<a class="xyz_cfl_link"  target="_blank" href="http://kb.xyzscripts.com/wordpress-plugins/custom-field-manager/" style="margin-right:12px;">FAQ</a> 
			</td>
			<td style="float:right;">
				<a class="xyz_cfl_link"  target="_blank" href="http://docs.xyzscripts.com/wordpress-plugins/custom-field-manager/">Readme</a> | 
			</td>
			<td style="float:right;">
				<a class="xyz_cfl_link"  target="_blank" href="http://xyzscripts.com/wordpress-plugins/custom-field-manager/details">About</a> | 
			</td>
			<td style="float:right;">
				<a class="xyz_cfl_link"  target="_blank" href="http://xyzscripts.com">XYZScripts</a> |
			</td>
		</tr>
	</table>
</div>

<div style="clear: both"></div>

<?php 
if($_POST && isset($_POST['xyz_credit_link']))
{
	
	$xyz_credit_link=$_POST['xyz_credit_link'];
	
	update_option('xyz_credit_link', $xyz_credit_link);
	?>
<div class="system_notice_area_style1" id="system_notice_area">
	Settings updated successfully. &nbsp;&nbsp;&nbsp;<span id="system_notice_area_dismiss">Dismiss</span>
</div>
	<?php 
}?>


<?php 

if(get_option('xyz_credit_link')=="0"){
	?>
<div style="float:left;background-color: #FFECB3;border-radius:5px;padding: 0px 5px;margin-top: 10px;border: 1px solid #E0AB1B" id="xyz_backlink_div">

	Please do a favour by enabling backlink to our site. <a id="xyz_cfl_backlink" style="cursor: pointer;" >Okay, Enable</a>.
<script type="text/javascript">
jQuery(document).ready(function() {

	jQuery('#xyz_cfl_backlink').click(function() {


		var dataString = { 
				action: 'xyz_cfl_ajax_backlink', 
				enable: 1 
			};

		jQuery.post(ajaxurl, dataString, function(response) {
			jQuery("#xyz_backlink_div").html('Thank you for enabling backlink !');
			jQuery("#xyz_backlink_div").css('background-color', '#D8E8DA');
			jQuery("#xyz_backlink_div").css('border', '1px solid #0F801C');
			jQuery("select[id=xyz_credit_link] option[value=cfl]").attr("selected", true);
		});

});
});
</script>
</div>
	<?php 
}
?>