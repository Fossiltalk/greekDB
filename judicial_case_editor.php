<?
	require("commonfiles/common.php");
	require("commonfiles/lists.php");
	require("commonfiles/permissions.php");

	if(empty($_SESSION['user'])) 
	{ 
	    // If they are not, we redirect them to the login page. 
	    header("Location: login.php"); 
     
	    // Remember that this die statement is absolutely critical.  Without it, 
	    // people can view your members-only content without logging in. 
	    die("Redirecting to login.php"); 
	} 
	
	$pagePermission = $_SESSION['user']['pjudicial'];
	
	if($pagePermission == 0)
	{
		header("Location: no_access.html");
		die("Redirecting to no_access.html"); 
	}

	$query = "
		SELECT *
		FROM judicialcases
		WHERE idjudicialcases = ".intval($_GET['idjudicialcases'])."
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
	$judicialCase = $stmt->fetch();
	
	if($judicialCase['idchapter'] != $_SESSION['user']['idchapter']
		|| $judicialCase['idorganization'] != $_SESSION['user']['idorganization'])
	{
		header("Location: no_access.html"); 
		die("Redirecting to no_access.html"); 
	}
	
	if(!empty($_POST)) 
	{ 
        $query = " 
            UPDATE judicialcases
			SET
				title = :title,
				localid = :localid,
				idchapter = :idchapter,
				idorganization = :idorganization,
				details = :details,
				verdict = :verdict,
				punishmentqty = :punishmentqty,
				punishmenttype = :punishmenttype,
				dateclosed = NOW(),
				notes = :notes
			WHERE idjudicialcases = ".intval($_GET['idjudicialcases']); 
		
        $query_params = array( 
			':title' => $_POST['title'],
			':localid' => $_POST['memberselector'],
			':idchapter' => $_SESSION['user']['idchapter'],
			':idorganization' => $_SESSION['user']['idorganization'],
			':details' => $_POST['details'],
			':verdict' => $_POST['verdictselector'],
			':punishmentqty' => $_POST['qtyselector'],
			':punishmenttype' => $_POST['typeselector'],
			':notes' => $_POST['notes']
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
		   , 'confirm("Updated Judicial Case!");'
		   , '</script>';
	}
	
	if(($pagePermission == 1 || $pagePermission == 3) && $_SESSION['user']['localid'] != $judicialCase['localid'])
	{
		header("Location: no_access.html"); 
		die("Redirecting to no_access.html"); 
	}
	
	$readonly = 0;
	
	if($_SESSION['user']['pjudicial'] == 1 
		|| $_SESSION['user']['pjudicial'] == 2
		|| ($_SESSION['user']['pjudicial'] == 5 && $_SESSION['user']['localid'] == $judicialCase['localid'])
		|| ($_SESSION['user']['pjudicial'] == 4 && $_SESSION['user']['localid'] != $judicialCase['localid']))
	{
		$readonly = 1;
	}
?> 
<!doctype html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/pop-up_std.css">
		<?
			if($_GET['idjudicialcases'] == "(new)")
			{
				print("<title>New Judicial Case</title>");
			}
			else
			{
				print("<title>Case Editor</title>");
			}
		?>
		<?php	require("commonfiles/header.html"); ?>
	</head>
	<div class="application">
		<form action="<?echo $_SERVER['PHP_SELF'];?>?idjudicialcases=<?echo $_GET['idjudicialcases'];?>" method="POST">
			Case ID:
				<input type="text"
					name="eventid"
					value="<?echo $_GET['idjudicialcases'];?>"
					size="4"
					disabled
				/>
			<br />
			Offense:
				<input type="text"
					name="title"
					size="25"
					value="<?echo htmlentities($judicialCase['title'], ENT_QUOTES, 'UTF-8');?>"
					<?if($readonly)echo 'disabled'?>
				/>
			<br />
			Date Opened:
				<input type="date"
					name="dateopened"
					value="<?echo $judicialCase['dateopened'];?>"
					size="10"
					disabled
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
							)
						ORDER BY localid DESC
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
				?>
				<select name="memberselector" <?if($readonly)echo 'disabled'?>>
					<?
						foreach($members AS $member):
							if(intval($member['localid']) == intval($judicialCase['localid']))
							{
								print("<option value=\"".$member['localid']."\" selected>");
								echo htmlentities($member['localid']." - ".$member['lastname'].", ".$member['firstname'], ENT_QUOTES, 'UTF-8');
								print("</option>");
							}
							elseif($_GET['idevent'] == '(new)' && intval($member['localid']) == intval($_SESSION['user']['localid']))
							{
								print("<option value=\"".$member['localid']."\" selected>");
								echo htmlentities($member['localid']." - ".$member['lastname'].", ".$member['firstname'], ENT_QUOTES, 'UTF-8');
								print("</option>");
							}
							else
							{
								print("<option value=\"".$member['localid']."\">");
								echo htmlentities($member['localid']." - ".$member['lastname'].", ".$member['firstname'], ENT_QUOTES, 'UTF-8');
								print("</option>");
							}
						endforeach; 
					?>
				</select>
			<br />
			Verdict:
				<select name="verdictselector" <?if($readonly)echo 'disabled'?>>
					<?php
						for($i = 0;$i < count($verdicts);next($verdicts))
						{
							if($judicialCase['verdict'] == key($verdicts))
							{
								print("<option value=\"".key($verdicts)."\" selected>".pos($verdicts)."</option>");
							}
							else
							{
								print("<option value=\"".key($verdicts)."\">".pos($verdicts)."</option>");
							}
					
							$i++;	
						}
					?>
				</select>
			<br />
			Offense Details:
				<textarea name="details"
					rows="4"
					cols="50"
					<?if($readonly)echo 'disabled'?>><?echo htmlentities($judicialCase['details'], ENT_QUOTES, 'UTF-8');?></textarea>
			<br />
			Punishment:
				<input type="number" name="qtyselector" max="999" maxlength="3" value="<?echo $judicialCase['punishmentqty'];?>" size="3" <?if($readonly)echo 'disabled'?>>
				<select name="typeselector" <?if($readonly)echo 'disabled'?>>
					<?php
						for($i = 0;$i < count($punishments);next($punishments))
						{
							if($judicialCase['punishmenttype'] == key($punishments))
							{
								print("<option value=\"".key($punishments)."\" selected>".pos($punishments)."</option>");
							}
							else
							{
								print("<option value=\"".key($punishments)."\">".pos($punishments)."</option>");
							}
				
							$i++;	
						}
					?>
				</select>
				<br />
				Last Updated:
					<input type="date"
						name="dateclosed"
						value="<?echo $judicialCase['dateclosed'];?>"
						size="10"
						disabled
					/>
				<br />
				Decision Details:
					<textarea name="notes"
						rows="4"
						cols="50"
						<?if($readonly)echo 'disabled'?>
						><?echo htmlentities($judicialCase['notes'], ENT_QUOTES, 'UTF-8');?></textarea>
				<br />
			<?
				if($_GET['idjudicialcases'] == '(new)')
				{
					if($pagePermission == 3 || $pagePermission == 4 || $pagePermission == 5 || $pagePermission == 6)
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
					if(($pagePermission == 3 || $pagePermission == 4) && $judicialCase['localid'] == $_SESSION['user']['localid']
						|| $pagePermission == 6
						|| $pagePermission == 5 && $judicialCase['localid'] != $_SESSION['user']['localid'])
					{
						print("<input type=\"submit\" value=\"Update\" />");
					}
					else
					{
						print("<input type=\"submit\" value=\"Update\" disabled/>");
					}
				}
			?>
		</form>
	</div>
<html>