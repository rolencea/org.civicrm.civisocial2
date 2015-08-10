<?php
// 


require('config.php');
require('oauth_lib.php');
$connection = get_connection();
require('db_lib.php');
$db = new db();

// After the first collection is done,
// set the current field in followers to zero
$query = "SELECT count(*) AS cnt
	FROM followers";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
if ($row['cnt']==0) {
	$first_collection = 1;
} else {
	$first_collection = 0;
	$query = "UPDATE followers
		SET current = 0";
	$db->select($query);
}

// Loop through all followers
$cursor = '-1';
while ($cursor != 0) {

	$connection->request('GET', $connection->url('1.1/followers/ids'), 
	  	array('user_id' => $engagement_user_id,
	    'cursor' => $cursor
	 ));
	
	$http_code = $connection->response['code'];
	if ($http_code == 200) {
		$data = json_decode($connection->response['response'], true);
	  $ids = $data['ids'];
	  $cursor = $data['next_cursor_str'];
		
		if (sizeof($ids)) {
			
			// Record the follower ids and add new followers to the log
		  foreach($ids as $user_id) {
				if ($db->in_table('followers',"user_id=$user_id")){
					$db->update('followers','current=1',"user_id=$user_id");
				} else {
					$db->insert('followers',"user_id=$user_id,current=1");
					if (!$first_collection) {
						$db->insert('follow_log',"user_id=$user_id,event='follow'");
					}
				}
			}
		} else {
			break;
		}
	} else {
		echo "not worked";
		exit;
	}
}

// Remove any followers not returned on this collection
// Record these as unfollows in the log
$query = "SELECT user_id
	FROM followers
	WHERE current = 0";
$results = $db->select($query);
while ($row = mysqli_fetch_assoc($results)) {
	$user_id = $row['user_id'];
	$db->insert('follow_log',"user_id=$user_id,event='unfollow'");
	$query = "DELETE FROM followers
		WHERE user_id = $user_id";
	$db->select($query);
}
?>