<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'tweets';
$report_name = 'engagement_tweets.php';
require('select_date.php');

print '<h2>Engagement Tweets</h2>';

require('../get_engagement_tweets.php');
$tweets = get_engagement_tweets($where,$page*$results_per_page, $results_per_page);
require('display_tweets.php');

require('page_bottom.html');
?>