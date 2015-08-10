<?php
// 


require('config.php');
require('oauth_lib.php');
$connection = get_connection();
require('db_lib.php');
$db = new db();

// $engagement_user_id is set in config.php
$query = "SELECT * 
	FROM engagement_account
	WHERE user_id = $engagement_user_id
	AND old_dms_sent_collected = '0000-00-00'";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
if (mysqli_num_rows($results)==0) {
	print 'Old engagement account DMs sent have already been collected';
	exit;
}

$max_id = 0;
while (true) {
	
	// The account sending the DMs is the one authorized by the OAuth tokens
	if ($max_id == 0) {
 		$connection->request('GET', $connection->url('1.1/direct_messages/sent'), 
	      array('include_entities' => 'false',
	        'count' => 100));
	} else {
		--$max_id;
		
		$connection->request('GET', $connection->url('1.1/direct_messages/sent'), 
	      array('include_entities' => 'false',
	        'count' => 100,
	        'max_id' => $max_id));
	}			

	if ($connection->response['response'] == '[]') {
  	break;
  } 
  if ($connection->response['code'] != 200) {
  	break;			
	} 
	
	$results = json_decode($connection->response['response']);
  foreach($results as $dm) {

		$dm_id = $dm->id;
		$max_id = $dm_id;
		
		if ($db->in_table('dms',"dm_id=$dm_id")) {
			continue;
		}
		
    $dm_text = $db->escape($dm->text);
		$created_at = $db->date($dm->created_at);
		$sender_user_id = $dm->sender->id;
		$recipient_user_id = $dm->recipient->id;
	
		$db->insert('dms',"dm_id=$dm_id,dm_text='$dm_text',created_at='$created_at',
			sender_user_id=$sender_user_id,recipient_user_id=$recipient_user_id,sent=1");
		
		$screen_name = $db->escape($dm->recipient->screen_name);
		$name = $db->escape($dm->recipient->name);	
		$location = $db->escape($dm->recipient->location);
		$description = $db->escape($dm->recipient->description);
		$url = $db->escape($dm->recipient->url);
		$profile_image_url = $db->escape($dm->recipient->profile_image_url);
		$created_at = $dm->recipient->created_at;
		$friends_count = $dm->recipient->friends_count;
		$followers_count = $dm->recipient->followers_count;
		$statuses_count = $dm->recipient->statuses_count;
		$listed_count = $dm->recipient->listed_count;
		$lang = $dm->recipient->lang;
		if (empty($dm->recipient->protected)) {
			$protected = 0;
		} else {
			$protected = 1;
		}
		
		$field_values = "user_id=$sender_user_id, name='$name', screen_name='$screen_name', profile_image_url='$profile_image_url',
			location='$location', description='$description', url='$url', created_at='$created_at', friends_count=$friends_count,
			followers_count=$followers_count, statuses_count=$statuses_count, listed_count=$listed_count, lang='$lang',
			protected=$protected";
		
		if (!$db->in_table('users',"user_id=$recipient_user_id")) {
			$db->insert('users',$field_values);
		} else {
			$db->update('users',$field_values,"user_id=$recipient_user_id");
		}
	} 
}

$db->update('engagement_account','old_dms_sent_collected=now()',"user_id=$engagement_user_id");
?>