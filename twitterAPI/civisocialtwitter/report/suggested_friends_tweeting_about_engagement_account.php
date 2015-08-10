<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$report_name = 'suggested_friends_tweeting_about_engagement_account.php';
require('select_next_prev.php');

print '<h2>Suggested friends who mention or retweet engagement account</h2>';

require('../get_suggested_friends_tweeting_about_engagement_account.php');
$users = get_suggested_friends_tweeting_about_engagement_account($page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>