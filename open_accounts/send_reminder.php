<?php
	require("../commonfiles/common.php");
	require("../commonfiles/permissions.php");
	require("../commonfiles/functions.php");
	
	if($_GET)
	{
	    if(empty($_SESSION['user'])) 
	    { 
	        header("Location: login.php"); 
	        die("Redirecting to login.php"); 
	    }
	
		$pagePermission = $_SESSION['user']['pofficerpermissions']; 
	
		if($pagePermission == 0)
		{
			header("Location: no_access.html"); 
			die("Redirecting to no_access.html"); 
		}
	
		$query = '
				SELECT localid, SUM(charges) AS totalCharges
				FROM
				(
					SELECT localid, charges
					FROM (
						SELECT amountcharged AS charges, localid
						FROM greekDB.membercharges 
						WHERE(
							idorganization = '.$_SESSION['user']['idorganization'].'
								AND
							idchapter = '.$_SESSION['user']['idchapter'].'
								AND
							localid = '.$_GET['member'].'
							)
						) AS a

					UNION

					SELECT localid, charges
					FROM (
						SELECT punishmentqty AS charges, localid
						FROM greekDB.judicialcases
						WHERE(
							idorganization = '.$_SESSION['user']['idorganization'].'
								AND
							idchapter = '.$_SESSION['user']['idchapter'].'
								AND
							localid = '.$_GET['member'].'
								AND
							punishmenttype = 2)
						) AS b
				)
				AS c
				GROUP BY localid
			';

		    try 
		    { 
		        $stmt = $db->prepare($query); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 
 
		    $dues_outstanding = $stmt->fetch();
			
			if($dues_outstanding['totalCharges'] > 0)
			{
				$query = 
					"SELECT members.phonenumber
					FROM greekDB.members
					WHERE members.idorganization = ".$_SESSION['user']['idorganization']."
						AND
							members.idchapter = ".$_SESSION['user']['idchapter']."
						AND
							members.localid = ".$_GET['member']."
					";
					
			    try 
			    { 
			        $stmt = $db->prepare($query); 
			        $stmt->execute(); 
			    } 
			    catch(PDOException $ex) 
			    { 
			        die("Failed to run query: " . $ex->getMessage()); 
			    } 

			    $numberToText = $stmt->fetch();
				$numberToText = $numberToText['phonenumber'];
				
				$content = "GreekDB Reminder! "."The treasurer of ".$_SESSION['user']['chapterdesignation']." would like to remind you that you have an outstanding balance of $".$dues_outstanding['totalCharges'].".";
    			sendToPhone($numberToText, $content);
			}
			
			echo '<script>close()</script>';
	}
?>