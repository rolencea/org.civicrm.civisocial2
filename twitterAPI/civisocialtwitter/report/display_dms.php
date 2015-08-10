<?php
// 


if(sizeof($dms)) {
	require_once('../display_lib.php');	
	$dm_html = '<link rel="stylesheet" type="text/css" media="all" href="dm.css" />';
	$template_html = file_get_contents('dm_template.html');
	foreach($dms as $dm) {
		$dm_html .= $template_html;
		$dm_html = str_replace('[sender_user_id]',$dm['sender_user_id'],$dm_html);
		$dm_html = str_replace('[sender_screen_name]',$dm['sender_screen_name'],$dm_html);
		$dm_html = str_replace('[sender_name]',$dm['sender_name'],$dm_html);
		$dm_html = str_replace('[sender_profile_image_url]',$dm['sender_profile_image_url'],$dm_html);
		$dm_html = str_replace('[recipient_user_id]',$dm['recipient_user_id'],$dm_html);
		$dm_html = str_replace('[recipient_screen_name]',$dm['recipient_screen_name'],$dm_html);
		$dm_html = str_replace('[recipient_name]',$dm['recipient_name'],$dm_html);
		$dm_html = str_replace('[recipient_profile_image_url]',$dm['recipient_profile_image_url'],$dm_html);
		$dm_html = str_replace('[dm_text]',linkify($dm['dm_text']),$dm_html);
		$dm_html = str_replace('[created_at]',twitter_time($dm['created_at']),$dm_html);
		
	}
	
	print $dm_html;
} else {
	print "No dms found";
}

?>