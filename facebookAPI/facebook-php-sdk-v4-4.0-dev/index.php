<?php
//

require_once( 'src/Facebook/HttpClients/FacebookHttpable.php' );
require_once( 'src/Facebook/HttpClients/FacebookCurl.php' );
require_once( 'src/Facebook/HttpClients/FacebookCurlHttpClient.php' );

require_once( 'src/Facebook/Entities/AccessToken.php' );
require_once( 'src/Facebook/Entities/SignedRequest.php' );

require_once( 'src/Facebook/FacebookSession.php' );
require_once( 'src/Facebook/FacebookRedirectLoginHelper.php' );
require_once( 'src/Facebook/FacebookSignedRequestFromInputHelper.php' ); // added in v4.0.9
require_once( 'src/Facebook/FacebookRequest.php' );
require_once( 'src/Facebook/FacebookResponse.php' );
require_once( 'src/Facebook/FacebookSDKException.php' );
require_once( 'src/Facebook/FacebookRequestException.php' );
require_once( 'src/Facebook/FacebookOtherException.php' );
require_once( 'src/Facebook/FacebookAuthorizationException.php' );

// these two classes required for canvas and tab apps
require_once( 'src/Facebook/FacebookCanvasLoginHelper.php' );
require_once( 'src/Facebook/FacebookPageTabHelper.php' );

require_once( 'src/Facebook/GraphObject.php' );
require_once( 'src/Facebook/GraphSessionInfo.php' );

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
FacebookSession::setDefaultApplication( 'xxx','yyy' );

// init login helper
$helper = new FacebookRedirectLoginHelper( 'http://sites.local/php-sdk-4.0/redirect.php' );

 
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\GraphUser;
 
// For apps using Facebook Canvas
// use Facebook\FacebookCanvasLoginHelper;
 
// For apps using the JavaScript SDK
// use Facebook\FacebookJavaScriptLoginHelper;
 
// Initialize the Facebook SDK.
FacebookSession::setDefaultApplication( '491605807656085', 'd6331b0573350cab399d43c276cf47e8' );
$helper = new FacebookRedirectLoginHelper('http://localhost/civisocial/');


//allowing users to login to ur website by providing a login link and authentication functionality

/**

try {
    if ( isset( $_SESSION['access_token'] ) ) {
        // Check if an access token has already been set.
        $session = new FacebookSession( $_SESSION['access_token'] );
    } else {
        // Get access token from the code parameter in the URL.
        $session = $helper->getSessionFromRedirect();
    }
} catch( FacebookRequestException $ex ) {
 
    // When Facebook returns an error.
    print_r( $ex );
} catch( \Exception $ex ) {
 
    // When validation fails or other local issues.
    print_r( $ex );
}
if ( isset( $session ) ) {
 
    // Retrieve & store the access token in a session.
    $_SESSION['access_token'] = $session->getToken();
    // Logged in
    echo 'Successfully logged in!';
} else {
 
    // Generate the login URL for Facebook authentication.
    $loginUrl = $helper->getLoginUrl();
    echo '<a href="' . $loginUrl . '">Login</a>';
}

*/



//making a request to the graph API




try {
    $me = (new FacebookRequest(
        $session, 'GET', '/me'
    ))->execute()->getGraphObject(GraphUser::className());
 
    // Output user name.
    echo $me->getName();
} catch (FacebookRequestException $ex) {
 
    // The Graph API returned an error.
    print_r( $ex );
} catch (\Exception $ex) {
 
    // Some other error occurred.
    print_r( $ex );
}