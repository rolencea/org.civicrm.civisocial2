<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$report_name = 'followers_not_friends.php';
require('select_next_prev.php');

print '<h2>Followers who are not friends</h2>';

require('../get_followers_not_friends.php');
$users = get_followers_not_friends($page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>