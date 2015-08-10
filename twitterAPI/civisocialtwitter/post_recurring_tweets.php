<?php
// 


// Run as cronjob every minute

require('config.php');
require('db_lib.php');
$db = new db();
require('oauth_lib.php');
$connection = get_connection();
require('autotweet_lib.php');

$current_dow = date('N',strtotime('now'));

// Get all recurring tweets for current day and time
$query = "SELECT tweet_type, id, tweet_text
	FROM autotweet_recurring
	WHERE dow LIKE '%$current_dow%'
		AND tweet_hour = hour(now())
		AND tweet_minute = minute(now())";
$results = $db->select($query);
while ($row = mysqli_fetch_assoc($results)) {
	$tweet_type = $row['tweet_type'];
	$id = $row['id'];
	$tweet_text = $row['tweet_text'];
	
	// Post the current tweet based on its type
	// The matching tweet functions are in autotweet_lib.php
	switch ($tweet_type) {
		case 'text':
			$http_code = post_tweet($tweet_text);
			$db->insert('autotweet_log',"tweet_type='text', recurring=1, id=$id, tweet_text='$tweet_text',http_code=$http_code");
			break;
			
		case 'rss':
			$post_result = post_rss_tweet();
			$tweet_text = $post_result['tweet_text'];
			$http_code = $post_result['http_code'];
			$db->insert('autotweet_log',"tweet_type='rss', recurring=1, id=$id, 
				tweet_text='$tweet_text', http_code=$http_code");			
			break;
			
		case 'ff':
			
			// #FF tweets are sent as a pair with 5 users each
			$post_result = post_ff_tweet();
			$tweet_text = $post_result['tweet_text1'];
			$http_code = $post_result['http_code1'];
			$db->insert('autotweet_log',"tweet_type='ff', recurring=1, id=$id, 
				tweet_text='$tweet_text', http_code=$http_code");
					
			$tweet_text = $post_result['tweet_text2'];
			$http_code = $post_result['http_code2'];
			$db->insert('autotweet_log',"tweet_type='ff', recurring=1, id=$id, 
				tweet_text='$tweet_text', http_code=$http_code");				
			break;
			
		case 'leader_retweet':
			$post_result = post_leader_retweet();
			$tweet_text = $post_result['tweet_text'];
			$tweet_id = $post_result['tweet_id'];
			$http_code = $post_result['http_code'];
			$db->insert('autotweet_log',"tweet_type='leader_retweet', recurring=1, id=$id, 
				tweet_text='$tweet_text', tweet_id='$tweet_id', http_code=$http_code");
			break;
	}
}

?>