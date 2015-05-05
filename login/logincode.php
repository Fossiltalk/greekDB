<?php 
    $submitted_username = '';
	require ("commonfiles/functions.php");
	require ("commonfiles/permissions.php");

    if(!empty($_POST)) 
    { 
        $query = " 
            SELECT
                users.id, 
                users.username, 
                users.password, 
                users.salt, 
                users.email,
				users.idchapter,
				chapters.chapterdesignation,
				users.idorganization,
				organizations.organizationname,
				users.localid,
				members.externalkey AS memberexternalkey,
				chapters.externalkey AS chapterexternalkey,
				organizations.externalkey AS organizationexternalkey
            FROM users
			JOIN members
			ON(
				members.idorganization = users.idorganization
					AND
				members.idchapter = users.idchapter
					AND
				members.localid = users.localid
				)
			RIGHT JOIN chapters
			ON(
				chapters.idorganization = users.idorganization
					AND
				chapters.idchapter = users.idchapter
				)
			RIGHT JOIN organizations
			ON(
				organizations.idorganization = users.idorganization
				)
            WHERE 
                username = :username 
        "; 
         
        $query_params = array( 
            ':username' => $_POST['username'] 
        ); 
         
        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) {} 

        $login_ok = false; 

        $row = $stmt->fetch(); 
        if($row) 
        { 
            $check_password = hash('sha256', $_POST['password'] . $row['salt']); 
            for($round = 0; $round < 65536; $round++) 
            { 
                $check_password = hash('sha256', $check_password . $row['salt']); 
            } 
             
            if($check_password === $row['password']) 
            { 
                $login_ok = true; 
            } 
        } 

        if($login_ok) 
        { 
            unset($row['salt']); 
            unset($row['password']); 

            $_SESSION['user'] = $row;
		
/*----------------------------------------------------------------------Start Grabbing Officer Permissions------------------------------------------------------------*/
		$officerQuery = "
			SELECT
				pservice,
				pdirectory,
				pjudicial,
				pbilling,
				pofficerpermissions,
				pmasscommunication
			FROM
				officers
				WHERE(
					officers.idorganization = ".$_SESSION['user']['idorganization']."
						AND
					officers.idchapter = ".$_SESSION['user']['idchapter']."
						AND
					officers.localid = ".$_SESSION['user']['localid']."
					);";
	
			$stmt = $db->prepare($officerQuery); 
			$stmt->execute(); 

        $officerpermissions = $stmt->fetchAll();
		
		foreach($officerpermissions AS $officerpermission):
		{
/*--------------------------------------------------------Determine Applicable Officer Permission 'p_service'---------------------------------------------------------*/
			if(($officerpermission['pservice'] == 2 && $_SESSION['user']['pservice'] == 3) 
				|| ($officerpermission['pservice'] == 3 && $_SESSION['user']['pservice'] == 2))
			{
				$_SESSION['user']['pservice'] = 4;
			}
			elseif(($officerpermission['pservice'] == 3 && $_SESSION['user']['pservice'] == 5) 
				|| ($officerpermission['pservice'] == 5 && $_SESSION['user']['pservice'] == 3))
			{
				$_SESSION['user']['pservice'] = 6;
			}
			elseif($officerpermission['pservice'] > $_SESSION['user']['pservice'])
			{
				$_SESSION['user']['pservice'] = $officerpermission['pservice'];
			}
/*------------------------------------------------------Determine Applicable Officer Permission 'pdirectory'---------------------------------------------------------*/
			if(($officerpermission['pdirectory'] == 2 && $_SESSION['user']['pdirectory'] == 3) 
				|| ($officerpermission['pdirectory'] == 3 && $_SESSION['user']['pdirectory'] == 2))
			{
				$_SESSION['user']['pdirectory'] = 4;
			}
			elseif(($officerpermission['pdirectory'] == 3 && $_SESSION['user']['pdirectory'] == 5) 
				|| ($officerpermission['pdirectory'] == 5 && $_SESSION['user']['pdirectory'] == 3))
			{
				$_SESSION['user']['pdirectory'] = 6;
			}
			elseif($officerpermission['pdirectory'] > $_SESSION['user']['pdirectory'])
			{
				$_SESSION['user']['pdirectory'] = $officerpermission['pdirectory'];
			}
/*------------------------------------------------------Determine Applicable Officer Permission 'pjudicial'---------------------------------------------------------*/
			if(($officerpermission['pjudicial'] == 2 && $_SESSION['user']['pjudicial'] == 3) 
				|| ($officerpermission['pjudicial'] == 3 && $_SESSION['user']['pjudicial'] == 2))
			{
				$_SESSION['user']['pjudicial'] = 4;
			}
			elseif(($officerpermission['pjudicial'] == 3 && $_SESSION['user']['pjudicial'] == 5) 
				|| ($officerpermission['pjudicial'] == 5 && $_SESSION['user']['pjudicial'] == 3))
			{
				$_SESSION['user']['pjudicial'] = 6;
			}
			elseif($officerpermission['pjudicial'] > $_SESSION['user']['pjudicial'])
			{
				$_SESSION['user']['pjudicial'] = $officerpermission['pjudicial'];
			}
/*------------------------------------------------------Determine Applicable Officer Permission 'pbilling'---------------------------------------------------------*/
			if(($officerpermission['pbilling'] == 2 && $_SESSION['user']['pbilling'] == 3) 
				|| ($officerpermission['pbilling'] == 3 && $_SESSION['user']['pbilling'] == 2))
			{
				$_SESSION['user']['pbilling'] = 4;
			}
			elseif(($officerpermission['pbilling'] == 3 && $_SESSION['user']['pbilling'] == 5) 
				|| ($officerpermission['pbilling'] == 5 && $_SESSION['user']['pbilling'] == 3))
			{
				$_SESSION['user']['pbilling'] = 6;
			}
			elseif($officerpermission['pbilling'] > $_SESSION['user']['pbilling'])
			{
				$_SESSION['user']['pbilling'] = $officerpermission['pbilling'];
			}
/*--------------------------------------------------Determine Applicable Officer Permission 'pofficerpermissions'-----------------------------------------------------*/
			if(($officerpermission['pofficerpermissions'] == 2 && $_SESSION['user']['pofficerpermissions'] == 3) 
				|| ($officerpermission['pofficerpermissions'] == 3 && $_SESSION['user']['pofficerpermissions'] == 2))
			{
				$_SESSION['user']['pofficerpermissions'] = 4;
			}
			elseif(($officerpermission['pofficerpermissions'] == 3 && $_SESSION['user']['pofficerpermissions'] == 5) 
				|| ($officerpermission['pofficerpermissions'] == 5 && $_SESSION['user']['pofficerpermissions'] == 3))
			{
				$_SESSION['user']['pofficerpermissions'] = 6;
			}
			elseif($officerpermission['pofficerpermissions'] > $_SESSION['user']['pofficerpermissions'])
			{
				$_SESSION['user']['pofficerpermissions'] = $officerpermission['pofficerpermissions'];
			}
/*--------------------------------------------------Determine Applicable Officer Permission 'pmasscommunication'-----------------------------------------------------*/
			if($officerpermission['pmasscommunication'] > 0
				|| $_SESSION['user']['pmasscommunication'] > 0)
			{
				$_SESSION['user']['pmasscommunication'] = 1;
			}
		}
		endforeach;
		
		if($_SESSION['user']['pdirectory'] == null)
			$_SESSION['user']['pdirectory'] = $defaults['directory'];
		if($_SESSION['user']['pservice'] == null)
			$_SESSION['user']['pservice'] = $defaults['service'];
		if($_SESSION['user']['pjudicial'] == null)
			$_SESSION['user']['pjudicial'] = $defaults['judicial'];
		if($_SESSION['user']['pbilling'] == null)
			$_SESSION['user']['pbilling'] = $defaults['billing'];
		if($_SESSION['user']['pofficerpermissions'] == null)
			$_SESSION['user']['pofficerpermissions'] = $defaults['officerpermissions'];
		if($_SESSION['user']['pmasscommunication'] == null)
			$_SESSION['user']['pmasscommunication'] = $defaults['masscommunication'];
		

            header("Location: landing.php"); 
            die("Redirecting to: landing.php"); 
    } 
    else 
    { 
        print("<p style=\"color:white;\">Login Failed.</p>"); 
         
        $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8'); 
    }
    }  
?>
