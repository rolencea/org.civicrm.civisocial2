<?php
// 


if(sizeof($tweets)) {
	require_once('../display_lib.php');
	
	$tweet_html = '<link rel="stylesheet" type="text/css" media="all" href="tweet.css" />';
	$template_html = file_get_contents('tweet_template.html');
	foreach($tweets as $tweet) {
		$tweet_html .= $template_html;
		$tweet_html = str_replace('[user_id]',$tweet['user_id'],$tweet_html);	
		$tweet_html = str_replace('[screen_name]',$tweet['screen_name'],$tweet_html);
		$tweet_html = str_replace('[name]',$tweet['name'],$tweet_html);
		$tweet_html = str_replace('[tweet_id]',$tweet['tweet_id'],$tweet_html);
		$tweet_html = str_replace('[tweet_text]',linkify($tweet['tweet_text']),$tweet_html);
		$tweet_html = str_replace('[profile_image_url]',$tweet['profile_image_url'],$tweet_html);
		$tweet_html = str_replace('[created_at]',twitter_time($tweet['created_at']),$tweet_html);
		$tweet_html = str_replace('[retweet_count]',$tweet['retweet_count'],$tweet_html);
	}
	
	print $tweet_html;
} else {
	print "No tweets found";
}

?>