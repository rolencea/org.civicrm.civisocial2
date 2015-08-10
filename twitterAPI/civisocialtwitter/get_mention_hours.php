<?php
// 


function get_mention_hours() {
	global $db;

	$query = "SELECT hour(created_at) AS tweet_hour, count(*) AS cnt
		FROM tweet_mentions
		GROUP BY hour(created_at)";

	return $db->select_array($query);
}
?>