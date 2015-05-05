<?
	require("commonfiles/common.php");
	require("commonfiles/lists.php");
	require("commonfiles/permissions.php");
	require("commonfiles/functions.php");

	if(empty($_SESSION['user'])) 
	{ 
	    // If they are not, we redirect them to the login page. 
	    header("Location: login.php"); 
     
	    // Remember that this die statement is absolutely critical.  Without it, 
	    // people can view your members-only content without logging in. 
	    die("Redirecting to login.php"); 
	} 
	
	$pagePermission = $_SESSION['user']['pservice'];
	
	if($pagePermission == 0)
	{
		header("Location: no_access.html"); 

		die("Redirecting to no_access.html"); 
	}

	if(empty($_POST))
	{}
	else
	{
		$query = "
			UPDATE 
				communityserviceevents
			SET
				serviceperformed = :serviceperformed, 
			    date = :date,
			    localid = :localid, 
				qty = :qty,
				idqtyunit = :idqtyunit, 
				description = :description,
				contactName = :contactName,
				contactInfo = :contactInfo
			WHERE idcommunityserviceevents = ".
					intval($_GET['idevent']);
		
        $query_params = array( 
			':serviceperformed' => $_POST['serviceperformed'],
			':date' => $_POST['dateperformed'],
			':localid' => $_POST['memberselector'],
			':qty' => $_POST['qtyperformed'],
			':idqtyunit' => $_POST['typeselector'],
			':description' => $_POST['description'],
			':contactName' => $_POST['contactName'],
			':contactInfo' => $_POST['contactInfo']
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
		echo '<script type="text/javascript">'
		   , 'confirm("Service Event Updated!");'
		   , '</script>';
	}
	
	$query = "
		SELECT *
		FROM communityserviceevents
		WHERE idcommunityserviceevents = ".intval($_GET['idevent'])."
		;
	";

	try 
	{ 
	    // These two statements run the query against your database table. 
	    $stmt = $db->prepare($query); 
	    $stmt->execute(); 
	} 
	catch(PDOException $ex) 
	{ 
	    die("Failed to run query: " . $ex->getMessage()); 
	} 
 
	$serviceEvent = $stmt->fetch();
	
	$readonly = 0;
	
	if($_SESSION['user']['pservice'] == 1 
		|| $_SESSION['user']['pservice'] == 2
		|| ($_SESSION['user']['pservice'] == 5 && $_SESSION['user']['localid'] == $serviceEvent['localid'])
		|| ($_SESSION['user']['pservice'] == 4 && $_SESSION['user']['localid'] != $serviceEvent['localid']))
	{
		$readonly = 1;
	}
?> 
<!doctype html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/pop-up_std.css">
		<title>Service Event Editor</title>
		<?php	require("commonfiles/header.html"); ?>
	</head>
	<div class="application">
		<form action="<?echo $_SERVER['PHP_SELF'];?>?idevent=<?echo $_GET['idevent'];?>" method="POST">
			Event ID:
				<input type="text"
					name="eventid"
					value="<?echo $_GET['idevent'];?>"
					size="5"
					disabled
					<?if($readonly)echo 'disabled'?>
				/>
			<br />
			Service Performed:
				<input type="text"
					name="serviceperformed"
					size="25"
					value="<?echo htmlentities($serviceEvent['serviceperformed'], ENT_QUOTES, 'UTF-8');?>"
					<?if($readonly)echo 'disabled'?>
					required
				/>
			<br />
			Date:
				<input type="date"
					name="dateperformed"
					value="<?echo $serviceEvent['date'];?>"
					<?if($readonly)echo 'disabled'?>
					required
				/>
			<br />
			Member Name:
			<?
				$query = "
					SELECT localid, firstname, lastname
					FROM chapters
					JOIN members
					ON members.idchapter=chapters.idchapter
					WHERE
						(
							members.idchapter = ".$_SESSION['user']['idchapter']."
								AND
							members.idorganization = ".$_SESSION['user']['idorganization']."
								".($pagePermission == 2 || $pagePermission == 1 || $pagePermission == 5 ? 'AND members.localid != '.$_SESSION['user']['localid'] : '')."
								".($pagePermission == 3 || $pagePermission == 4 ? 'AND members.localid = '.$_SESSION['user']['localid'] : '')."
						)
					ORDER BY localid ASC
						;
					";

				try 
				{ 
					// These two statements run the query against your database table. 
					$stmt = $db->prepare($query); 
					$stmt->execute(); 
				} 
				catch(PDOException $ex) 
				{ 
					// Note: On a production website, you should not output $ex->getMessage(). 
					// It may provide an attacker with helpful information about your code.  
					die("Failed to run query: " . $ex->getMessage()); 
				} 
 
				// Finally, we can retrieve all of the found rows into an array using fetchAll 
				$members = $stmt->fetchAll();
		
				if($pagePermission >= 5 && strcmp($_GET['idevent'],'(new)') == 0)
					generateMemberCheckBoxTable($members, 2);
				else
					generateMemberSelectorBox($members, $readonly, $serviceEvent['localid']);
			?>
			Quantity:
				<input type="number"
					name="qtyperformed"
					step="0.5"
					value="<?echo $serviceEvent['qty'];?>"
					<?if($readonly)echo 'disabled'?>
				/>
			<br />
			Type:
				<select name="typeselector" <?if($readonly)echo 'disabled'?>>
					<?php
						for($i = 0;$i < count($units);next($units))
						{
							if($serviceEvent['idqtyunit'] == key($units))
							{
								print("<option value=\"".key($units)."\" selected>".pos($units)."</option>");
							}
							else
							{
								print("<option value=\"".key($units)."\">".pos($units)."</option>");
							}
					
							$i++;	
						}
					?>
				</select>
			<br />
			Description:
				<textarea name="description"
					rows="4"
					cols="50"
					<?if($readonly)echo 'disabled'?>
					><?echo htmlentities($serviceEvent['description'], ENT_QUOTES, 'UTF-8');?></textarea>
			<br />
			Event Contact:
				<br />
				<input type="text" name="contactName" size="30" value="<?echo $serviceEvent['contactName'];?>" <?if($readonly)echo 'disabled'?> required/>
				<br />
			Event Contact Info: (phone or email)
				<br />
				<input type="text" name="contactInfo" size="45" value="<?echo $serviceEvent['contactInfo'];?>" <?if($readonly)echo 'disabled'?> required/>
				<br />
			<?
				if($_GET['idevent'] == '(new)')
				{
					if($pagePermission == 3 || $pagePermission == 4 || $pagePermission == 6)
					{
						print("<input type=\"submit\" value=\"Add\" />");
					}
					else
					{
						print("<input type=\"submit\" value=\"Add\" disabled/>");
					}
				}
				else
				{
					if(($pagePermission == 3 && $serviceEvent['localid'] == $_SESSION['user']['localid'])
						|| ($pagePermission == 4 && $serviceEvent['localid'] == $_SESSION['user']['localid'])
						|| ($pagePermission == 5 && $serviceEvent['localid'] != $_SESSION['user']['localid'])
						|| $pagePermission == 6)
					{
						print("<input type=\"submit\" value=\"Update\" />  ");
						print("<a href=\"delete_service_event.php?&idevent=".$serviceEvent['idcommunityserviceevents']."\">Delete Event</a>");
					}
					else
					{
						print("<input type=\"submit\" value=\"Update\" disabled/>");
					}
				}
			?>
		</div>
		</form>
	</div>
</html>