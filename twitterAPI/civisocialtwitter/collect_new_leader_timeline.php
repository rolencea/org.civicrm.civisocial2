<?php
// 


require('config.php');
require('oauth_lib.php');
$connection = get_connection();
require('db_lib.php');
$db = new db();

// Get all the leader accounts that have had their old tweets collected
$query = "SELECT user_id 
	FROM leaders
	WHERE old_timeline_collected != '0000-00-00'";
$results = $db->select($query);
while ($row = mysqli_fetch_assoc($results)) {
	$user_id = $row['user_id'];
	collect_tweets($user_id);
	$db->update('leaders','new_timeline_collected=now()',"user_id=$user_id");
}

// Get all new tweets by this user
function collect_tweets($user_id) {
	global $db, $connection;

	// Get the highest tweet_id (newest tweet) for this user
	$query = "SELECT max(tweet_id) AS since_id
		FROM tweets
		WHERE user_id = $user_id"; 
	$results = $db->select($query);
	$row = mysqli_fetch_assoc($results);
	$since_id = $row['since_id'];
	
	while (true) {
		
		$connection->request('GET', $connection->url('1.1/statuses/user_timeline'), 
	      array('user_id' => $user_id,
	        'include_entities' => 'true',
	        'include_rts' => 'true',
	        'exclude_replies' => 'false',
	        'trim_user' => 'true',
	        'count' => 100,
	        'since_id' => $since_id));
	
		if ($connection->response['response'] == '[]') {
	  	return;
	  } 
	  if ($connection->response['code'] != 200) {
	  	return;			
		} 
		
		$results = json_decode($connection->response['response']);
	  foreach($results as $tweet) {
	    	
			$tweet_id = $tweet->id;
			$since_id = $tweet_id;
			
			if ($db->in_table('tweets',"tweet_id=$tweet_id")) {
				continue;
			}
			
	    $tweet_text = $db->escape($tweet->text);
			$created_at = $db->date($tweet->created_at);
			$retweet_count = $tweet->retweet_count;		
			$user_id = $tweet->user->id;
		
			if (isset($tweet->retweeted_status)){
				$is_rt = 1;
				$tweet_text = $db->escape($tweet->retweeted_status->text);
				$retweet_count = 0;
				$retweet_user_id = $tweet->retweeted_status->user->id; 
				$entities = $tweet->retweeted_status->entities;
			} else {
				$is_rt = 0;
				$entities = $tweet->entities;
			}
			
			$db->insert('tweets',"tweet_id=$tweet_id,tweet_text='$tweet_text',created_at='$created_at',
				user_id=$user_id,is_rt=$is_rt,retweet_count=$retweet_count");
			
			if ($is_rt) {
				$db->insert('tweet_retweets',"tweet_id=$tweet_id,created_at='$created_at',
					source_user_id=$user_id, target_user_id=$retweet_user_id");
			}	
			
			if ($entities->hashtags) {
				foreach($entities->hashtags as $hashtag) {
					$tag = $hashtag->text;
					$db->insert('tweet_tags',"tweet_id=$tweet_id,user_id=$user_id, created_at='$created_at',
						tag='$tag'");
				}
			}
			if ($entities->user_mentions) {
				foreach($entities->user_mentions as $user_mention) {
					$target_user_id = $user_mention->id;
					$db->insert('tweet_mentions',"tweet_id=$tweet_id,created_at='$created_at',
						source_user_id=$user_id, target_user_id=$target_user_id");
				}
			}
			if ($entities->urls) {
				foreach($entities->urls as $url) {
					$url = $url->expanded_url;
					$db->insert('tweet_urls',"tweet_id=$tweet_id,created_at='$created_at',
						user_id=$user_id, url='$url'");
				}
			}		
	
		} 
	}	
}
?>