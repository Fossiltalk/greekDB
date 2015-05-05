<?
	require("commonfiles/common.php");
	require("commonfiles/permissions.php");
	require("commonfiles/functions.php");
	
	if(empty($_SESSION['user'])) 
	{ 
	    header("Location: login.php"); 
	    die("Redirecting to login.php"); 
	}
	
	$pagePermission = $_SESSION['user']['pbilling'];
	
	if($pagePermission < 5)
	{
		header("Location: no_access.html"); 
		die("Redirecting to no_access.html"); 
	}
	
	if(!empty($_POST))
	{
		$query =
			"
				INSERT INTO greekDB.membercharges (localid, idchapter, idorganization, amountcharged, timestamp)
				VALUES 
			";
		if(isset($_POST['smsAlert']))
		{
			if($_POST['adjustmenttype'] == 0)
			{
				next($_POST);
				next($_POST);
				for($i = 2; $i < count($_POST)-1; $i++)
				{
					$query .= "(".key($_POST).",".$_SESSION['user']['idchapter'].",".$_SESSION['user']['idorganization'].",".$_POST['amount'].",NOW()),";
					next($_POST);
				}
			}
			elseif($_POST['adjustmenttype'] == 1)
			{
				next($_POST);
				next($_POST);
				for($i = 2; $i < count($_POST)-1; $i++)
				{
					$query .= "(".key($_POST).",".$_SESSION['user']['idchapter'].",".$_SESSION['user']['idorganization'].",".-1*$_POST['amount'].",NOW()),";
					next($_POST);
				}
			}
		}
		else
		{
			if($_POST['adjustmenttype'] == 0)
			{
				next($_POST);
				next($_POST);
				for($i = 2; $i < count($_POST); $i++)
				{
					$query .= "(".key($_POST).",".$_SESSION['user']['idchapter'].",".$_SESSION['user']['idorganization'].",".$_POST['amount'].",NOW()),";
					next($_POST);
				}
			}
			elseif($_POST['adjustmenttype'] == 1)
			{
				next($_POST);
				next($_POST);
				for($i = 2; $i < count($_POST); $i++)
				{
					$query .= "(".key($_POST).",".$_SESSION['user']['idchapter'].",".$_SESSION['user']['idorganization'].",".-1*$_POST['amount'].",NOW()),";
					next($_POST);
				}
			}
		}
		
		$query = substr($query,0,-1).";";
		
        try 
        { 
            $stmt = $db->prepare($query);
			$result = $stmt->execute();
        } 
        catch(PDOException $ex) 
        { 
            die("Failed to run query: " . $ex->getMessage()); 
        }

        if(isset($_POST['smsAlert']))
        {
        	reset($_POST);
        	next($_POST);
			next($_POST);
			
        	$query = 'SELECT phonenumber FROM greekDB.members WHERE idorganization = '
        		.$_SESSION['user']['idorganization'].' AND idchapter = '
        		.$_SESSION['user']['idchapter'].' AND (';
        		
        	for($i = 2; $i < count($_POST)-1; $i++)
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
			if ($_POST['adjustmenttype'] == 1)
			{
				foreach($numbersToText as $numberToText):
				{
        			$content = "GreekDB Alert! "."A payment of $".$_POST['amount']." has been made to ".$_SESSION['user']['chapterdesignation'];
        			sendToPhone($numberToText['phonenumber'], $content);
        		}
        		endforeach;
        	}
        	else if ($_POST['adjustmenttype'] == 0)
        	{
        		foreach($numbersToText as $numberToText):
				{
        			$content = "GreekDB Alert! "."A charge of $"
        			.$_POST['amount']." has been added to your account.";
        			sendToPhone($numberToText['phonenumber'], $content);
        		}
        		endforeach;
        	}
        }
	}
?>
<!doctype html>
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/pop-up_std.css">
		<title>Easy-Biller</title>
	</head>
	
	<div class="application">
		<h1>Easy-Biller</h1>
		<form action="<?echo $_SERVER['PHP_SELF'];?>" method="POST">
			<b>Adjustment Type:</b>
			<select name="adjustmenttype">
				<option value="0">Charge</option>
				<option value="1">Payment</option>
			</select>
			<br />
			<b>Amount:</b>
			<input type="number" name="amount" min="0.00" max="9999.99" step=".01">
			<br>
			<b>Adjusted Members:</b>
				<?
				$query = '
					SELECT members.localid, members.firstname, members.lastname
					FROM
					(	
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
									)
								) AS a

							UNION ALL

							SELECT localid, charges
							FROM (
								SELECT punishmentqty AS charges, localid
								FROM greekDB.judicialcases
								WHERE(
									idorganization = '.$_SESSION['user']['idorganization'].'
										AND
									idchapter = '.$_SESSION['user']['idchapter'].'
										AND
									punishmenttype = 2)
								) AS b
						)
						AS c
						GROUP BY localid
					) AS d
			
					LEFT JOIN greekDB.members
						ON d.localid=members.localid
					WHERE
						idorganization = '.$_SESSION['user']['idorganization'].'
							AND
						idchapter = '.$_SESSION['user']['idchapter'].'
							AND
						totalCharges != 0
					
				UNION
				
						SELECT members.localid, members.firstname, members.lastname
						FROM greekDB.members
						WHERE members.isAlumnus = 0	
					
					ORDER BY
						localid;';

				try 
				{ 
					$stmt = $db->prepare($query); 
					$stmt->execute(); 
				} 
				catch(PDOException $ex) {} 
 
				$members = $stmt->fetchAll();
			
				generateMemberCheckBoxTable($members, 4);
			?>
			<input type="submit" style="position: fixed; bottom:6px;">
			<div class="alerts" style="position:fixed; left: 70px; bottom:8px;">
				<input type="checkbox" name="smsAlert">Text Alert
			</div>
		</form>
	</div>
</html>

<style>
div.memberCheckTable
{
	height: 170px;
}
</style>