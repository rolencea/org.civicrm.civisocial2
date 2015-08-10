<?php
// 


function get_user_profile($user_id) {
	global $db;

	$query = "SELECT * 
		FROM users
		WHERE user_id = $user_id";

	return $db->select_array($query);
}
?>