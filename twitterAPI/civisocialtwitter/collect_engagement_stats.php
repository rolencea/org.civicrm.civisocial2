<?php
require('config.php');
$engagement_user_id = 15131310;

require('oauth_lib.php');
$connection = get_connection();

require('db_lib.php');
$db = new db();

$connection->request('GET', 
	$connection->url('1.1/users/show'), 
	array('user_id' => $engagement_user_id));

$user = json_decode($connection->response['response']);
$friends_total = $user->friends_count;
$followers_total = $user->followers_count;
$statuses_total = $user->statuses_count;
$listed_total = $user->listed_count;

$previous_date = date('Y-m-d',strtotime('-2 day'));
$stat_date = date('Y-m-d',strtotime('-1 day'));

if ($db->in_table('daily_stats',"stat_date='$stat_date'")){
	print "Stats already collected for $stat_date";
	exit;
}

$query = "SELECT friends_total, followers_total, statuses_total, listed_total
	FROM daily_stats
	WHERE stat_date = '$previous_date'";
$results = $db->select($query);
if($previous_row = mysqli_fetch_assoc($results)) {
	$friends_added = $friends_total - $previous_row['friends_total'];
	$followers_added = $followers_total - $previous_row['followers_total'];	
	$statuses_added = $statuses_total - $previous_row['statuses_total'];	
	$listed_added = $listed_total - $previous_row['listed_total'];
} else {
	$friends_added = 0;
	$followers_added = 0;	
	$statuses_added = 0;	
	$listed_added = 0;	
}

$query = "SELECT count(*) AS cnt
	FROM tweet_mentions
	WHERE date(created_at) = '$stat_date'
	AND source_user_id = $engagement_user_id";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
$mentions_made = $row['cnt'];

$query = "SELECT count(*) AS cnt
	FROM tweet_mentions
	WHERE date(created_at) = '$stat_date'
	AND target_user_id = $engagement_user_id";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
$mentions_received = $row['cnt'];

$query = "SELECT count(distinct source_user_id) AS cnt
	FROM tweet_mentions
	WHERE date(created_at) = '$stat_date'
	AND target_user_id = $engagement_user_id";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
$unique_mentioners = $row['cnt'];

$query = "SELECT count(*) AS cnt
	FROM tweet_retweets
	WHERE date(created_at) = '$stat_date'
	AND source_user_id = $engagement_user_id";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
$retweets_made = $row['cnt'];

$query = "SELECT count(*) AS cnt
	FROM tweet_retweets
	WHERE date(created_at) = '$stat_date'
	AND target_user_id = $engagement_user_id";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
$retweets_received = $row['cnt'];

$query = "SELECT count(distinct source_user_id) AS cnt
	FROM tweet_retweets
	WHERE date(created_at) = '$stat_date'
	AND target_user_id = $engagement_user_id";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
$unique_retweeters = $row['cnt'];

$field_values = "user_id=$engagement_user_id, stat_date='$stat_date', 
	statuses_total=$statuses_total, statuses_added=$statuses_added,
	friends_total=$friends_total, friends_added=$friends_added,
	followers_total = $followers_total, followers_added = $followers_added,
	listed_total = $listed_total, listed_added = $listed_added,
	mentions_made=$mentions_made, mentions_received = $mentions_received, unique_mentioners = $unique_mentioners,
	retweets_made=$retweets_made, retweets_received = $retweets_received, unique_retweeters = $unique_retweeters";

$db->insert('daily_stats',$field_values);

?>