<?php
// 


// If there is any data to display
if(sizeof($users)) {
	require_once('../display_lib.php');
	
	$user_html = '<link rel="stylesheet" type="text/css" media="all" href="user.css" />';
	$template_html = file_get_contents('user_template.html');
	foreach($users as $user) {
		$user_html .= $template_html;	
		$user_html = str_replace('[user_id]',$user['user_id'],$user_html);
		$user_html = str_replace('[screen_name]',$user['screen_name'],$user_html);
		$user_html = str_replace('[name]',$user['name'],$user_html);
		$user_html = str_replace('[profile_image_url]',$user['profile_image_url'],$user_html);
		$user_html = str_replace('[created_at]',$user['created_at'],$user_html);
		$user_html = str_replace('[description]',$user['description'],$user_html);
		$user_html = str_replace('[location]',$user['location'],$user_html);
		$user_html = str_replace('[url]',$user['url'],$user_html);
		$user_html = str_replace('[followers_count]',number_format($user['followers_count']),$user_html);
		$user_html = str_replace('[friends_count]',number_format($user['friends_count']),$user_html);
		$user_html = str_replace('[statuses_count]',number_format($user['statuses_count']),$user_html);
		$user_html = str_replace('[listed_count]',number_format($user['listed_count']),$user_html);
		if ($user['protected']) {
			$user_html = str_replace('[protected]','<img src="images/protected.png" alt="Account is protected" title="Account is protected" style="margin-bottom: -1px;"/>',$user_html);
		} else {
			$user_html = str_replace('[protected]','',$user_html);
		} 
		if ($user['suspended']) {
			$user_html = str_replace('[suspended]','<img src="images/suspended.png" alt="Accunt is suspended" title="User is suspended" style="margin-bottom: -1px;" />',$user_html);
		} else {
			$user_html = str_replace('[suspended]','',$user_html);
		}
	}
	print $user_html;
} else {
	print "No users found";
}

?>