<?php
// 


require('config.php');
require('oauth_lib.php');
$connection = get_connection();
require('db_lib.php');
$db = new db();

$query = "SELECT user_id, screen_name
	FROM leaders";
$results = $db->select($query);
while($row=mysqli_fetch_assoc($results)) {

	// Get leader account's info
	$connection->request('GET', $connection->url('1.1/users/show'), 
		array('user_id' => $row['user_id']));
	
	// Get the HTTP response code for the API request
	$response_code = $connection->response['code'];
	
	// A response code of 200 is a success
	if ($response_code <> 200) {
	  print "Error for @" . $row['screen_name'] . ": $response_code\n";
	  print $connection->response['response'];
	  
	} else {

		$user = json_decode($connection->response['response']);
		
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
			// If an account is not protected, 
			// the $user->protected element is blank
			$protected = 0;
		} else {
			$protected = 1;
		}
		$last_tweet_date = $db->date($user->status->created_at);
		
		$field_values = "user_id=$user_id, name='$name', screen_name='$screen_name', profile_image_url='$profile_image_url',
			location='$location', description='$description', url='$url', created_at='$created_at', friends_count=$friends_count,
			followers_count=$followers_count, statuses_count=$statuses_count, listed_count=$listed_count, lang='$lang',
			protected=$protected, last_tweet_date='$last_tweet_date'";
		
		if (!$db->in_table('users',"user_id=$user_id")) {
			$db->insert('users',$field_values);
		} else {
			$db->update('users',$field_values,"user_id=$user_id");
		}
	}
}

?>