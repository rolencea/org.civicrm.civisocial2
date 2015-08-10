<?php
// 


function get_search_users($where,$start,$results_per_page) {
	global $db;

	$query = "SELECT * 
		FROM users
		$where
		ORDER BY followers_count DESC
		LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>