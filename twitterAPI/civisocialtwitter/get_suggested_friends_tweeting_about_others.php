<?php
// 


function get_suggested_friends_tweeting_about_others($start,$results_per_page) {
	global $db, $engagement_user_id;

	$query = "SELECT users.* 
	FROM users
	WHERE user_id NOT IN 
		(SELECT user_id
		FROM friends)
	AND (user_id IN
		(SELECT source_user_id
		FROM tweet_mentions)
		OR user_id IN 
		(SELECT source_user_id
		FROM tweet_retweets))
	ORDER BY users.followers_count DESC  
	LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>