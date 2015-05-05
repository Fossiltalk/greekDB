<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/sessionbar.css">
</head>

<div id="sessionbar">
	<div id="session">
		<div id="login">
			<?
				if(empty($_SESSION['user'])) 
				{
					echo '<a href="/commonfiles/nocontent.php">Guests</a>';
					echo '<a href="login.php">Sign Up/Login</a>';
				}
				else
				{	
					echo '<a href="edit_account.php">Edit Account</a>';
					echo '<a href="logout.php">Logout</a>';
				}
			?>
		</div>
		
		<div id="bug">
			<a href="javascript:displayBugReporter()"><img src="https://s3.amazonaws.com/greekdb/resources/sessionbar/icons/bug.png" alt="Bug Reporter" height="18" width="18"></a>
		</div>
	</div>
	
	<div id="logo">
		<a href="index.php"><img src="https://s3.amazonaws.com/greekdb/resources/sessionbar/icons/greekDB-logo.png" alt="greekDB" height="20" width="88"></a>
	</div>
</div>

<script>
function displayBugReporter()
{
    window.open("bug_reporter.php",
		"_blank",
		"toolbar=no, scrollbars=no, resizable=no, top=400, left=400, width=410, height=206");
}
</script>