<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'tweet_retweets';
$report_name = 'engagement_retweeters.php';
require('select_date.php');

print '<h2>Most frequent engagement retweeters</h2>';

require('../get_engagement_retweeters.php');
$users = get_engagement_retweeters($where,$page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>