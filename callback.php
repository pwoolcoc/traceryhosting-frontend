<?php

require "twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth($_ENV["TWITTER_CONSUMER_KEY"], $_ENV["TWITTER_CONSUMER_SECRET"]);

$pdo = new PDO("mysql:dbname=" . $_ENV["MYSQL_DB_NAME"] . ";host=" . $_ENV["MYSQL_HOST"] . ";port=" . $_ENV["MYSQL_PORT"] . ";charset=utf8mb4", $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASS"]);

$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


session_set_cookie_params(2678000);
session_start();

function login_failure()
{
  $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();
    die('Error! Couldn\'t log in. <a href="http://cheapbotsdonequick.com">Retry</a>');
}


$request_token = array();
$request_token['oauth_token'] = $_SESSION['oauth_token'];
$request_token['oauth_token_secret'] = $_SESSION['oauth_token_secret'];

if ((isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) 
  || (isset($_GET['denied'])) 
  || !isset($_GET['oauth_token'])) {
    // Abort! Something is wrong.



    login_failure();
}
$connection = new TwitterOAuth($_ENV["TWITTER_CONSUMER_KEY"], $_ENV["TWITTER_CONSUMER_SECRET"], $request_token['oauth_token'], $request_token['oauth_token_secret']);
$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));

if (!(isset($access_token["oauth_token"])) || !(isset($access_token["oauth_token_secret"])))
{
  login_failure();
}

//todo verify that we succeeded


//var_dump($access_token);

//die();



  $stmt = $pdo->prepare('INSERT INTO traceries (token,token_secret, screen_name, user_id) VALUES(:token, :token_secret, :screen_name, :user_id) ON DUPLICATE KEY UPDATE token=:token2, token_secret=:token_secret2, screen_name=:screen_name2, user_id=:user_id2');

  $stmt->execute(array('token' => $access_token["oauth_token"], 
                       'token_secret' => $access_token["oauth_token_secret"], 
                       'screen_name' => $access_token["screen_name"],
                       'token2' => $access_token["oauth_token"], 
                       'token_secret2' => $access_token["oauth_token_secret"], 
                       'screen_name2' => $access_token["screen_name"],
                       'user_id' => $access_token["user_id"],
                       'user_id2' => $access_token["user_id"]
                      ));




$_SESSION['oauth_token'] = $access_token["oauth_token"]; //this should be this already?

$connection = new TwitterOAuth($_ENV["TWITTER_CONSUMER_KEY"], $_ENV["TWITTER_CONSUMER_SECRET"], $access_token['oauth_token'], $access_token['oauth_token_secret']);
$user_data = $connection->get("users/show", array("screen_name" => $access_token["screen_name"]));

$_SESSION['profile_pic'] = $user_data->profile_image_url; 
$_SESSION['screen_name'] =  $access_token["screen_name"]; 
$_SESSION['user_id'] = $access_token["user_id"];

if (!(isset($user_data) || !(isset($user_data->profile_image_url))))
{
  login_failure(); 
}

header('Location: http://cheapbotsdonequick.com');
die();


?>

