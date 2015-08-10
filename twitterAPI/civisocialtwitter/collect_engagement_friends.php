<?php
// 


require('config.php');
require('oauth_lib.php');
$connection = get_connection();
require('db_lib.php');
$db = new db();

// Find out if any friends have already been collected
$query = "SELECT count(*) AS cnt
	FROM friends";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
if ($row['cnt']==0) {
	$first_collection = 1;
} else {
	
	// if friends have been collected before
	// set the current field to zero
	$first_collection = 0;
	$query = "UPDATE friends
		SET current = 0";
	$db->select($query);
}

// Start with a cursor value of -1
// Stop when the API returns a next cursor of zero
$cursor = '-1';
while ($cursor != 0) {

	$connection->request('GET', $connection->url('1.1/friends/ids'), 
	  	array('user_id' => $engagement_user_id,
	    'cursor' => $cursor
	 ));
	
	$http_code = $connection->response['code'];
	if ($http_code == 200) {
	    $data = json_decode($connection->response['response'], true);
		
		// Get the list of friend user_ids, which will be an array
	    $ids = $data['ids'];
		
		// Get the cursor value for the next request
	    $cursor = $data['next_cursor_str'];
		
		// If there are any friends returned
		if (sizeof($ids)) {
		    foreach($ids as $user_id) {
		    	
				// If this friend is already in the table,
				// set the current field back to 1
				if ($db->in_table('friends',"user_id=$user_id")){
					$db->update('friends','current=1',"user_id=$user_id");
				} else {
						
					// If this is a new friend,
					// insert it with a current value of 1
					$db->insert('friends',"user_id=$user_id,current=1");
					
					// If this is not the first time friends have been collected,
					// record this new friend event in the follow_log table
					if (!$first_collection) {
						$db->insert('follow_log',"user_id=$user_id,event='friend'");
					}
				}
			}
		} else {
			// Stop collecting if no more friends are found
			break;
		}
	} else {
		
		// If an error occurs, exit and try again later
		exit;
	}
}

// Find any friends who were collected in the past, but not this time
$query = "SELECT user_id
	FROM friends
	WHERE current = 0";
$results = $db->select($query);
while ($row = mysqli_fetch_assoc($results)) {
	$user_id = $row['user_id'];
	
	// Record the 'unfriend' event
	// This means the engagement account unfollowed this friend
	$db->insert('follow_log',"user_id=$user_id,event='unfriend'");
	
	// Delete any friends who are not found during collection
	$query = "DELETE FROM friends
		WHERE user_id = $user_id";
	$db->select($query);
}
?>