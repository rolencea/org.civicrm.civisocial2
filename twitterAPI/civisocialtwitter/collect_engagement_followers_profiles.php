<?php
// 


require('config.php');
require('oauth_lib.php');
$connection = get_connection();
require('db_lib.php');
$db = new db();

$query = "SELECT user_id
	FROM followers
	WHERE user_id NOT IN
		(SELECT user_id
		FROM users)
	LIMIT 15000";
$results = $db->select($query);
$row=mysqli_fetch_assoc($results);



if (mysqli_num_rows($results)==0) {
	print 'All followers user accounts have already been collected';
	exit;
}

require('collect_user_profiles.php');

while(true) {
	$id_count = 0;
	$user_list = '';
	while(($row=mysqli_fetch_assoc($results)) && ($id_count<100)) {
		$user_list .= $row['user_id'] . ',';
		++$id_count;
	}
	if ($id_count==0) {
		break;
	}
	$user_list = substr($user_list,0,strlen($user_list)-1);
	$response_code = collect_user_profiles($user_list);	
	if ($response_code != 200) {
		break;

		echo "entered successfully ";
	}
}
?>