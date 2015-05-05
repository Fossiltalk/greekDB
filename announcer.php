<?
	function makeUseablePhone($phoneNumberString)
	{
		$phoneNumberString = preg_replace('/\D/', '', $phoneNumberString); 

		$length = strlen($phoneNumberString);

		if($length > 10 && $length < 13)
			return $phoneNumberString;
		else if($length == 10)
			return '1'.$phoneNumberString;
		else
			exit;
	}

	function sendToPhone($destinationPhoneNumber, $content)
	{	
		$api_key = '2deea24c';
		$api_secret = '3a3ceb68';
		$source = '12173183349';

		$url = 'https://rest.nexmo.com/sms/json';
		$data = array(
			'api_key' => $api_key,
			'api_secret' => $api_secret,
			'from' => $source,
			'to' => makeUseablePhone($destinationPhoneNumber),
			'text' => $content);

		// use key 'http' even if you send the request to https://...
		$options = array(
			'http' => array(
	 	   'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	 	   'method'  => 'POST',
	 	   'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
	}
?>

<?
	require("commonfiles/common.php");
	require("commonfiles/permissions.php");
     
    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    }
	
	$pagePermission = $_SESSION['user']['pdirectory'];
	
	if($pagePermission == 0)
	{
        header("Location: no_access.html"); 
        die("Redirecting to no_access.html"); 
	}
	
	if(!empty($_POST))
	{
		echo '<script type="text/javascript">'
		   , 'confirm("Kicking off Messages.  DO NOT refresh the page.\n\nA notification will appear when the process has finished.");'
		   , '</script>';
		
		next($_POST);
	
		$query = 'SELECT phonenumber FROM greekDB.members WHERE idorganization = '
			.$_SESSION['user']['idorganization'].' AND idchapter = '
			.$_SESSION['user']['idchapter'].' AND (';
		
		for($i = 0; $i < count($_POST)-1; $i++)
		{
			$query .= "localid = ".key($_POST).' OR ';
			next($_POST);
		}
	
		$query = substr($query,0,-4).');';
	
		try 
		{ 
			$stmt = $db->prepare($query); 
			$stmt->execute(); 
		} 
		catch(PDOException $ex) 
		{ 
		    die("Failed to run query: " . $ex->getMessage()); 
		} 

		$numbersToText = $stmt->fetchAll();
		
		foreach($numbersToText as $numberToText):
		{
			$content = $_POST['content'];
			sendToPhone($numberToText['phonenumber'], $content);
			sleep(1.01);
		}
		endforeach;
		
		echo '<script type="text/javascript">'
		   , 'confirm("Messages Sent!");'
		   , '</script>';
	}
?>

<!doctype html class="grad">
	<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/3panel.css">
	<head>
		<meta charset="utf-8">
		<title>Bulk Announcer</title>
		<?php	require("commonfiles/header.html"); ?>
	</head>
	<?php   require("commonfiles/globalnavigationbar.php"); ?>
<div id="content">
	<aside>
		<?
			include ("commonfiles/navigationbanner.php");
		?>
	</aside>
	<div class="application">
		
		<form name="massTextForm" onsubmit="return confirm('Do you really want to send this message?');" method="POST">
			<h1>Bulk Announcer</h1>
			<br />
			<b>Message:</b><br />
			<textarea maxlength="160" cols="60" rows="3" name="content">Announcement:</textarea>
			<br />
			<br />
			<b>Members:</b>
			<?
				$query="SELECT members.localid, members.firstname, members.lastname
						FROM greekDB.members
						WHERE members.isAlumnus = 0	
						ORDER BY
							localid;";
						
				try 
				{ 
					$stmt = $db->prepare($query); 
					$stmt->execute(); 
				} 
				catch(PDOException $ex) {} 

				$members = $stmt->fetchAll();
			
				generateMemberCheckBoxTable($members, 4);
			?>
			<input type="submit" onsubmit="return confirmSend()">
		</form>
	</div>
</div>
	<?php require("commonfiles/footer.html");?>	
</html>

<style>
	html{
		background-image: -webkit-gradient(
			linear,
			left top,
			left bottom,
			color-stop(0, #002B54),
			color-stop(1, #0081C6)
		);
		background-image: -o-linear-gradient(bottom, #002B54 0%, #0081C6 100%);
		background-image: -moz-linear-gradient(bottom, #002B54 0%, #0081C6 100%);
		background-image: -webkit-linear-gradient(bottom, #002B54 0%, #0081C6 100%);
		background-image: -ms-linear-gradient(bottom, #002B54 0%, #0081C6 100%);
		background-image: linear-gradient(to bottom, #002B54 0%, #0081C6 100%);
	}
</style>

<style>
	div.memberCheckTable
	{
		border: 2px solid black;
	}
	
	div.application *{
		padding-left: 1px;
		padding-right: 1px;
	}
	
	h1
	{
		margin:auto;
	}
</style>