<?php


require "twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

define('OAUTH_CALLBACK', "http://cheapbotsdonequick.com/callback.php");

$connection = new TwitterOAuth($_ENV["TWITTER_CONSUMER_KEY"], $_ENV["TWITTER_CONSUMER_SECRET"]);
session_set_cookie_params(2678000);
session_start();

$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));

$_SESSION['oauth_token'] = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

$url = $connection->url('oauth/authenticate', array('oauth_token' => $request_token['oauth_token']));

//redirect to $url

header('Location: ' . $url);
die();


?>
