<?php
// 


// Send a tweet from the RSS feed items collected in the database
function post_rss_tweet() {
	global $db;
	
	// Get the next available RSS feed tweet
	$query = "SELECT id, tweet_text
		FROM autotweet_rss_tweets
		WHERE NOT posted
		ORDER BY pub_date DESC
		LIMIT 1";
	$results = $db->select($query);
	if ($row = mysqli_fetch_assoc($results)) {
		$id = $row['id'];
		$tweet_text = $row['tweet_text'];
		
		// Send the tweet
		$http_code = post_tweet($tweet_text);
		
		// Record the fact that it was sent
		$db->update('autotweet_rss_tweets','posted=1',"id=$id");
		
	} else {
		$tweet_text = '';
		$http_code = 0;
	}
	return array('http_code'=> $http_code,
		'tweet_text' => $tweet_text);
}		
	
// Send #FF tweets
// Two tweets will be sent with 5 user accounts in each
function post_ff_tweet() {
	global $db, $default_tag;
	
	// Build a list of up to 10 users for #FF tweets
	$user_list = array();
	
	// Get most mentioned users
	// Don't use leaders or those mentioned by leaders 
	// They will tend to repeat too often
	$query = "SELECT count(*) AS cnt, users.screen_name
		FROM tweet_mentions, users
		WHERE tweet_mentions.target_user_id = users.user_id
		AND tweet_mentions.created_at >= now() - interval 7 day
		AND tweet_mentions.source_user_id NOT IN (
			SELECT user_id 
			FROM leaders)
		AND users.user_id NOT IN (
			SELECT user_id 
			FROM leaders)
		GROUP BY tweet_mentions.target_user_id
		ORDER BY cnt DESC 
		LIMIT 5";

	// Add these users to the $user_list array
	$results = $db->select($query);
	while($row = mysqli_fetch_assoc($results)) {
		$user_list[] = $row['screen_name'];
	}

	// Get most retweeted users
	// Don't use leaders or those retweeted by leaders 
	// They will repeat too often	
	$query = "SELECT count(*) AS cnt, users.screen_name
		FROM tweet_retweets, users
		WHERE tweet_retweets.target_user_id = users.user_id
		AND tweet_retweets.created_at >= now() - interval 7 day
		AND tweet_retweets.source_user_id NOT IN (
			SELECT user_id 
			FROM leaders)
		AND users.user_id NOT IN (
			SELECT user_id 
			FROM leaders)
		GROUP BY tweet_retweets.target_user_id
		ORDER BY cnt DESC 
		LIMIT 10";

	// Add these users to $user_list array, stop at 10 total
	$results = $db->select($query);
	while(($row = mysqli_fetch_assoc($results)) && (sizeof($user_list) < 10)) {
		if (!in_array($row['screen_name'], $user_list)) {
			$user_list[] = $row['screen_name'];
		}
	}	
	
	// Send a tweet with the first 5 users
	// $default_tag comes from config.php
	$tweet_text1 = '#FF';
	for($i=0;$i<5;++$i) {
		$tweet_text1 .= ' @' . $user_list[$i];
	}
	$tweet_text1 .= " $default_tag";
	$http_code1 = post_tweet($tweet_text1);

	// Send a tweet with the next 5 users		
	$tweet_text2 = '#FF';
	for($i=5;$i<10;++$i) {
		$tweet_text2 .= ' @' . $user_list[$i];
	}
	$tweet_text2 .= " $default_tag";
	$http_code2 = post_tweet($tweet_text2);

	// Return results of both tweets
	return array('http_code1' => $http_code1, 'tweet_text1' => $tweet_text1,
		'http_code2' => $http_code2, 'tweet_text2' => $tweet_text2);
}		

// Retweet the most retweeted tweet by a leader in past 24 hours
// Check the autotweet_log table to prevent duplicates
function post_leader_retweet() {
	global $db;
	$query = "SELECT tweet_id, tweet_text
		FROM tweets
		WHERE tweet_id NOT IN 
			(SELECT tweet_id
			FROM autotweet_log
			WHERE tweet_type = 'leader rt')
		AND user_id IN
			(SELECT user_id
			FROM leaders)
		AND created_at >= now() - interval 24 hour
		AND retweet_count >= 10
		ORDER BY retweet_count DESC
		LIMIT 1";
	$results = $db->select($query);
	if ($row = mysqli_fetch_assoc($results)) {
		$tweet_text = $row['tweet_text'];
		$tweet_id = $row['tweet_id'];
		$http_code = post_retweet($tweet_id);
	} else {
		$tweet_text = '';
		$tweet_id = 0;
		$http_code = 0;
	}
	return array('http_code' => $http_code,
		'tweet_text' => $tweet_text,
		'tweet_id' => $tweet_id);
}	

// Send a tweet
function post_tweet($tweet_text) {
	global $connection;
	
  	$connection->request('POST', $connection->url('1.1/statuses/update'), 
    	array('status' => $tweet_text));
		
	return $connection->response['code'];

} 

// Send a retweet of specified tweet_id
function post_retweet($tweet_id) {
	global $connection;
	
  	$connection->request('POST', $connection->url('1.1/statuses/retweet/' . $tweet_id));
		
	return $connection->response['code'];

} 

?>