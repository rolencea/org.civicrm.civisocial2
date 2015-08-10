<?php
// 


require('../config.php');
require('../db_lib.php');
$db = new db();

require('page_top.html');
print '<link rel="stylesheet" type="text/css" media="all" href="user_profile.css" />';

// Confirm that a user_id argument was passed in URL
if (!isset($_GET['user_id'])) {
	print 'User id is required';
	exit;
} 

// Convert to integer before using in SQL query
$user_id = intval($_GET['user_id']);

require('../get_user_profile.php');
$user = get_user_profile($user_id);
if (sizeof(user)==0) {
	print "User not found";
	exit;
}

// Only one user is returned, so extract its row
$user_row = $user[0];

$screen_name = $user_row['screen_name'];
print '<h2>Profile of: @' .$screen_name . '</h2>';

// Display user's account profile
print '<div class="column">';

print '<div><div id="user_image"><img src="' . $user_row['profile_image_url'] . 
	'"></div>';

if ($user_row['protected']) {
	print '<img src="images/protected.png" alt="Account is protected" title="Account is protected" style="margin-bottom: -1px;"/> ';
}
if ($user_row['suspended']) {
	print '<img src="images/suspended.png" alt="Accunt is suspended" title="User is suspended" style="margin-bottom: -1px;" /> ';
} 
	
print '<b>' . $user_row['name'] . '</b> ';	
print '<a href="https://twitter.com/' .$screen_name . '" class="twitter-follow-button" data-show-count="false" data-show-screen-name="false">Follow @' .$screen_name . '</a> <br/>';
print '<div id="user_description">' . $user_row['description'] . '</div></div>';
if (!empty($user_row['url'])) {
	print '<a href="' . $user_row['url'] . '">' . $user_row['url'] . '</a><br />';
}
if (!empty($user_row['location'])) {
	print 'Location: ' . $user_row['location'] . '<br />';
}
print 'Following: ' . number_format($user_row['friends_count']) . '<br />';
print 'Followers: ' . number_format($user_row['followers_count']) . '<br />';
print 'Total tweets: ' . number_format($user_row['statuses_count']) . '<br />';
print 'Started on Twitter: ' . date('F, d Y', strtotime($user_row['created_at'])) . '<br />';
print '<a href="http://twitter.com/' . $screen_name . '">View Twitter page</a>';

// Display activity stats
print '<h3>Tweet Stats: </h3>';

$stat_query = "SELECT count(*) AS total_tweets, DATEDIFF( MAX( created_at ) , MIN( created_at ) ) AS tweet_days
	FROM tweets
	WHERE user_id = $user_id";

$stat_results = $db->select($stat_query);
$stat_row = mysqli_fetch_assoc($stat_results);	
$total_tweets = $stat_row['total_tweets'];

$tweet_days = $stat_row['tweet_days'] + 1;
$tweets_per_day = number_format($total_tweets/$tweet_days,1);

$stat_query = "SELECT count(*) AS tweet_cnt
	FROM tweet_mentions
	WHERE target_user_id =$user_id";
$stat_results = $db->select($stat_query);
$stat_row = mysqli_fetch_assoc($stat_results);	
$mentioned_cnt = $stat_row['tweet_cnt'];
$mentioned_per_day = number_format($mentioned_cnt/$tweet_days,1);

$stat_query = "SELECT count(*) AS tweet_cnt
	FROM tweet_retweets
	WHERE target_user_id =$user_id";
$stat_results = $db->select($stat_query);
$stat_row = mysqli_fetch_assoc($stat_results);	
$retweeted_cnt = $stat_row['tweet_cnt'];
$retweeted_per_day = number_format($retweeted_cnt/$tweet_days,1);
$retweeted_per_tweet = number_format($retweeted_cnt/$total_tweets,1);

// Some users don't have any tweets in the database
// They were found in mentions or retweets by others
if ($total_tweets <> 0) {
	print "Tweets per day: $tweets_per_day<br/>";
	print "Retweeted per day: $retweeted_per_day<br/>";
	print "Retweeted per tweet: $retweeted_per_tweet<br/>";	
}
print "Mentioned per day: $mentioned_per_day<br/>";

// Display most frequently used tags by this user
$tag_query = "SELECT COUNT( * ) AS cnt, tag
	FROM tweet_tags
	WHERE user_id = $user_id
	GROUP BY tag
	ORDER BY cnt DESC , tag ASC 
	LIMIT 5";

$tag_results = $db->select($tag_query);
if (mysqli_num_rows($tag_results)) { 
	print '<h3>Favorite tags: </h3>';	
	print '<ol>';
	while($tag_row = mysqli_fetch_assoc($tag_results)) {
		print '<li>' . $tag_row['tag'] . ' - ' . 
			number_format($tag_row['cnt']) . ' tweets</li>';
	}
	print '</ol>';
}

print '</div>';
print '<div class="columnright">';
// Display screen names of users mentioned most by this user
$mentions_query = "SELECT COUNT(*) AS cnt, users.screen_name, users.user_id
	FROM tweet_mentions, users
	WHERE tweet_mentions.target_user_id = users.user_id
	AND tweet_mentions.source_user_id = $user_id
	GROUP BY tweet_mentions.target_user_id
	ORDER BY cnt DESC, users.screen_name ASC 
	LIMIT 6";
$mentions_results = $db->select($mentions_query);
if (mysqli_num_rows($mentions_results)) { 
	print "<strong>Mentioned by @$screen_name:</strong><br/>";	
	while($mentions_row = mysqli_fetch_assoc($mentions_results)) {
		print '(' . $mentions_row['cnt'] . ') ' . '<a href="user_profile.php?user_id=' . $mentions_row['user_id'] .
			'">@' . $mentions_row['screen_name'] . '</a><br/>';
	}
}

// Display screen names of users who mention this user the most
$mentions_query = "SELECT COUNT(*) AS cnt, users.screen_name, users.user_id
	FROM tweet_mentions, users
	WHERE tweet_mentions.source_user_id = users.user_id
	AND tweet_mentions.target_user_id = $user_id
	GROUP BY tweet_mentions.source_user_id
	ORDER BY cnt DESC, users.screen_name ASC 
	LIMIT 6";
$mentions_results = $db->select($mentions_query);
if (mysqli_num_rows($mentions_results)) { 
	print "<br/><strong>Mentioners of @$screen_name:</strong><br/>";	
	while($mentions_row = mysqli_fetch_assoc($mentions_results)) {
		print '(' . $mentions_row['cnt'] . ') ' . '<a href="user_profile.php?user_id=' . $mentions_row['user_id'] .
			'">@' . $mentions_row['screen_name'] . '</a><br/>';
	}
}
print '</div>';

// Display recent tweets by this user
if ($total_tweets <> 0) {
	print '<div class="clear"><h3>Recent Tweets</h3>';
	require('../get_all_tweets.php');
	$where = " AND tweets.user_id = $user_id ";
	$tweets = get_all_tweets($where,0,100);
	require('display_tweets.php');
}

require('page_bottom.html');
?>