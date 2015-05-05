<?
	require("commonfiles/common.php");
	require("commonfiles/lists.php");
	require("commonfiles/permissions.php");
	require("commonfiles/functions.php");

	if(empty($_SESSION['user'])) 
	{ 
	    header("Location: login.php"); 
	    die("Redirecting to login.php"); 
	} 
	
	$pagePermission = $_SESSION['user']['pjudicial'];
	
	if($pagePermission < 5)
	{
		header("Location: no_access.html"); 

		die("Redirecting to no_access.html"); 
	}
	
	if(!empty($_POST)) 
	{
		$query =
			"
				INSERT INTO greekDB.judicialcases (localid, idchapter, idorganization, title, dateopened, details, verdict, punishmentqty, punishmenttype, dateclosed, notes)
				VALUES 
			";
			next($_POST);
		if(isset($_POST['smsAlert']))
		{
			for($i = 1; $i < count($_POST)-6; $i++)
			{
				$query .= "(".key($_POST).",".$_SESSION['user']['idchapter'].",".$_SESSION['user']['idorganization'].', :title, NOW(), :details, :verdict, :punishmentqty, :punishmenttype, NOW(), :notes),';
				next($_POST);
			}
		}
		else
		{
			for($i = 1; $i < count($_POST)-5; $i++)
			{
				$query .= "(".key($_POST).",".$_SESSION['user']['idchapter'].",".$_SESSION['user']['idorganization'].', :title, NOW(), :details, :verdict, :punishmentqty, :punishmenttype, NOW(), :notes),';
				next($_POST);
			}
		}
			
		$query = substr($query,0,-1).";";
		
		$query_params = array( 
			':title' => $_POST['title'],
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
		
		if(isset($_POST['smsAlert']))
        {
        	reset($_POST);
			next($_POST);
			
        	$query = 'SELECT phonenumber FROM greekDB.members WHERE idorganization = '
        		.$_SESSION['user']['idorganization'].' AND idchapter = '
        		.$_SESSION['user']['idchapter'].' AND (';
        		
        	for($i = 1; $i < count($_POST)-6; $i++)
			{
				$query .= "localid = ".key($_POST).' OR ';
				next($_POST);
			}
        	
        	$query = substr($query,0,-4).');';
        	
        	try 
			{ 
	    		$stmt = $db->prepare($query); 
	    		$stmt->execute(); 
			} 
			catch(PDOException $ex) 
			{  
			    die("Failed to run query: " . $ex->getMessage()); 
			} 

			$numbersToText = $stmt->fetchAll();
			
			if ($_POST['verdictselector'] == 1)
			{
				$content = "GreekDB Alert! "."A punishment has been levied by the Judicial Committee for ".$_POST['title'].". ".$_POST['qtyselector'];
        	
				if ($_POST['typeselector'] == 0)
				{
					$content.= " additional Service Hour(s) will be required.";
				}
				else if ($_POST['typeselector'] == 1)
				{
					$content.= ' Demerit(s) added.';
				}
				else if ($_POST['typeselector'] == 2)
				{
					$content.= '$ fine assessed.';
				}
				else if ($_POST['typeselector'] == 3)
				{
					$content.= ' additional DD Shift(s) will be required.';
				}
				else if ($_POST['typeselector'] == 4)
				{
					$content.= ' additional Cleaning Detail(s) will be required.';
				}
			}
			
			else if($_POST['verdictselector'] == 0)
			{
				$content = "GreekDB Alert! "."A case has been created by the Judicial Committee for ".$_POST['title'].".  Please attend the next committee meeting if you wish to appeal.";
			}

        	foreach($numbersToText as $numberToText):
			{
				sendToPhone($numberToText['phonenumber'], $content);
			}
			endforeach;
        }

		print("<script>close();</script>");
	}
?> 
<!doctype html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/pop-up_std.css">
		<title>New Judicial Case</title>
		<?php require("commonfiles/header.html"); ?>
	</head>
	<div class="application">
		<form action="<?echo $_SERVER['PHP_SELF'];?>" method="POST">
			Case ID:
				<input type="text" name="eventid" value="new" size="4" disabled/>
			<br />
			Offense:
				<input type="text" name="title" size="25"/>
			<br />
			Members:
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
								AND
							members.isAlumnus = 0
								".($pagePermission == 5 ? 'AND members.localid != '.$_SESSION['user']['localid'] : '')."
						)
					ORDER BY localid ASC;
					";

				try 
				{ 
					$stmt = $db->prepare($query); 
					$stmt->execute(); 
				} 
				catch(PDOException $ex) {} 
 
				$members = $stmt->fetchAll();
			
				generateMemberCheckBoxTable($members, 2);
			?>
			Verdict:
			<select name="verdictselector">
				<?php
					for($i = 0;$i < count($verdicts);next($verdicts))
					{
						echo '<option value="'.key($verdicts).'">'.pos($verdicts).'</option>';
						$i++;	
					}
				?>
			</select>
			<br />
			Offense Details:
				<textarea name="details" rows="4" cols="50"></textarea>
			Punishment:
				<input type="number" name="qtyselector" max="999" size="3" value="0">
				<select name="typeselector">
					<?php
						for($i = 0;$i < count($punishments);next($punishments))
						{

							echo '<option value="'.key($punishments).'">'.pos($punishments).'</option>';
							$i++;	
						}
					?>
				</select>
				<br />
			Decision Details:
				<textarea name="notes" rows="4" cols="50"></textarea>
				<br />
				<input type="submit" value="Add" style="position:fixed; left: 12px; bottom:6px;"/>
			<div class="alerts" style="position:fixed; left: 52px; bottom:8px;">
				<input type="checkbox" name="smsAlert">Text Alert
			</div>
		</form>
	</div>
<html>