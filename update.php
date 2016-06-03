<?php

header('Content-Type: application/json');

require "twitteroauth/autoload.php";

use Abraham\TwitterOAuth\TwitterOAuth;

$connection = new TwitterOAuth($_ENV["TWITTER_CONSUMER_KEY"], $_ENV["TWITTER_CONSUMER_SECRET"]);

$pdo = new PDO("mysql:dbname=" . $_ENV["MYSQL_DB_NAME"] . ";host=" . $_ENV["MYSQL_HOST"] . ";port=" . $_ENV["MYSQL_PORT"] . ";charset=utf8mb4", $_ENV["MYSQL_USER"], $_ENV["MYSQL_PASS"], array(
    PDO::MYSQL_ATTR_FOUND_ROWS => true
));

$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


session_set_cookie_params(2678000);
session_start();

if (isset($_SESSION['oauth_token']))
{
	try
	{
		//todo validate json here

		$stmt = $pdo->prepare('UPDATE traceries SET frequency=:frequency, tracery=:tracery, public_source=:public_source WHERE token=:token');

	  	$stmt->execute(array('frequency' => $_POST['frequency'], 'tracery' => $_POST['tracery'],'public_source' => $_POST['public_source'], 'token' => $_SESSION['oauth_token']));

	  	if ($stmt->rowCount() == 1)
	  	{
	  		//mail("vtwentyone+php@gmail.com", "Bot update : " . $_SESSION['screen_name'] . " every " . $_POST['frequency'] . " minutes", $_POST['tracery']);
			die ("{\"success\": true}");
	  	}
	  	else
	  	{
			die ("{\"success\": false, \"reason\" : \"row count mismatch\"}");
	  	}

	}
	catch(PDOException $e)
	{
		
		error_log($e);
		die ("{\"success\": false, \"reason\" : \"db err " . $e->getCode() . "\"}");
		//die($e); //todo clean this
	}

}
else
{
	die ("{\"success\": false, \"reason\" : \"Not signed in\"}");
}



?>
