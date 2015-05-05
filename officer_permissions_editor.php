<?
	require("commonfiles/common.php");
	require("commonfiles/permissions.php");
	
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
	
	if(empty($_POST))
	{
		$query = "
			SELECT *
			FROM officers
			WHERE
			(
				officers.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				officers.idchapter = ".$_SESSION['user']['idchapter']."
					AND 
				officers.idofficers = ".intval($_GET['officer'])."
			);";
		
			try 
			{ 
				$stmt = $db->prepare($query); 
				$stmt->execute(); 
			} 
			catch(PDOException $ex) 
			{} 
         
			$row = $stmt->fetch();
		
			if(empty($row))
			{
				header("Location: no_access.html"); 
				die("Redirecting to no_access.html"); 
			}
		
	
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
	    		$stmt = $db->prepare($query); 
	    		$stmt->execute(); 
			} 
			catch(PDOException $ex) 
			{} 

			$members = $stmt->fetchAll();
	}
	else
	{
		$query=" 
	        UPDATE greekDB.officers	
			SET
				localid = :localid,
				title = :title,
	            pservice = :pservice,
				pdirectory = :pdirectory,
	            pjudicial = :pjudicial,
				pbilling = :pbilling,
				pofficerpermissions = :pofficerpermissions
			WHERE
			(
				officers.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				officers.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				officers.idofficers = ".intval($_GET['officer'])."
			);";

        $query_params = array( 
			':localid' => $_POST['memberselector'],
			':title' => $_POST['title'],
			':pservice' => $_POST['communityserviceselector'],
			':pdirectory' => $_POST['memberinfoselector'],
			':pjudicial' => $_POST['judicialcasesselector'],
			':pbilling' => $_POST['billingselector'],
			':pofficerpermissions' => $_POST['officerpermissionsselector']
        );

        try 
        { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        {}

	    header("Location: officer_permissions_editor.php?officer=".intval ($_GET['officer'])); 
		die("Redirecting to officer_permissions_editor.php?officer=".intval ($_GET['officer']));	
	}
?>

<!doctype html>
	<head>
		<meta charset="utf-8">
		<title><?echo $row['title'];?></title>
		<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/pop-up_std.css">
	</head>
	<div class="application">
		<h1><?echo $row['title'];?></h1>
		<form action="<?=$_SERVER['PHP_SELF']."?officer=".$_GET['officer']?>" method="POST">
			Title:
					<input type="text"
						name="title"
						value="<?php echo $row['title']; ?>"
						size="30"
						/>
				<br />
			Member:
				<select name="memberselector">
					<option value=""></option>
					<?
						foreach($members AS $member):
							if(intval($member['localid']) == intval($row['localid']))
							{
								echo '<option value="'.$member['localid'].'" selected>';
								echo $member['localid']." - ".$member['lastname'].", ".$member['firstname'];
								echo '</option>';
							}
							else
							{
								echo '<option value="'.$member['localid'].'">';
								echo $member['localid']." - ".$member['lastname'].", ".$member['firstname'];
								echo '</option>';
							}
						endforeach; 
					?>
				</select>
			<br />
			Community Service:
				<select name="communityserviceselector">
					<option value=""></option>
					<?php
						reset($descriptions);
				
						for($i = 1;$i <= count($descriptions);next($descriptions))
						{
							if($row['pservice'] == $i)
							{
								print("<option value=\"".$i."\" selected>".pos($descriptions)."</option>");
							}
							else
							{
								print("<option value=\"".$i."\">".pos($descriptions)."</option>");
							}
		
							$i++;
						}
					?>
				</select>
			<br />
			Member Information:
				<select name="memberinfoselector">
					<option value=""></option>
					<?php
						reset($descriptions);
			
						for($i = 1;$i <= count($descriptions);next($descriptions))
						{
							if($row['pdirectory'] == $i)
							{
								print("<option value=\"".$i."\" selected>".pos($descriptions)."</option>");
							}
							else
							{
								print("<option value=\"".$i."\">".pos($descriptions)."</option>");
							}
		
							$i++;
						}
					?>
				</select>
			<br />
			Judicial Cases:
				<select name="judicialcasesselector">
					<option value=""></option>
					<?php
						reset($descriptions);
			
						for($i = 1;$i <= count($descriptions);next($descriptions))
						{
							if($row['pjudicial'] == $i)
							{
								print("<option value=\"".$i."\" selected>".pos($descriptions)."</option>");
							}
							else
							{
								print("<option value=\"".$i."\">".pos($descriptions)."</option>");
							}
		
							$i++;
						}
					?>
				</select>
			<br />
			Billing Information:
				<select name="billingselector">
					<option value=""></option>
					<?php
						reset($descriptions);
			
						for($i = 1;$i <= count($descriptions);next($descriptions))
						{
							if($row['pbilling'] == $i)
							{
								print("<option value=\"".$i."\" selected>".pos($descriptions)."</option>");
							}
							else
							{
								print("<option value=\"".$i."\">".pos($descriptions)."</option>");
							}
		
							$i++;
						}
					?>
				</select>
			<br />
			Officer Permissions Management:
				<select name="officerpermissionsselector">
					<option value=""></option>
					<?php
						reset($descriptions);
			
						for($i = 1;$i <= count($descriptions);next($descriptions))
						{
							if($row['pofficerpermissions'] == $i)
							{
								print("<option value=\"".$i."\" selected>".pos($descriptions)."</option>");
							}
							else
							{
								print("<option value=\"".$i."\">".pos($descriptions)."</option>");
							}
		
							$i++;
						}
					?>
				</select>
			<br />
			<input type="submit" value="Update Officer" />
		</form>
	</div>
</html>