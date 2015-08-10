<?php
// 


require('config.php');
require('db_lib.php');
$db = new db();
require('oauth_lib.php');
$connection = get_connection();

require('autotweet_lib.php');

// Find out how many follow tweets have been sent today
$query = "SELECT count(*) AS cnt
	FROM autotweet_log
	WHERE tweet_type = 'follow_tweet'
	AND date(created_at) = date(now())";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);

// $max_daily_follow_tweets is set in config.php
// Stop if limit sent today
if($row['cnt']==$max_daily_follow_tweets) {exit;}

// Send a tweet to the most recent follower
$query = "SELECT follow_log.id, users.screen_name
	FROM follow_log, users
	WHERE follow_log.user_id = users.user_id
	AND follow_log.event = 'follow'
	AND NOT tweet_sent
	ORDER BY follow_log.created_at DESC
	LIMIT 1";
$results = $db->select($query);
while ($row = mysqli_fetch_assoc($results)) {
	$id = $row['id'];
	
	// $default_tag is found in config.php
	$tweet_text = "Thanks for the follow @" . $row['screen_name'] . " $default_tag";
  $http_code = post_tweet($tweet_text);
	$db->insert('autotweet_log',"tweet_type='follow_tweet', tweet_text='$tweet_text', http_code=$http_code");
	$db->update('follow_log','tweet_sent=1',"id=$id");
}

?>