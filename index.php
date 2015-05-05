<?php
	require("commonfiles/common.php");
?>
<!doctype html class="grad">
	<head>
		<?php	require("commonfiles/header.html");?>
	</head>
	<?php
		require("commonfiles/globalnavigationbar.php");
		
		if($_SESSION)
		{	
			header("Location: landing.php");
			die("landing.php");
		}
	?>
	<div id="content">
		<aside></aside>
		<div class="application">
			<b>News:</b>
			<ul>
				<li>Completed migration to Linode! (2015/02/08) 17:10 EST</li>
				<li>Completed upgrade from Apache to NginX! (2015/02/08) 16:30 EST</li>
				<li>Added more Judicial Reports (2015/02/10) 20:30 EST</li>
				<li>Added SMS functionality!!! (2015/02/13) 20:30 EST</li>
				<li>Removed 1100 lines of code! (2015/02/13) 20:30 EST</li>
				<li>Added Multi-select to Judicial Cases!!! (2015/02/13) 20:30 EST</li>
				<li>SSL Encryption is now Active! (2015/02/17) 12:35 EST</li>
				<li>Automated Timestamps for Phone Number and Email Updates! (2015/02/19) 12:35 EST</li>
				<li>Added Accounts Receivable Report for Financial Officers (2015/02/19) 12:50 EST</li>
				<li>Traffic from our server to our SMS Provider is now encrypted too! (2015/02/19) 12:55 EST</li>
				<li>First steps toward compatibility across all screen resolutions and aspect ratios! (2015/03/09) 23:39 EST</li>
				<li>UI now behaves appropriately on mobile and unusual screen sizes/aspect ratios! (2015/03/11) 00:39 EST</li>
				<li>Account Receivable Report now includes fines. (2015/03/12) 13:10 EST</li>
				<li>Bug fixes and Useability Updates (2015/03/16) 23:10 EST</li>
				<li>Lineage Tree Report! (2015/03/18) 01:14 EST</li>
				<li>Performance Improvements! (2015/03/19) 00:11 EST</li>
			</ul>
		</div>
	</div>
	<?php require("commonfiles/footer.html");?>
</html>
												<!--------------------Page CSS Begins Here-------------------------->
<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/3panel.css">

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