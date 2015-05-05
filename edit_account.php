<?php 

    // First we execute our common code to connection to the database and start the session 
    require("commonfiles/common.php"); 
	require("commonfiles/lists.php");
    // At the top of the page we check to see whether the user is logged in or not 
    if(empty($_SESSION['user'])) 
    { 
        // If they are not, we redirect them to the login page. 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 
     
    if(!empty($_POST)) 
    { 
        // Make sure the user entered a valid E-Mail address 
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            die("Invalid E-Mail Address"); 
        } 

        if($_POST['email'] != $_SESSION['user']['email']) 
        { 
            // Define our SQL query 
            $query = " 
                SELECT 
                    1 
                FROM users 
                WHERE 
                    email = :email 
            "; 
             
            // Define our query parameter values 
            $query_params = array( 
                ':email' => $_POST['email'] 
            ); 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
             
            // Retrieve results (if any) 
            $row = $stmt->fetch(); 
            if($row) 
            { 
                die("This E-Mail address is already in use"); 
            } 
        } 

        if(!empty($_POST['password'])) 
        { 
            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
            $password = hash('sha256', $_POST['password'] . $salt); 
            for($round = 0; $round < 65536; $round++) 
            { 
                $password = hash('sha256', $password . $salt); 
            } 
        } 
        else 
        { 
            // If the user did not enter a new password we will not update their old one. 
            $password = null; 
            $salt = null; 
        } 
         
        // Initial query parameter values 
        $query_params = array( 
            ':email' => $_POST['email'], 
            ':user_id' => $_SESSION['user']['id'], 
        ); 

        if($password !== null) 
        { 
            $query_params[':password'] = $password; 
            $query_params[':salt'] = $salt; 
        } 

        $query = " 
            UPDATE users 
            SET 
                email = :email 
        "; 

        if($password !== null) 
        { 
            $query .= " 
                , password = :password 
                , salt = :salt 
            "; 
        } 

        $query .= " 
            WHERE 
                id = :user_id 
        "; 
         
        $stmt = $db->prepare($query); 
        $result = $stmt->execute($query_params); 

        $_SESSION['user']['email'] = $_POST['email'];
		
		if($_POST['providerSelector'] != $_SESSION['user']['cellularProvider'])
		{
			$_SESSION['user']['cellularProvider'] = $_POST['providerSelector'];
			
			$query = "
					UPDATE greekDB.users
					SET users.cellularProvider = ".$_POST['providerSelector'].
					" WHERE users.id = ".$_SESSION['user']['id'].";";
			
		    try 
		    { 
		        $stmt = $db->prepare($query); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 
		}
         
        header("Location: landing.php"); 
        die("Redirecting to landing.php"); 
    } 
     
?> 
<!doctype html class="grad">
	<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/3panel.css">
	<head>
		<meta charset="utf-8">
		<title>Edit Account</title>
		<?php	require("commonfiles/header.html"); ?>
	</head>
	<?php   require("commonfiles/globalnavigationbar.php"); ?>
	<div id="content">
		<aside>
			<?
				require ("commonfiles/navigationbanner.php");
			?>
		</aside>
		<div class="application">
			<h1>Edit Account</h1> 
			<form action="edit_account.php" method="post"> 
				Username:<br /> 
				<b><?php echo htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'); ?></b> 
				<br /><br /> 
				E-Mail Address:<br /> 
				<input type="text" name="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>" /> 
				<br /><br /> 
				Password:<br /> 
				<input type="password" name="password" value="" /><br /> 
				<i>(leave blank if you do not want to change your password)</i> 
				<br /><br />
				<input type="submit" value="Update Account" /> 
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
	.application h1{
		margin:auto;
	}
</style>