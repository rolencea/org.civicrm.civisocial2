<?php
// 


// Get default values
require('config.php');

// Create an OAuth connection
require('oauth_lib.php');
$connection = get_connection();

// Create a MySQL database connection
require('db_lib.php');
$db = new db();
	
// Get the engagement account's info
// The $engagement_user_id is in config.php
$connection->request('GET', $connection->url('1.1/users/show'), 
	array('user_id' => $engagement_user_id
));

// Get the HTTP response code for the API request
$response_code = $connection->response['code'];

// A response code that is not 200 is an error
if ($response_code <> 200) {
  print "Error: $response_code\n";
  print $connection->response['response'];
} else {
	
	// Convert the API's JSON response into a PHP object
	$user = json_decode($connection->response['response']);
	
	// Collect all the user data for insertion into the database
	// Escape all text fields that may contain quotes
	$user_id = $user->id;
	$name = $db->escape($user->name);
	$screen_name = $db->escape($user->screen_name);
	$profile_image_url = $db->escape($user->profile_image_url);
	$location = $db->escape($user->location);	
	$description = $db->escape($user->description);	
	$created_at = $db->date($user->created_at);
	$friends_count = $user->friends_count;
	$followers_count = $user->followers_count;
	$statuses_count = $user->statuses_count;
	$listed_count = $user->listed_count;
	$lang = $db->escape($user->lang);
	$url = $db->escape($user->url);
	
	// The protected element is blank if the user is not protected
	if (empty($user->protected)) {
		$protected = 0;
	} else {
		$protected = 1;
	}
	// Get the creation date of the user's last tweet
	$last_tweet_date = $db->date($user->status->created_at);
	
	$field_values = "user_id=$user_id, name='$name', screen_name='$screen_name', profile_image_url='$profile_image_url',
		location='$location', description='$description', url='$url', created_at='$created_at', friends_count=$friends_count,
		followers_count=$followers_count, statuses_count=$statuses_count, listed_count=$listed_count, lang='$lang',
		protected=$protected, last_tweet_date='$last_tweet_date'";
	
	// Insert this user if not already in database
	if (!$db->in_table('users',"user_id=$user_id")) {
		$db->insert('users',$field_values);
	} else {
		// Update if already in the database
		$db->update('users',$field_values,"user_id=$user_id");
	}

	// Gather the data from the user's last tweet
	$tweet_id = $user->status->id;
	$tweet_text = $db->escape($user->status->text);
	$retweet_count = $user->status->retweet_count;
		
	// Insert this tweet if not already in database
	if (!$db->in_table("tweets","tweet_id=$tweet_id")) {
		$field_values = "tweet_id=$tweet_id, tweet_text='$tweet_text', created_at='$last_tweet_date',
			user_id='$user_id', retweet_count=$retweet_count";
		$db->insert('tweets',$field_values);
		echo "yes in db";
	} else {
		// Update if already in the database
		$db->update('tweets',"retweet_count=$retweet_count","tweet_id=$tweet_id");	
		echo "updated";	
	}	


}

?>