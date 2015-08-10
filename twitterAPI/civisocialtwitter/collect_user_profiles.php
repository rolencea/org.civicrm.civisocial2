<?php
// 


function collect_user_profiles($user_list) {	
	
	global $db, $connection;

	// Get account info based on a list of user_ids
	$connection->request('GET', $connection->url('1.1/users/lookup'), array(
	  'user_id' => $user_list
	));
	
	// Get the HTTP response code for the API request
	$response_code = $connection->response['code'];
	
	// A response code of 200 is a success
	if ($response_code <> 200) {
	  print "Error: $response_code\n";
	  print $connection->response['response'];
	  return $response_code;
	  
	} else {
		
		// Convert the JSON response into an array
		$response_data = json_decode($connection->response['response'],true);
		
		foreach ($response_data as $user) {
			$user_id = $user['id'];
			$name = $db->escape($user['name']);
			$screen_name = $db->escape($user['screen_name']);
			$profile_image_url = $db->escape($user['profile_image_url']);
			$location = $db->escape($user['location']);	
			$description = $db->escape($user['description']);
			$url = $db->escape($user['url']);
			$created_at = $db->date($user['created_at']);
			$friends_count = $user['friends_count'];
			$followers_count = $user['followers_count'];
			$statuses_count = $user['statuses_count'];
			$listed_count = $user['listed_count'];
			$lang = $db->escape($user['lang']);
			if (empty($user['protected'])) {
				$protected = 0;
				if (isset($user['status'])) { 
					$last_tweet_date = $db->date($user['status']['created_at']);
				} else {
					$last_tweet_date = '0000-00-00';
				}
			} else {
				$protected = 1;
				$last_tweet_date = '0000-00-00';
			}		
			
			$field_values = "user_id=$user_id, name='$name', screen_name='$screen_name', profile_image_url='$profile_image_url',
				location='$location', description='$description', url='$url', created_at='$created_at', friends_count=$friends_count,
				followers_count=$followers_count, statuses_count=$statuses_count, listed_count=$listed_count, lang='$lang',
				protected=$protected, last_tweet_date='$last_tweet_date'";

			if (!$db->in_table("users","user_id=$user_id")) {
				$db->insert("users",$field_values);
				
			} else {
				$db->update("users",$field_values,"user_id=$user_id");
				
			}
		}
	}
	return $response_code;
}
?>