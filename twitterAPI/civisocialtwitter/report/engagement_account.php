<?php
// 


require('page_top.html');
print '<h2>Engagement Account</h2>';

require('../config.php');
require('../db_lib.php');
$db = new db();

// Display a single user based on the $engagement_user_id
// $engagement_user_id is stored in config.php
require('../get_user_profile.php');
$users = get_user_profile($engagement_user_id);
require('display_users.php');

// Display a single tweet based on the $engagement_user_id
require('../get_last_tweet.php');
$tweets = get_last_tweet($engagement_user_id);
require('display_tweets.php');

require('page_bottom.html');
?>