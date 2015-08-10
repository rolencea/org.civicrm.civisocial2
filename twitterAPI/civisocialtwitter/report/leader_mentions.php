<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'tweet_mentions';
$report_name = 'leader_mentions.php';
require('select_date.php');

print '<h2>Top Leader Mentions</h2>';

require('../get_leader_mentions.php');
$users = get_leader_mentions($where,$page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>