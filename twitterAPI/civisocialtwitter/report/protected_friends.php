<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$report_name = 'protected_friends.php';
require('select_next_prev.php');

print '<h2>Protected friends</h2>';

require('../get_protected_friends.php');
$users = get_protected_friends($page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>