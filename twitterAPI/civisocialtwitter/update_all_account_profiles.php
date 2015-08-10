<?php
// 


$query = "SELECT user_id
		FROM users
		WHERE last_updated < now() - INTERVAL 24 hour
		LIMIT 15000";
	
require('collect_account_profiles.php');	
collect_account_profiles($query);
?>