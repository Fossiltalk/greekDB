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
	
	$pagePermission = $_SESSION['user']['pservice'];

	if($pagePermission < 3)
	{
		header("Location: no_access.html"); 

		die("Redirecting to no_access.html"); 
	}
	
	if(!empty($_POST)) 
	{ 	
		$query =
			"
				INSERT INTO greekDB.communityserviceevents (localid, idchapter, idorganization, serviceperformed, date, qty, idqtyunit, description, contactName, contactInfo, submitted)
				VALUES 
			";
			next($_POST);
			next($_POST);
			for($i = 2; $i < count($_POST)-5; $i++)
			{
				$query .= "(".key($_POST).",".$_SESSION['user']['idchapter'].",".$_SESSION['user']['idorganization'].', :serviceperformed, :date, :qty, :idqtyunit, :description, :contactName, :contactInfo, NOW()),';
				next($_POST);
			}
			
			$query = substr($query,0,-1).";";
		    $query_params = array( 
				':serviceperformed' => $_POST['serviceperformed'],
				':date' => $_POST['dateperformed'],
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
		catch(PDOException $ex) {}
			
		echo '<script>close()</script>';
	}
	
?> 
<!doctype html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/pop-up_std.css">
		<title>New Service Event!</title>
		<?php require("commonfiles/header.html"); ?>
	</head>
	<div class="application">
		<form action="<?echo $_SERVER['PHP_SELF'];?>" method="POST">
			Event ID:
				<input type="text" name="eventid" value="(new)" size="5" disabled />
			<br />
			Service Performed:
				<input type="text" name="serviceperformed" size="25" required/>
			<br />
			Date:
				<input type="date" name="dateperformed" value="<?php echo date('Y-m-d'); ?>" required/>
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
								".($pagePermission == 5 ? 'AND members.localid != '.$_SESSION['user']['localid'] : '')
								.($pagePermission == 3 ? 'AND members.localid = '.$_SESSION['user']['localid'] : '')
								.($pagePermission == 4 ? 'AND members.localid = '.$_SESSION['user']['localid'] : '')."
						)
					ORDER BY members.localid ASC;
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
			Quantity:
				<input type="number" name="qtyperformed" step="0.5" />
			<br />
			Type:
				<select name="typeselector">
					<option></option>
					<?php
						for($i = 0;$i < count($units);next($units))
						{
							echo '<option value="'.key($units).'">'.pos($units).'</option>';
							$i++;	
						}
					?>
				</select>
			<br />
			Description:
				<textarea name="description" rows="4" cols="50"></textarea>
			<br />
			Event Contact:
				<br />
				<input type="text" name="contactName" size="30" required/>
				<br />
			Event Contact Info: (phone or email)
				<br />
				<input type="text" name="contactInfo" size="45" required/>
				<br />
			<input type="submit" value="Add" />
		</div>
		</form>
	</div>
</html>