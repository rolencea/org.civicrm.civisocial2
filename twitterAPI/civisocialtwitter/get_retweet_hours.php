<?php
// 


function get_retweet_hours() {
	global $db;

	$query = "SELECT hour(created_at) AS tweet_hour, count(*) AS cnt
		FROM tweet_retweets
		GROUP BY hour(created_at)";

	return $db->select_array($query);
}
?>