<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'tweet_retweets';
$report_name = 'all_retweets.php';
require('select_date.php');

print '<h2>Top Retweets by all Accounts</h2>';

require('../get_all_retweets.php');
$users = get_all_retweets($where,$page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>