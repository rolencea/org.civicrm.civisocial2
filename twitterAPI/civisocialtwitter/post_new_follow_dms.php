<?php
// 


require('config.php');
require('db_lib.php');
$db = new db();
require('oauth_lib.php');
$connection = get_connection();

$query = "SELECT follow_log.id, follow_log.user_id, users.name
	FROM follow_log, users
	WHERE follow_log.user_id = users.user_id
	AND event = 'follow'
	AND NOT dm_sent
	ORDER BY follow_log.created_at DESC
	LIMIT 1";
$results = $db->select($query);
while ($row = mysqli_fetch_assoc($results)) {
	$log_id = $row['id'];
	$user_id = $row['user_id'];
	$dm_text = "Thanks for the follow " . $row['name'] . ". Please check out our site at http://foodiecentral.com";
	
	$connection->request('POST', $connection->url('1.1/direct_messages/new'), 
    	array('user_id' => $user_id, 
    	'text' => $dm_text));
	$http_code = $connection->response['code'];
	$db->insert('autodm_log',"dm_type='follow_dm', dm_text='$dm_text', http_code=$http_code");
	$db->update('follow_log','dm_sent=1',"id=$log_id");
}

?>