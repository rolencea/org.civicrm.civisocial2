<?php
// 


require('../config.php');
require('../db_lib.php');
$db = new db();

// If the user clicked the one of the buttons
if ((isset($_GET['submit'])) || (isset($_GET['prev'])) || (isset($_GET['next']))) {
	$where = '';
	$query = htmlspecialchars($_GET['query'], ENT_QUOTES);
	if ($query != '') {
		// Extract the query words and convert them 
		// into a SQL WHERE clause with ANDs
		$query_words = explode(' ',$query);
		foreach($query_words as $word) {
			$word = trim($word);
			if (empty($where)) {
				$where = ' WHERE ';
			} else {
				$where .= ' AND ';
			}
			$where .= " users.description LIKE '%$word%' ";
		}
		if(isset($_GET['prev'])) {
			$page = intval($_GET['page']) - 1;
			if($page<0) {
				$page=0;
			}
		} elseif (isset($_GET['next'])) {
			$page = intval($_GET['page']) + 1;
		} else {
			$page = 0;
		}
	}
} else {
	$query = '';
	$page = 0;
}
	
require('page_top.html');
print '<h2>Search Users</h2>';

// Display the form with empty fields
// or the values entered when a button was clicked	
print "<form action='search_users.php' method='get'>";
print "Search Terms: <input type='text' name='query' value='$query' size='50'>";
print "<input type='hidden' name='page' value=$page>";
print '<button type="submit" name="submit" value=1>Search</button>';
print '<button type="submit" name="prev" value=1>< Prev</button>';
print '<button type="submit" name="next" value=1>Next ></button>';
print '</form>';

if (!empty($where)) {
	require('../get_search_users.php');
	$users = get_search_users($where,$page*$results_per_page, $results_per_page);
	require('display_users.php');
}

require('page_bottom.html');
?>