<?php
// 


$query = "SELECT DISTINCT target_user_id AS user_id
	FROM tweet_retweets
	WHERE target_user_id NOT IN
		(SELECT user_id
		FROM users)
	LIMIT 15000";
	
require('collect_account_profiles.php');	
collect_account_profiles($query);
?>