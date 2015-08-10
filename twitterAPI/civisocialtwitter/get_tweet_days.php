<?php
// 


function get_tweet_days() {
	global $db;

	$query = "SELECT dayname(created_at) AS tweet_day, count(*) AS cnt
		FROM tweets
		GROUP BY dayname(created_at)
		ORDER BY dayofweek(created_at)";

	return $db->select_array($query);
}
?>