<?php



require('config.php');

require('db_lib.php');
$db = new db();

// This is all done within the database
// So there are no rate limits
$query = "SELECT user_id, description
		FROM users";
$results = $db->select($query);
while($row=mysqli_fetch_assoc($results)) {
	$user_id = $row['user_id'];
	$description = $row['description'];
	
	// Delete the old description tags for this user
	$delete_query = "DELETE 
		FROM user_tags
		WHERE user_id = $user_id";
	$db->select($delete_query);
	
	// Parse out any tags in the latest description
	preg_match_all("/\B(?<![=\/])#([\w]+[a-z]+([0-9]+)?)/i",$description,$tags);
	
	// Add the current tags back into the database
	foreach($tags[1] as $tag) {
		$db->insert('user_tags',"user_id=$user_id, tag='$tag'");
	}
}
?>