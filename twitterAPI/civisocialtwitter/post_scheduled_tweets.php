<?php
// 


// Run this as a cronjob every minute

require('config.php');
require('db_lib.php');
$db = new db();
require('oauth_lib.php');
$connection = get_connection();

// Load tweeting functions
require('autotweet_lib.php');

// Get any tweets scheduled for the current day and time
$query = "SELECT id, tweet_text
	FROM autotweet_scheduled
	WHERE (tweet_date = date(now())
		AND tweet_hour = hour(now())
		AND tweet_minute = minute(now()))
	AND NOT posted";
$results = $db->select($query);
while ($row = mysqli_fetch_assoc($results)) {
	$id = $row['id'];
	$tweet_text = $row['tweet_text'];
	
	// Post this tweet
  	$http_code = post_tweet($tweet_text);

	// Flag it as having been posted
	// Even if the $http_code is not 200, this is still flagged as posted
	// It is safer to not try to repeat this posting to avoid duplication
	$db->update('autotweet_scheduled','posted=1',"id=$id");
	
	// Log this tweet
	$db->insert('autotweet_log',"tweet_type='text', scheduled=1, id=$id, tweet_text='$tweet_text', http_code=$http_code");
}

?>