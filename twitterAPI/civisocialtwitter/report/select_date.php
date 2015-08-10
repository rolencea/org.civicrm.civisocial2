<?php
// 


$where = '';
	
// If the user clicked one of the buttons
// Create the values needed in the SQL where clause
if ((isset($_GET['submit'])) || (isset($_GET['prev'])) || (isset($_GET['next']))) {
	
	$start_date = htmlspecialchars($_GET['start_date'], ENT_QUOTES);
	if ($start_date != '0000-00-00') {
		$where .= " AND $table_name.created_at >= '" . $db->date($start_date) . "'";
	}
	$end_date = htmlspecialchars($_GET['end_date'], ENT_QUOTES);
	if ($end_date != '0000-00-00') {
		$where .= " AND $table_name.created_at <= '" . $db->date($end_date) . "'";
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
} else {
	$start_date = '0000-00-00';
	$end_date = '0000-00-00';
	$page = 0;
}

// Display the form
print "<br/><form action='$report_name' method='get'>";
print "Start Date: <input type='text' name='start_date' value='$start_date'>";
print "End Date: <input type='text' name='end_date' value='$end_date'>";
print "<input type='hidden' name='page' value=$page>";
print '<button type="submit" name="submit" value=1>Search</button>';
print '<button type="submit" name="prev" value=1>< Prev</button>';
print '<button type="submit" name="next" value=1>Next ></button>';
print '</form><hr>';

?>