<?php
// 


function get_engagement_mentioners($where,$start,$results_per_page) {
	global $db, $engagement_user_id;

	$query = "SELECT COUNT( * ) AS cnt, users.* 
		FROM tweet_mentions, users
		WHERE tweet_mentions.source_user_id = users.user_id
		AND tweet_mentions.target_user_id = $engagement_user_id
		$where
		GROUP BY tweet_mentions.source_user_id
		ORDER BY cnt DESC 
		LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>