<?php
// 


require('config.php');
require('db_lib.php');
$db = new db();

// Get day of week number (Monday = 1)
$current_dow = date('N',strtotime('now'));
	
// Get all the randomly timed tweets for today
$query = "SELECT id
	FROM autotweet_recurring
	WHERE random_time
	AND dow LIKE '%$current_dow%'";
$results = $db->select($query);
while ($row = mysqli_fetch_assoc($results)) {
	$id = $row['id'];
	
	// Pick a new hour and minute
	// $autotweet_start and $autotweet_stop are set in config.php
	$new_hour = rand($autotweet_start,$autotweet_stop);
	$new_minute = rand(0,59);
	
	// Save the new hour and minute
	$db->update('autotweet_recurring',"tweet_hour=$new_hour, tweet_minute=$new_minute", "id=$id");
}

?>