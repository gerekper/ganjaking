<?php
/**
 * Facebook SDK 4.4 loader
 */

require __DIR__ . '/Facebook/facebook-php-sdk-v4-4.0.23/autoload.php';

use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\FacebookJavaScriptLoginHelper;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\GraphSessionInfo;