<?php



function collect_account_profiles($query) {	
	
	require('config.php');
	require('oauth_lib.php');
	$connection = get_connection();	
	require('db_lib.php');
	$db = new db();
	
	// Run the SQL query passed to this script
	$results = $db->select($query);
	
	// Stop if there are no users to process
	if (mysqli_num_rows($results)==0) {
		print 'All user accounts for this query have been collected';
		return;
	}

	while(true) {
		// Get 100 user_ids as comma delimited list
		$id_count = 0;
		$user_list = '';
		while(($row=mysqli_fetch_assoc($results)) && ($id_count<100)) {
			$user_list .= $row['user_id'] . ',';
			++$id_count;
		}
		
		// Exit if no additional users are found
		if ($id_count==0) {
			return;
		}
		
		// Clip off the trailing comma after the last user_id
		$user_list = substr($user_list,0,strlen($user_list)-1);

		// Get the account profiles for these users
		$connection->request('GET', $connection->url('1.1/users/lookup'), 
			array('user_id' => $user_list));
		
		$response_code = $connection->response['code'];
		
		if ($response_code <> 200) {
		  print "Error: $response_code\n";
		  print $connection->response['response'];
		  return;
		  
		} else {
			
			// Convert the API JSON result into a PHP object
			$response_data = json_decode($connection->response['response']);
			
			// Extract each user's data
			foreach ($response_data as $user) {
				$user_id = $user->id;
				$name = $db->escape($user->name);
				$screen_name = $db->escape($user->screen_name);
				$profile_image_url = $db->escape($user->profile_image_url);
				$location = $db->escape($user->location);	
				$description = $db->escape($user->description);
				$url = $db->escape($user->url);
				$created_at = $db->date($user->created_at);
				$friends_count = $user->friends_count;
				$followers_count = $user->followers_count;
				$statuses_count = $user->statuses_count;
				$listed_count = $user->listed_count;
				$lang = $db->escape($user->lang);
				if (empty($user->protected)) {
					$protected = 0;
					
					// Check to see if this user has ever tweeted
					if (isset($user->status)) { 
						$last_tweet_date = $db->date($user->status->created_at);
					} else {
						// If there are no tweets, 
						// set the $last_tweet_date to show that
						$last_tweet_date = '0000-00-00';
					}
				} else {
					$protected = 1;
					
					// If the user is protected, no tweet is returned 
					$last_tweet_date = '0000-00-00';
				}		
				
				$field_values = "user_id=$user_id, name='$name', screen_name='$screen_name', profile_image_url='$profile_image_url',
					location='$location', description='$description', url='$url', created_at='$created_at', friends_count=$friends_count,
					followers_count=$followers_count, statuses_count=$statuses_count, listed_count=$listed_count, lang='$lang',
					protected=$protected, suspended=0, last_tweet_date='$last_tweet_date'";
				
				if (!$db->in_table('users',"user_id=$user_id")) {
					$db->insert('users',$field_values);
				} else {
					$db->update('users',$field_values,"user_id=$user_id");
				}
			}
		}
	}
}
?>