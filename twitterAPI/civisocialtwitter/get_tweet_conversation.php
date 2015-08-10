<?php
// 


function get_tweet_conversation($partner,$where,$start,$results_per_page) {
	global $db, $engagement_user_id;
	
	// The report page collected the screen name of the conversation partner
	// We need to look up the matching user_id from the users table
	$query = "SELECT user_id
		FROM users
		WHERE screen_name = '$partner'";
	$results = $db->select($query);
	$row = mysqli_fetch_assoc($results);
	$partner_user_id = $row['user_id'];
	
	// Get the tweets where this user mentioned or retweeted the engagement account
	// Combine them with tweets where the engagement account interacted with this user	
	$query = "SELECT users.user_id, users.screen_name, users.name, users.profile_image_url, 
		tweets.tweet_id, tweets.created_at, tweets.tweet_text, tweets.retweet_count
		FROM tweets, users, tweet_mentions
		WHERE tweets.user_id = users.user_id
		AND ((tweets.tweet_id = tweet_mentions.tweet_id AND
			tweet_mentions.source_user_id = $partner_user_id AND
			tweet_mentions.target_user_id = $engagement_user_id)
			OR (tweets.tweet_id = tweet_mentions.tweet_id AND
			tweet_mentions.source_user_id = $engagement_user_id AND
			tweet_mentions.target_user_id = $partner_user_id))
		$where
		UNION
		SELECT users.user_id, users.screen_name, users.name, users.profile_image_url, 
		tweets.tweet_id, tweets.created_at, tweets.tweet_text, tweets.retweet_count
		FROM tweets, users, tweet_retweets
		WHERE tweets.user_id = users.user_id
		AND ((tweets.tweet_id = tweet_retweets.tweet_id AND
			tweet_retweets.source_user_id = $engagement_user_id AND
			tweet_retweets.target_user_id = $partner_user_id)
			OR (tweets.tweet_id = tweet_retweets.tweet_id AND
			tweet_retweets.source_user_id = $partner_user_id AND
			tweet_retweets.target_user_id = $engagement_user_id))
		AND NOT (tweets.is_rt and tweets.user_id = $engagement_user_id)
		$where		
		ORDER BY created_at ASC 
		LIMIT $start, $results_per_page";
		
	return $db->select_array($query);
}
?>