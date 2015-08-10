<?php
// 


// Get all default values, including OAuth tokens		
require('config.php');

// Create an OAuth connection
require('oauth_lib.php');
$connection = get_connection();

// Connect to the database
require('db_lib.php');
$db = new db();

// Get a leader account that has not had its old tweets collected
// Only process one leader at a time to make sure rate limits aren't exceeded
$query = "SELECT user_id 
	FROM leaders
	WHERE old_timeline_collected = '0000-00-00'
	LIMIT 1";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
if (mysqli_num_rows($results)==0) {
	
	// Stop if all leader accounts have already had old tweets collected
	print 'All old leader timelines have already been collected';
	exit;
}

// This is the leader account we will collect
$user_id = $row['user_id'];

$max_id = 0;
while (true) {
	
	if ($max_id == 0) {
		// Get the first set of tweets, so max_id is not included as a parameter
 		$connection->request('GET', $connection->url('1.1/statuses/user_timeline'), 
	      array('user_id' => $user_id,
	        'include_entities' => 'true',
	        'include_rts' => 'true',
	        'exclude_replies' => 'false',
	        'trim_user' => 'true',
	        'count' => 100));
	} else {
		// We are now past the first tweets, so get any older than the last one collected
		--$max_id;
		
		$connection->request('GET', $connection->url('1.1/statuses/user_timeline'), 
	      array('user_id' => $user_id,
	        'include_entities' => 'true',
	        'include_rts' => 'true',
	      	'exclude_replies' => 'false',
	        'trim_user' => 'true',
	        'count' => 100,
	        'max_id' => $max_id));
	}			

	// No more tweets returned for this account
	if ($connection->response['response'] == '[]') {
		break;
	} 
			
	// API call failed
	if ($connection->response['code'] != 200) {
    break;			
	} 
	
	// Convert the API results into a PHP object
	$results = json_decode($connection->response['response']);
	
	// Loop through all tweets from this request
	foreach($results as $tweet) {
    	
		$tweet_id = $tweet->id;
		$max_id = $tweet_id;
		
		// The API sometimes sends duplicate tweets
		// Ignore this one if it is already in the database
		if ($db->in_table('tweets',"tweet_id=$tweet_id")) {
			continue;
		}
		
		// Prepare all the data for insertion into the database
    $tweet_text = $db->escape($tweet->text);
		$created_at = $db->date($tweet->created_at);
		$retweet_count = $tweet->retweet_count;		
		$user_id = $tweet->user->id;
	
		if (isset($tweet->retweeted_status)){
			// This is a retweet, get the text and entities from the original tweet
			$is_rt = 1;
			$tweet_text = $db->escape($tweet->retweeted_status->text);
			$retweet_count = 0;
			$retweet_user_id = $tweet->retweeted_status->user->id; 
			$entities = $tweet->retweeted_status->entities;
		} else {
			$is_rt = 0;
			$entities = $tweet->entities;
		}
		
		// Record the tweet
		$db->insert('tweets',"tweet_id=$tweet_id,tweet_text='$tweet_text',created_at='$created_at',
			user_id=$user_id,is_rt=$is_rt,retweet_count=$retweet_count");
		
		// Record any retweets
		if ($is_rt) {
			$db->insert('tweet_retweets',"tweet_id=$tweet_id,created_at='$created_at',
				source_user_id=$user_id, target_user_id=$retweet_user_id");
		}	
		
		// Extract the hashtags from the entities object and record them			
		if ($entities->hashtags) {
			foreach($entities->hashtags as $hashtag) {
				$tag = $hashtag->text;
				$db->insert('tweet_tags',"tweet_id=$tweet_id,user_id=$user_id, created_at='$created_at',
					tag='$tag'");
			}
		}
		
		// Extract the @mentions from the entities object and record them	
		if ($entities->user_mentions) {
			foreach($entities->user_mentions as $user_mention) {
				$target_user_id = $user_mention->id;
				$db->insert('tweet_mentions',"tweet_id=$tweet_id,created_at='$created_at',
					source_user_id=$user_id, target_user_id=$target_user_id");
			}
		}
		
		// Extract the URLs from the entities object and record them	
		if ($entities->urls) {
			foreach($entities->urls as $url) {
				$url = $url->expanded_url;
				$db->insert('tweet_urls',"tweet_id=$tweet_id,created_at='$created_at',
					user_id=$user_id, url='$url'");
			}
		}		
	} 
}

// Record the fact that the old tweets for this leader account have been collected
$db->update('leaders','old_timeline_collected=now()',"user_id=$user_id");
?>