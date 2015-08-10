<?php
// 


require('config.php');
require('oauth_lib.php');
$connection = get_connection();
require('db_lib.php');
$db = new db();

// Check any user that hasn't been updated in 48 hours
$query = "SELECT user_id
		FROM users
		WHERE last_updated < now() - INTERVAL 48 hour
		LIMIT 150";
$results = $db->select($query);
while($row = mysqli_fetch_assoc($results)) {
	
	$user_id = $row['user_id'];
	
	// See if this user can be found without an error
	$connection->request('GET', $connection->url('1.1/users/show'), 
		array('user_id' => $user_id));
	$response_code = $connection->response['code'];	
	
	// 404 error says this account has been deleted
	if ($response_code == 404) {
		
		// Delete the user and all related data
		$delete_query = "DELETE
			FROM users
			WHERE user_id = $user_id";
		$db->select($delete_query);
		
		$delete_query = "DELETE
			FROM user_tags
			WHERE user_id = $user_id";
		$db->select($delete_query);
		
		$delete_query = "DELETE
			FROM tweets
			WHERE user_id = $user_id";
		$db->select($delete_query);
		
		$delete_query = "DELETE
			FROM tweet_mentions
			WHERE source_user_id = $user_id
			OR target_user_id = $user_id";
		$db->select($delete_query);
		
		$delete_query = "DELETE
			FROM tweet_retweets
			WHERE source_user_id = $user_id
			OR target_user_id = $user_id";
		$db->select($delete_query);
		
		$delete_query = "DELETE
			FROM tweet_tags
			WHERE user_id = $user_id";
		$db->select($delete_query);
		
		$delete_query = "DELETE
			FROM tweet_urls
			WHERE user_id = $user_id";
		$db->select($delete_query);
		
	// 403 is a suspended user
	} elseif ($response_code == 403) {
		$db->update('users','suspended=1',"user_id=$user_id");	

	} 
}

?>