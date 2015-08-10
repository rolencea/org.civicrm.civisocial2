<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'tweet_retweets';
$report_name = 'retweeters.php';
require('select_date.php');

print '<h2>Most frequent retweeters</h2>';

require('../get_retweeters.php');
$users = get_retweeters($where,$page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>