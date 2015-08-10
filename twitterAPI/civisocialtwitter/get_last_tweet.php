<?php
// 


function get_last_tweet($user_id) {
	global $db;

	$query = "SELECT users.screen_name, users.name, users.profile_image_url,
		tweets.user_id, tweets.tweet_id, tweets.tweet_text, tweets.created_at, tweets.retweet_count
		FROM tweets, users
		WHERE users.user_id = $user_id
		AND tweets.user_id = users.user_id
		ORDER BY tweets.created_at DESC
		LIMIT 1";

	return $db->select_array($query);
}
?>