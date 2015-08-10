<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'follow_log';
$report_name = 'new_unfriends.php';
require('select_date.php');

print '<h2>New engagement unfriends</h2>';

require('../get_new_unfriends.php');
$users = get_new_unfriends($where,$page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>