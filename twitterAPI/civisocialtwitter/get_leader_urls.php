<?php
// 


function get_leader_urls($where,$start,$results_per_page) {
	global $db;

	$query = "SELECT COUNT( * ) AS cnt, url
		FROM tweet_urls
		WHERE tweet_urls.user_id IN	
			(SELECT user_id
			FROM leaders)
		$where
		GROUP BY tweet_urls.url
		ORDER BY cnt DESC 
		LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>