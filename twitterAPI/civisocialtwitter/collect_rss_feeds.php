<?php
// 


require('config.php');
require('db_lib.php');
$db = new db();

// Get all feed URLs
$query = "SELECT name, url
	FROM autotweet_rss_feeds";
$results = $db->select($query);
while ($row = mysqli_fetch_assoc($results)) {
	$feed_name = trim($row['name']);
	$feed_url = $row['url'];
	
	// Load the feed as XML
	$feed_xml = simplexml_load_file($feed_url);
	$feed_items = $feed_xml->channel->item;
	
	// Loop through each feed item
	foreach($feed_items as $item){
		
		// Every RSS feed item has a unique ID
		$guid = $db->escape($item->guid);
		if (!$db->in_table('autotweet_rss_tweets',"guid='$guid'")) {
			
			// Assemble a tweet using the title and link (URL) elements 
			// $default_tag is set in config.php
			$via_str = " (via $feed_name)";
			$title_length = 140 - (strlen(' ' . $item->link . $via_str . ' ' . $default_tag));
			if (strlen($item->title) > $title_length) {
				
				// Use an ellipse if tweet text is too long
				$title_str = substr($item->title,0,$title_length-1) . '&hellip;';
			} else {
				$title_str = $item->title;
			}
				
			$tweet_text = $title_str . ' ' . 
				$item->link . $via_str . ' ' . $default_tag;
			
			$title = $db->escape($item->title);
			$link = $db->escape($item->link);
			$pub_date = $db->date($item->pubDate);
			
			// Save the feed item with the assembled tweet
			$field_values = "feed_name = '$feed_name', title='$title',link='$link',
				guid='$guid', pub_date='$pub_date', tweet_text = '$tweet_text'";
			$db->insert('autotweet_rss_tweets',$field_values);
		}
	}	
}
?>