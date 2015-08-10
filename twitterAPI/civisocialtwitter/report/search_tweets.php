<?php
// 


require('../config.php');
require('../db_lib.php');
$db = new db();

// If the user clicked one of the buttons
if ((isset($_GET['submit'])) || (isset($_GET['prev'])) || (isset($_GET['next']))) {
	$where = '';
	$start_date = htmlspecialchars($_GET['start_date'], ENT_QUOTES);
	if ($start_date != '0000-00-00') {
		$where .= ' AND tweets.created_at >= "' . $db->date($start_date) . '"';
	}
	$end_date = htmlspecialchars($_GET['end_date'], ENT_QUOTES);
	if ($end_date != '0000-00-00') {
		$where .= ' AND tweets.created_at <= "' . $db->date($end_date) . '"';
	}
	$query = htmlspecialchars($_GET['query'], ENT_QUOTES);
	if ($query != '') {
		$query_words = explode(' ',$query);
		foreach($query_words as $word) {
			$word = trim($word);
			$where .= " AND tweets.tweet_text LIKE '%$word%' ";
		}
	}
	if(isset($_GET['prev'])) {
		$page = intval($_GET['page']) - 1;
		if($page<0) {
			$page=0;
		}
	} elseif ($_GET['next']) {
		$page = intval($_GET['page']) + 1;
	} else {
		$page = 0;
	}
} else {
	$start_date = '0000-00-00';
	$end_date = '0000-00-00';
	$query = '';
	$page = 0;
}
	
require('page_top.html');
print '<h2>Search Tweets</h2>';
print "<form action='search_tweets.php' method='get'>";
print "Start Date: <input type='text' name='start_date' value='$start_date'>";
print "End Date: <input type='text' name='end_date' value='$end_date'><br/>";
print "Search Terms: <input type='text' name='query' value='$query' size='50'>";
print "<input type='hidden' name='page' value=$page>";
print '<button type="submit" name="submit" value=1>Search</button>';
print '<button type="submit" name="prev" value=1>< Prev</button>';
print '<button type="submit" name="next" value=1>Next ></button>';
print '</form>';

if (!empty($where)) {
	require('../get_all_tweets.php');
	$tweets = get_all_tweets($where,$page*$results_per_page, $results_per_page);	
	require('display_tweets.php');
}

require('page_bottom.html');
?>