<?
	require("functions.php");
	generateContactCard($_GET['firstName'],$_GET['lastName'], $_GET['phoneNumber'], $_GET['email'], $_GET['company'], $_GET['website'],
							$_GET['street'], $_GET['streetContinued'], $_GET['city'], $_GET['state'], $_GET['zipcode']);
?>