<?php
// 

		
require('config.php');
require('oauth_lib.php');
$connection = get_connection();
require('db_lib.php');
$db = new db();

$query = "SELECT * 
	FROM engagement_account
	WHERE user_id = $engagement_user_id
	AND old_dms_received_collected = '0000-00-00'";
$results = $db->select($query);
$row = mysqli_fetch_assoc($results);
if (mysqli_num_rows($results)==0) {
	print 'Old engagement account DMs received have already been collected';
	exit;
}

$max_id = 0;
while (true) {
	if ($max_id == 0) {
 		$connection->request('GET', $connection->url('1.1/direct_messages'), 
	      array('include_entities' => 'false',
	        'count' => 100));
	} else {
		--$max_id;
		
		$connection->request('GET', $connection->url('1.1/direct_messages'), 
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
			sender_user_id=$sender_user_id,recipient_user_id=$recipient_user_id,received=1");
		
		$screen_name = $db->escape($dm->sender->screen_name);
		$name = $db->escape($dm->sender->name);	
		$location = $db->escape($dm->sender->location);
		$description = $db->escape($dm->sender->description);
		$url = $db->escape($dm->sender->url);
		$profile_image_url = $db->escape($dm->sender->profile_image_url);
		$created_at = $dm->sender->created_at;
		$friends_count = $dm->sender->friends_count;
		$followers_count = $dm->sender->followers_count;
		$statuses_count = $dm->sender->statuses_count;
		$listed_count = $dm->sender->listed_count;
		$lang = $dm->sender->lang;
		if (empty($dm->sender->protected)) {
			$protected = 0;
		} else {
			$protected = 1;
		}
		
		$field_values = "user_id=$sender_user_id, name='$name', screen_name='$screen_name', profile_image_url='$profile_image_url',
			location='$location', description='$description', url='$url', created_at='$created_at', friends_count=$friends_count,
			followers_count=$followers_count, statuses_count=$statuses_count, listed_count=$listed_count, lang='$lang',
			protected=$protected";
		
		if (!$db->in_table('users',"user_id=$sender_user_id")) {
			$db->insert('users',$field_values);
		} else {
			$db->update('users',$field_values,"user_id=$sender_user_id");
		}
	} 
}

$db->update('engagement_account','old_dms_received_collected=now()',"user_id=$engagement_user_id");
?>