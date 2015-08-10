<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'tweets';
$report_name = 'all_tweets.php';
require('select_date.php');

print '<link rel="stylesheet" type="text/css" media="all" href="dm.css">';
print '<h2>All Tweets</h2>';

require('../get_all_tweets.php');
$tweets = get_all_tweets($where,$page*$results_per_page, $results_per_page);
require('display_tweets.php');

require('page_bottom.html');
?>