<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

// Display the date selection form with next/prev buttons
$table_name = 'tweets';
$report_name = 'leader_tweets.php';
require('select_date.php');

print '<h2>Leader Tweets</h2>';

require('../get_leader_tweets.php');
// The $where and $page variables are created by select_date.php
// $results_per_page is set in config.php
$tweets = get_leader_tweets($where,$page*$results_per_page, $results_per_page);
require('display_tweets.php');

require('page_bottom.html');
?>