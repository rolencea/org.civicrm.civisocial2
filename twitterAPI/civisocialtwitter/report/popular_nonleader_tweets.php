<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'tweets';
$report_name = 'popular_nonleader_tweets.php';
require('select_date.php');

print '<h2>Popular Non-Leader Tweets</h2>';

require('../get_popular_nonleader_tweets.php');
$tweets = get_popular_nonleader_tweets($where,$page*$results_per_page, $results_per_page);
require('display_tweets.php');

require('page_bottom.html');
?>