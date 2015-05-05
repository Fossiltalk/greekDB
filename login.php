<?php
	require("commonfiles/common.php");
	require("login/logincode.php");
?>
<!doctype html class="grad">
	<head>
		<?php	
			require("commonfiles/header.html");
		?>
	</head>

	<?php   require("commonfiles/globalnavigationbar.php");?>
	<div id="content">
		<?if(isMobile())
		{?>
		<aside></aside>
		<?}?>
		<div class="application">
			<div id="loginbox">
				<h1 class="center">Login</h1>
				<form action="login.php" method="post">
					Username:<br />
					<input type="text" name="username" value="<?php echo $submitted_username; ?>" size="30" autofocus="autofocus"/>
					<br /><br />
					Password:<br />
					<input type="password" name="password" value="" size="30"/>
					<br /><br />
					<input type="submit" value="Login"/>
				</form>
				<p class="center"><a href="register.php">Register</a></p>
			</div>
		</div>
	</div>
	<?php require("commonfiles/footer.html");?>	
</html>
												<!--------------------Page CSS Begins Here-------------------------->
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

	#loginbox
	{
		width:225px;
		height:275px;
		margin:auto;
		top:-200px;
		left:230px;
	}

	.center{
		text-align:center;
	}
</style>