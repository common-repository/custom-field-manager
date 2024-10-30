<?php 
add_shortcode( 'xyz_cfl_shortcode' , 'xyz_cfl_shortcode' );
function xyz_cfl_shortcode($params){
	if(is_numeric(ini_get('output_buffering')))
	{
		$tmp=ob_get_contents();
		ob_clean();
		ob_start();
		require_once( dirname( __FILE__ ) . '/shortcode.php' );
		$xyz_em_content = ob_get_contents();
		ob_clean();
		echo $tmp;
		$xyz_em_content=str_replace(array("\r\n","\r","\t"),"\n",$xyz_em_content);
		do{		$xyz_em_content=str_replace("\n\n","\n",$xyz_em_content);
		}while(strpos($xyz_em_content,"\n\n") !== false);
		return $xyz_em_content;
	}
	else
	{
		require_once( dirname( __FILE__ ) . '/shortcode.php' );
	}
}

add_filter ('the_content', 'xyz_cfl_insertshortcode');
function xyz_cfl_insertshortcode($content)
{
	global $post;

	$xyz_cfl_add_sc=get_option('xyz_cfl_shortcode_field');
	$pid=$post->ID;
	$p_content=$post->post_content;
	if($xyz_cfl_add_sc==0)
	{
		//$p_content=strip_shortcodes($p_content);
		
		if (strpos($p_content,'[xyz_cfl_shortcode') != true)
		{
			if(is_single())
				$content=$p_content.'<br>[xyz_cfl_shortcode id='.$pid.']';
			return $content;
		}
		else
			return $p_content;
	}
	else
		return $p_content;
}
?>