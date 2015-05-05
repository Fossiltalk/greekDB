<?	
	if(!empty($_POST))
	{
		$con = mysqli_connect("104.237.146.186","greekDB_user",'6(]cSWAH@35m',"greekDB"); 
		
		if (mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
		}
		
		$sql="
			INSERT INTO greekDB.bugs(
				email,
				details,
				timestamp
				)
			VALUES(
				'".htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8')."',
				'".preg_replace("/[^a-zA-Z 0-9]+/", " ", $_POST['details'])."',
				NOW())
				";
				
		if (!mysqli_query($con,$sql))
		{
			die('Error: ' . mysqli_error($con));
		}
				
		mysqli_close($con);
		print("<script>close();</script>");
		
	}
?>
<!doctype html>
	<head>
		<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/pop-up_std.css">
		<meta charset="utf-8">
		<title>Bug Reporter</title>
	</head>
<div class="application">
	<form action="<?echo $_SERVER['PHP_SELF'];?>" method="POST">
		Email:
		<br />
		<input type="email"
			name="email"
			size="20"
			autofocus="autofocus"
		/>
		<br />

		Details:
		<br />
		<textarea name="details"
			rows="4"
			cols="50"
			></textarea>
		<br />
		<input type="submit" value="Report Bug" />
	</form>
</div>