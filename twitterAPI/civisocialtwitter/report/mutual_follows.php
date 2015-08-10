<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$report_name = 'mutual_follows.php';
require('select_next_prev.php');

print '<h2>Mutual follows</h2>';

require('../get_mutual_follows.php');
$users = get_mutual_follows($page*$results_per_page, $results_per_page);
require('display_users.php');

require('page_bottom.html');
?>