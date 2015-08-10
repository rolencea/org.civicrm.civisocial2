<?php
// 


function get_user_tags($start,$results_per_page) {
	global $db;

	$query = "SELECT COUNT( * ) AS cnt, tag
		FROM user_tags
		GROUP BY user_tags.tag
		ORDER BY cnt DESC 
		LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>