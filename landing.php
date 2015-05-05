<?php 

    // First we execute our common code to connection to the database and start the session 
    require("commonfiles/common.php");
	require("commonfiles/lists.php");
	require("commonfiles/permissions.php");
	
    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 
	
	$query = "
		SELECT localid, firstname, lastname
		FROM members
		WHERE(
			members.idorganization = ".$_SESSION['user']['idorganization']."
				AND
			members.idchapter = ".$_SESSION['user']['idchapter']."
				AND
			members.localid = ".$_SESSION['user']['localid']."
			);";

	try 
 	{ 
		$stmt = $db->prepare($query); 
		$stmt->execute(); 
	} 
	catch(PDOException $ex) 
	{ 
	 	die("Failed to run query: " . $ex->getMessage()); 
 	} 

	$member = $stmt->fetch();
?> 
<!doctype html class="grad">
	<head>
		<?php	require("commonfiles/header.html"); ?>
	</head>
	<?php   require("commonfiles/globalnavigationbar.php"); ?>
	
<?	
	$query = "
				SELECT twitterhandle, twitterwidgetid, googlecalendarembedcode
				FROM chapters
				WHERE (idchapter = ".$_SESSION['user']['idchapter']."
							AND
						idorganization = ".$_SESSION['user']['idorganization']."
						);";

	try 
	{ 
		$stmt = $db->prepare($query); 
		$stmt->execute(); 
	} 
	catch(PDOException $ex) 
	{ 
 		die("Failed to run query: " . $ex->getMessage()); 
	} 

	$apiData = $stmt->fetch();
	
	$chaptertwitterhandle = $apiData['twitterhandle'];
	$chaptertwitterwidgetid = $apiData['twitterwidgetid'];
	$googlecalendarembedcode = $apiData['googlecalendarembedcode'];
?>
	<div id="content">
		<aside>
			<?
				require ("commonfiles/navigationbanner.php");
			?>
		</aside>
		<div class="application">
			<div id="welcomemessage">
				Hello <?php echo htmlentities($member['firstname']." ".$member['lastname'], ENT_QUOTES, 'UTF-8'); ?>, welcome to greekDB!
			</div>
<!---------------------------------------------------------------------Begin User Applets-------------------------------------------------------------------------------->
			<div id="userapplets">
					<h2>Member Information</h2>
					<?
						require("landing/user_applets/communityserviceapplet.php");
						require("landing/user_applets/judicialapplet.php");
						require("landing/user_applets/billingapplet.php");
					?>
			</div>
				
			<div id="officerapplets">
			<?
				if($_SESSION['user']['pservice'] >= 5 || $_SESSION['user']['pjudicial'] >= 5 || $_SESSION['user']['pbilling'] >= 5)
				{
					print("<h2>Officer Information</h2>");
					if($_SESSION['user']['pservice'] >= 5)
					{
						require("landing/officer_applets/officer_serviceapplet.php");
					}
					if($_SESSION['user']['pjudicial'] >= 5)
					{
						require("landing/officer_applets/officer_judicialapplet.php");
					}
					if($_SESSION['user']['pbilling'] >= 5)
					{
						require("landing/officer_applets/officer_treasuryapplet.php");
					}
				}
			?>
			</div>
			
			<?
			if(!isMobile())
			{
				$query = "
					SELECT twitterhandle, twitterwidgetid, colordark, colormedium, colorlight
					FROM organizations
					WHERE (idorganization = ".$_SESSION['user']['idorganization'].");";

				try 
				{ 
					$stmt = $db->prepare($query); 
					$stmt->execute(); 
				} 
				catch(PDOException $ex) 
				{ 
			 		die("Failed to run query: " . $ex->getMessage()); 
				} 

				$apiData = $stmt->fetch();
	
				$organizationtwitterhandle = $apiData['twitterhandle'];
				$organizationtwitterwidgetid = $apiData['twitterwidgetid'];
			
			?>
			<div id="twitter">
				<h2>Feeds</h2>
				<div id="twitterorganizationblock">
					<h4>Organization</h4>
					<?
						getTwitterFeed($organizationtwitterwidgetid,$organizationtwitterhandle,200);
					?>
				</div>
				<div class="spacer"></div>
				<div id="twitterchapterblock">
					<h4>Chapter</h4>
					<?
						getTwitterFeed($chaptertwitterwidgetid,$chaptertwitterhandle,200);
					?>
				</div>
				<div class="spacer"></div>
			</div>
			<?}?>
		</div>
	</div>
	<?php require("commonfiles/footer.html");?>	
</html>
												<!--------------------------Page CSS Begins Here------------------------------>

<?
	if(isMobile())
	{
		echo '<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/2panel.css">';
	}
	else
	{
		echo '<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/3panel.css">';
	}
?>

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
												<!--------------------Applet CSS Begins Here------------------------------>
<style>
	div#welcomemessage{
		margin-left: 4px;
		margin-bottom: 5px;
		margin-top: 2px;
	}

	div#officerapplets
	{
		margin-bottom: 10px;
		min-height: 150px;
		height: 20%;
		display: block;
	}
	
	div#userapplets
	{
		margin-bottom: 10px;
		min-height: 150px;
		height: 20%;
		display: block;
	}
	
	div#twitter
	{
		min-height: 500px;
		display: block;
		max-height: 1200px;
		padding-right: 10px;
	}
	
	div.spacer {
		height: 17px;
		display: block;
	}
	
	div#twitterorganizationblock
	{
		min-height: 220px;
		height: 50%;
		max-height: 600px;
		display:block;
	}
	
	div#twitterchapterblock
	{
		min-height: 220px;
		height: 50%;
		max-height: 600px;
		display: block;
	}
	
	div#officerapplets h2
	{
		margin-bottom: 5px;
		width: 50%;
		color:black;
		border-bottom: 1px solid #002B54;
	}

	div#userapplets h2
	{
		margin-bottom: 5px;
		width: 50%;
		color:black;
		border-bottom: 1px solid #002B54;
	}

	div#twitter h2
	{
		margin-bottom: 0px;
		width: 50%;
		color:black;
		border-bottom: 1px solid #002B54;
	}
	div#twitter h4
	{
		margin-bottom: 0px;
		margin-top: 0px;
	}
	
	iframe[id^='twitter-widget-']{ width:100% !important; min-height: 200px !important; height: 100% !important;}
</style>