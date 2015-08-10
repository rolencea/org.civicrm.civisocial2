<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$report_name = 'friends_not_followers.php';
require('select_next_prev.php');

print '<h2>Friends who are not followers</h2>';

require('../get_friends_not_followers.php');
$users = get_friends_not_followers($page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>