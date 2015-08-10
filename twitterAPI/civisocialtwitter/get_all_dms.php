<?php
// 


function get_all_dms($where,$start,$results_per_page) {
	global $db, $engagement_user_id;
	
	$query = "SELECT t1.user_id AS sender_user_id, t1.screen_name AS sender_screen_name, 
		t1.name AS sender_name, t1.profile_image_url AS sender_profile_image_url,
		t2.user_id AS recipient_user_id, t2.screen_name AS recipient_screen_name, 
		t2.name aS recipient_name, t2.profile_image_url AS recipient_profile_image_url,
		dms.created_at, dms.dm_text
		FROM dms, users t1, users t2
		WHERE dms.sender_user_id = t1.user_id
		AND dms.recipient_user_id = t2.user_id
		AND dms.recipient_user_id =$engagement_user_id
		AND dms.received 
		$where
		UNION 
		SELECT t1.user_id AS sender_user_id, t1.screen_name AS sender_screen_name, 
		t1.name AS sender_name, t1.profile_image_url AS sender_profile_image_url,
		t2.user_id AS recipient_user_id, t2.screen_name AS recipient_screen_name, 
		t2.name aS recipient_name, t2.profile_image_url AS recipient_profile_image_url,
		dms.created_at, dms.dm_text
		FROM dms, users t1, users t2
		WHERE dms.sender_user_id = t1.user_id
		AND dms.recipient_user_id = t2.user_id
		AND dms.sender_user_id = $engagement_user_id
		AND dms.sent
		$where
		ORDER BY created_at ASC 
		LIMIT $start, $results_per_page";
		
	return $db->select_array($query);
}
?>