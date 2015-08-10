<?php
// 


function get_leader_tags($where,$start,$results_per_page) {
	global $db;

	$query = "SELECT COUNT( * ) AS cnt, tag
		FROM tweet_tags
		WHERE tweet_tags.user_id IN
			(SELECT user_id
			FROM leaders)
		$where
		GROUP BY tweet_tags.tag
		ORDER BY cnt DESC, tag ASC 
		LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>