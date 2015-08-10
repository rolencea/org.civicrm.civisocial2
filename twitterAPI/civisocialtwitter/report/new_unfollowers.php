<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'follow_log';
$report_name = 'new_unfollowers.php';
require('select_date.php');

print '<h2>New engagement un-followers</h2>';

require('../get_new_unfollowers.php');
$users = get_new_unfollowers($where,$page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>