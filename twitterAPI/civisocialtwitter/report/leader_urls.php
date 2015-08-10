<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$table_name = 'tweet_urls';
$report_name = 'leader_urls.php';
require('select_date.php');

print '<h2>Top Leader URLs</h2>';

require('../get_leader_urls.php');
$urls = get_leader_urls($where,$page*$results_per_page, $results_per_page);

if(sizeof($urls)){	
	$url_html = '<table><th align="left">Count</th><th align="left">URL</th>';
	foreach($urls as $url) {
		$url_html .= '<tr><td style="width:60px">' . number_format($url['cnt']) . 
			'</td><td><a href="' . $url['url'] .
			'">' . $url['url'] . '</a></td></tr>';
	}
	$url_html .= '</table>';
	print $url_html;
} else {
	print "No URLs found";	
}

require('page_bottom.html');
?>