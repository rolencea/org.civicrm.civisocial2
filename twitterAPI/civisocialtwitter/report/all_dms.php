<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'dms';
$report_name = 'all_dms.php';
require('select_date.php');

print '<h2>All DMs</h2>';

require('../get_all_dms.php');
$dms = get_all_dms($where,$page*$results_per_page, $results_per_page);
require('display_dms.php');

require('page_bottom.html');
?>