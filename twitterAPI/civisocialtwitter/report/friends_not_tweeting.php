<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$report_name = 'friends_not_tweeting.php';
require('select_next_prev.php');

print '<h2>Friends not tweeting</h2>';

require('../get_friends_not_tweeting.php');
$users = get_friends_not_tweeting($page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>