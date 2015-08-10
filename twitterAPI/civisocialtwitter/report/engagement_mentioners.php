<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'tweet_mentions';
$report_name = 'engagement mentioners.php';
require('select_date.php');

print '<h2>Most frequent engagement mentioners</h2>';

require('../get_engagement_mentioners.php');
$users = get_engagement_mentioners($where,$page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>