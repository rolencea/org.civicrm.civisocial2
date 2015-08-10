<?php
// 

	
require('config.php');
require('oauth_lib.php');
$connection = get_connection();
require('db_lib.php');
$db = new db();

// Get one leader account that has not been searched yet
// Only process a complete old tweet search for one leader at a time
// Then wait at least 15 minutes before running this script again
// That allows the rate limit to reset between collections
$query = "SELECT user_id, screen_name
	FROM leaders
	WHERE old_search_collected = '0000-00-00'
	LIMIT 1";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);

// Exit if all leaders have run the old search
if (mysqli_num_rows($results)==0) {
	print 'All old leader search results have already been collected';
	exit;
}
$leader_user_id = $row['user_id'];
$leader_screen_name = $row['screen_name'];
$max_id = 0;

// We need to save the highest tweet_id collected to run new tweet searches later
$search_since_id = 0;

while (true) {

	if ($max_id == 0) {
 		$connection->request('GET', $connection->url('1.1/search/tweets'), 
	      array('q' => $leader_screen_name,
	        'result_type' => 'recent',
	        'lang' => 'en',
	        'include_entities' => 'true',
	        'count' => 100));
	} else {
		--$max_id;
		
 		$connection->request('GET', $connection->url('1.1/search/tweets'), 
	      array('q' => $leader_screen_name,
	        'result_type' => 'recent',
	        'lang' => 'en',
	        'include_entities' => 'true',
	        'count' => 100,
			'max_id' => $max_id));
	}			

  if ($connection->response['code'] != 200) {
    	break;			
	} 

	// Track the number of tweets returned by the API
	$tweets_found = 0;
	
	$results = json_decode($connection->response['response']);
	$tweets = $results->statuses;
  foreach($tweets as $tweet) {
		++$tweets_found;

		$tweet_id = $tweet->id;
		$max_id = $tweet_id;
		
		// The first tweet we receive has the highest tweet_id
		// If we haven't received any tweets yet
		// record this tweet's id for use later in since_id
		if ($search_since_id==0) {
			$search_since_id = $tweet_id;	
		}
		
		if ($db->in_table('tweets',"tweet_id=$tweet_id")) {
			continue;
		}
		
    $tweet_text = $db->escape($tweet->text);
		$tweet_created_at = $db->date($tweet->created_at);
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
		
		$db->insert('tweets',"tweet_id=$tweet_id,tweet_text='$tweet_text',created_at='$tweet_created_at',
			user_id=$user_id,is_rt=$is_rt,retweet_count=$retweet_count");
		
		if ($is_rt) {
			$db->insert('tweet_retweets',"tweet_id=$tweet_id,created_at='$tweet_created_at',
				source_user_id=$user_id, target_user_id=$retweet_user_id");
		}	
		
		// Collect all user account values we want to record
		// Strings should be escaped to hide quotes and other characters from MySQL
		$name = $db->escape($tweet->user->name);
		$screen_name = $db->escape($tweet->user->screen_name);
		$profile_image_url = $db->escape($tweet->user->profile_image_url);
		$location = $db->escape($tweet->user->location);	
		$description = $db->escape($tweet->user->description);
		$url = $db->escape($tweet->user->url);
		$user_created_at = $db->date($tweet->user->created_at);
		$friends_count = $tweet->user->friends_count;
		$followers_count = $tweet->user->followers_count;
		$statuses_count = $tweet->user->statuses_count;
		$listed_count = $tweet->user->listed_count;
		$lang = $db->escape($tweet->user->lang);
		
		// We know this user can't be protected,
		// because their tweet was returned by the search API
		$protected = 0;
		
		$field_values = "user_id=$user_id, name='$name', screen_name='$screen_name', profile_image_url='$profile_image_url',
			location='$location', description='$description', url='$url', created_at='$user_created_at', friends_count=$friends_count,
			followers_count=$followers_count, statuses_count=$statuses_count, listed_count=$listed_count, lang='$lang',
			protected=$protected, last_tweet_date='$tweet_created_at'";
		
		if (!$db->in_table("users","user_id=$user_id")) {
			// If this is a new user, we add it
			$db->insert('users',$field_values);
		} else {
			// Otherwise we update it
			// This makes sure the user's row is refreshed as often as possible
			$db->update('users',$field_values,"user_id=$user_id");
		}
				
		if ($entities->hashtags) {
			foreach($entities->hashtags as $hashtag) {
				$tag = $hashtag->text;
				$db->insert('tweet_tags',"tweet_id=$tweet_id,user_id=$user_id, created_at='$tweet->created_at',
					tag='$tag'");
			}
		}
		if ($entities->user_mentions) {
			foreach($entities->user_mentions as $user_mention) {
				$target_user_id = $user_mention->id;
				$db->insert('tweet_mentions',"tweet_id=$tweet_id,created_at='$tweet_created_at',
					source_user_id=$user_id, target_user_id=$target_user_id");
			}
		}
		if ($entities->urls) {
			foreach($entities->urls as $url) {
				$url = $url->expanded_url;
				$db->insert('tweet_urls',"tweet_id=$tweet_id,created_at='$tweet->created_at',
					user_id=$user_id, url='$url'");
			}
		}		
  } 
  if ($tweets_found==0) {
       	break;
  } 
}

$db->update('leaders',"old_search_collected=now(),search_since_id=$search_since_id",
	"user_id=$leader_user_id");

?>