<?php
//

require_once( 'index.php');


try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
} catch( Exception $ex ) {
  // When validation fails or other local issues
}
 
// see if we have a session
if ( isset( $session ) ) {
  // graph api request for user data
  $taggable = (new FacebookRequest( $session, 'GET', '/me/taggable_friends' ))->execute()->getGraphObject()->asArray();

// output response
echo '<pre>' . print_r( $taggable, 1 ) . '</pre>';

// output total friends
echo count( $taggable['data'] );
} else {
  // show login url
  echo '<a href="' . $helper->getLoginUrl() . '">Login</a>';
}






