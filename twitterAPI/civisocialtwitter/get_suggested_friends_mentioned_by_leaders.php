<?php
// 


function get_suggested_friends_mentioned_by_leaders($start,$results_per_page) {
	global $db, $engagement_user_id;

	$query = "SELECT users.* 
	FROM users
	WHERE not users.protected
	AND user_id NOT IN 
		(SELECT user_id
		FROM friends)
	AND user_id IN
		(SELECT target_user_id
		FROM tweet_mentions
		WHERE source_user_id IN 
			(SELECT user_id
			FROM leaders
			where tweet_mentions.created_at >= now() - interval 30 day))
	ORDER BY users.followers_count DESC
	LIMIT $start, $results_per_page";

	return $db->select_array($query);
}
?>