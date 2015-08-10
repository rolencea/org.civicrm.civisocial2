<?php
//

require_once( 'facebook-php-sdk-v4-4.0-dev/autoload.php');


use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookCurlHttpClient;

use Facebook\Entities\AccessToken;
use Facebook\Entities\SignedRequest;

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookSignedRequestFromInputHelper; // added in v4.0.9
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;

// these two classes required for canvas and tab apps
use Facebook\FacebookCanvasLoginHelper;
use Facebook\FacebookPageTabHelper;

// start session
session_start();




// init app with app id and secret

FacebookSession::setDefaultApplication( '491605807656085', 'd6331b0573350cab399d43c276cf47e8' );





// login helper with redirect_uri
$helper = new FacebookRedirectLoginHelper( 'http://mss.hostoi.com/' );
 $loginUrl = $helper->getLoginUrl();



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








   