<?php
// 


if ((isset($_GET['prev'])) || (isset($_GET['next']))) {

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
	$page = 0;
}

print "<br/><form action='$report_name' method='get'>";
print "<input type='hidden' name='page' value=$page>";
print '<button type="submit" name="prev" value=1>< Prev</button>';
print '<button type="submit" name="next" value=1>Next ></button>';
print '</form><hr>';

?>