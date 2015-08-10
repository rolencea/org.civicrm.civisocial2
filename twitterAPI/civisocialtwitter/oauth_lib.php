<?php
// 


// Create an OAuth connection
function get_connection() {

	// Get OAuth tokens for engagement account
	require('config.php');
	require('tmhOAuth.php');
	
	// Create the connection
	// The OAuth tokens are kept in config.php
	$connection = new tmhOAuth(array(
		  'consumer_key'    => $consumer_key,
		  'consumer_secret' => $consumer_secret,
		  'user_token'      => $user_token,
		  'user_secret'     => $user_secret
	));
			
	return $connection;
}

?>