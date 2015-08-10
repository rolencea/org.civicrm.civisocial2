<?php
// 


function get_leader_mentions($where,$start,$results_per_page) {
	global $db;

	$query = "SELECT COUNT( * ) AS cnt, users.* 
		FROM tweet_mentions, users
		WHERE tweet_mentions.source_user_id IN 
			(SELECT user_id
			FROM leaders)
		AND tweet_mentions.target_user_id = users.user_id
		$where
		GROUP BY tweet_mentions.target_user_id
		ORDER BY cnt DESC 
		LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>