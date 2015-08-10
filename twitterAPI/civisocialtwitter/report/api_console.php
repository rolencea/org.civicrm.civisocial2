<?php

require('page_top.html');

print '<h2>API Console links</h2>';
print 'Chapter 3. <a href="http://140dev.com/twitter-api-console/?method=GET&url=1.1/users/show&screen_name=foodiecentral&run=1">/users/show for @Foodiecentral</a><br/>';
print 'Chapter 3. <a href="http://140dev.com/twitter-api-console/?method=GET&url=1.1/users/show&screen_name=foodnetwork&run=1">/users/show for @FoodNetwork</a><br/>';
print 'Chapter 3. <a href="http://140dev.com/twitter-api-console/?method=GET&url=1.1/users/show&screen_name=wholefoods&run=1">/users/show for @WholeFoods</a><br/>';
print 'Chapter 6. <a href="http://140dev.com/twitter-api-console/?method=GET&url=1.1/statuses/user_timeline&screen_name=foodnetwork&count=1&run=1">/statuses/timeline for @FoodNetwork</a><br/>';
print 'Chapter 6. <a href="http://140dev.com/twitter-api-console/?method=GET&url=1.1/search/tweets&q=foodnetwork&run=1">/search/tweets for FoodNetwork</a><br/>';


require('page_bottom.html');
?>