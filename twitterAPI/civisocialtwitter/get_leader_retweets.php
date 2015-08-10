<?php
// 


function get_leader_retweets($where,$start,$results_per_page) {
	global $db;

	$query = "SELECT COUNT( * ) AS cnt, users.* 
		FROM tweet_retweets, users
		WHERE tweet_retweets.source_user_id IN 
			(SELECT user_id
			FROM leaders)
		AND tweet_retweets.target_user_id = users.user_id
		$where
		GROUP BY tweet_retweets.target_user_id
		ORDER BY cnt DESC 
		LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>