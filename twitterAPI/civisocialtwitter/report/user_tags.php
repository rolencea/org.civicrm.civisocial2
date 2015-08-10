<?php
// 


require('page_top.html');

require('../config.php');
require('../db_lib.php');
$db = new db();

$report_name = 'user_tags.php';
require('select_next_prev.php');

print '<h2>Top Tags in User Descriptions</h2>';

require('../get_user_tags.php');
$tags = get_user_tags($page*$results_per_page, $results_per_page);

if(sizeof($tags)){	
	$tag_html = '<table><th align="left">Count</th><th align="left">Tag</th>';
	foreach($tags as $tag) {
		$tag_html .= '<tr><td style="width:60px">' . number_format($tag['cnt']) . 
			'</td><td><a href="https://twitter.com/search?q=%23' . $tag['tag'] .
			'">' . $tag['tag'] . '</a></td></tr>';
	}
	$tag_html .= '</table>';
	print $tag_html;
} else {
	print "No tags found";
}

require('page_bottom.html');
?>