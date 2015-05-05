<?php 

    // First we execute our common code to connection to the database and start the session 
    require("commonfiles/common.php");
     
    // This if statement checks to determine whether the registration form has been submitted 
    // If it has, then the registration code is run, otherwise the form is displayed 
    if(!empty($_POST)) 
    { 
        // Ensure that the user has entered a non-empty username 
        if(empty($_POST['username'])) 
        { 
            die("Please enter a username."); 
        } 
         
        // Ensure that the user has entered a non-empty password 
        if(empty($_POST['password'])) 
        { 
            die("Please enter a password."); 
        } 

        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            die("Invalid E-Mail Address"); 
        } 

        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                username = :username
					OR
				localid = :localid
        "; 
         
        // This contains the definitions for any special tokens that we place in 
        // our SQL query.  In this case, we are defining a value for the token 
        // :username.  It is possible to insert $_POST['username'] directly into 
        // your $query string; however doing so is very insecure and opens your 
        // code up to SQL injection exploits.  Using tokens prevents this. 
        // For more information on SQL injections, see Wikipedia: 
        // http://en.wikipedia.org/wiki/SQL_Injection 
        $query_params = array( 
            ':username' => $_POST['username'],
			':localid' => $_POST['localid'] 
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        $row = $stmt->fetch(); 
         
        if($row) 
        { 
            die("This username is already in use"); 
        } 
         
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                email = :email 
        ;"; 
         
        $query_params = array( 
            ':email' => $_POST['email'] 
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        } 
         
        $row = $stmt->fetch(); 
         
        if($row) 
        { 
            die("This email address is already registered"); 
        } 
 
        $query = " 
            INSERT INTO users ( 
                username, 
                password, 
                salt, 
                email
            ) VALUES ( 
                :username, 
                :password, 
                :salt, 
                :email
            ) 
        "; 
         
        $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
         
        $password = hash('sha256', $_POST['password'] . $salt); 
         
        for($round = 0; $round < 65536; $round++) 
        { 
            $password = hash('sha256', $password . $salt); 
        } 
		
		$parsedOrganizationSelector = explode('|',$_POST['chapterselector']);
		$selectedChapter = intval($parsedOrganizationSelector[0]);
		$selectedOrganization = intval($parsedOrganizationSelector[1]);
		
        $query_params = array( 
            ':username' => $_POST['username'], 
            ':password' => $password, 
            ':salt' => $salt, 
            ':email' => $_POST['email']
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        }
		
		$addchapterinfo = " 
			UPDATE
				greekDB.users
			SET users.idchapter = ".$selectedChapter.",
			users.idorganization = ".$selectedOrganization.",
			users.localid = ".$_POST['localid']."
			WHERE users.username = '".$_POST['username']."';";

		$stmt = $db->prepare($addchapterinfo); 
		$stmt->execute();
		
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    } 
     
?> 
<title>Registration</title>
<h1>Register</h1> 
<form action="register.php" method="post"> 
    Username:<br /> 
    <input type="text" name="username" value="" /> 
    <br /><br /> 
    E-Mail:<br /> 
    <input type="text" name="email" value="" /> 
    <br /><br /> 
    Password:<br /> 
    <input type="password" name="password" value="" /> 
    <br /><br /> 
	Organization:<br />
	<select name="chapterselector">
	<?php
		$query = " 
			SELECT
				chapters.idorganization, idchapter, organizationname, chapterdesignation
			FROM organizations
			INNER JOIN chapters
			ON chapters.idorganization = organizations.idorganization
			ORDER BY organizationname ASC,
			chapterdesignation ASC;";

		$stmt = $db->prepare($query); 
		$stmt->execute(); 
	    // Finally, we can retrieve all of the found rows into an array using fetchAll
		$organizations = $stmt->fetchAll();
		foreach($organizations AS $organization): ?>
			<option value="<?echo $organization['idchapter']."|".$organization['idorganization']?>">
				<?
				echo $organization['organizationname']
					." - "
					. $organization['chapterdesignation'];
				?>
			</option>
		<?php endforeach; ?>
	</select>
	<br /><br />
    Member ID (Bond Number):<br /> 
    <input type="number" name="localid" value="" /> 
    <br /><br />
	<input type="submit" value="Register" />
</form>