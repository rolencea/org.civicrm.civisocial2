<?php
// 


function get_mutual_follows($start,$results_per_page) {
	global $db;

	$query = "SELECT users.* 
		FROM friends, followers, users
		WHERE followers.user_id = friends.user_id
		AND friends.user_id = users.user_id
		ORDER BY users.followers_count DESC
		LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>