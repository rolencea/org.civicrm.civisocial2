<?php
// 


function get_engagement_tweets($where,$start,$results_per_page) {
	global $db, $engagement_user_id;

	// Get tweets sent by the engagement account or mentioning it
	$query = "SELECT tweets.tweet_id, tweets.created_at, tweets.tweet_text, tweets.retweet_count, 
		users.user_id, users.screen_name, users.name, users.profile_image_url
		FROM tweets, users
		WHERE tweets.user_id = users.user_id 
		AND (tweets.user_id = $engagement_user_id	
			OR tweets.tweet_id IN 
				(SELECT tweet_id 
				FROM tweet_mentions
				WHERE target_user_id = $engagement_user_id))
		$where
		ORDER BY tweets.created_at DESC 
		LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>