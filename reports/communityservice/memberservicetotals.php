<?
	require("../../commonfiles/common.php");
	require("../../commonfiles/permissions.php");
 
	if(empty($_SESSION['user'])) 
	{ 
	    header("Location: ../../login.php"); 
     
	    die("Redirecting to login.php"); 
	}

	$pagePermission = $_SESSION['user']['pdirectory'];
	
	if($pagePermission  == 0 || $pagePermission  == 1 || $pagePermission  == 3)
	{
		header("Location: ../../no_access.html"); 

		die("Redirecting to no_access.html"); 
	}
	else
	{
	/*-----------------------------------------------------------------------Grab all active members-------------------------------------------------------*/	
	
		$startdate = $_GET['startdate'];
		$enddate = $_GET['enddate'];
		
		$query_members = "
			SELECT members.localid, CONCAT(members.firstname, \" \", members.lastname) AS name
			FROM greekDB.members
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				members.isAlumnus = 0
			ORDER BY members.localid;";
	
		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_members); 
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
	/*-----------------------------------------------------------------------Grab all hours-------------------------------------------------------*/
		$query_hours = "
			SELECT members.localid, SUM(qty) AS total
			FROM greekDB.members
			LEFT JOIN greekDB.communityserviceevents
			ON members.localid = communityserviceevents.localid
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				members.isAlumnus = 0
					AND
				(
					communityserviceevents.idqtyunit = 0
						OR
					communityserviceevents.idqtyunit = 3
				)
					AND
				communityserviceevents.date <= '".$enddate."'
					AND
				communityserviceevents.date >= '".$startdate."'
			GROUP BY members.localid;";

		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_hours); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        // Note: On a production website, you should not output $ex->getMessage(). 
		        // It may provide an attacker with helpful information about your code.  
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 
 
		    // Finally, we can retrieve all of the found rows into an array using fetchAll 
		    $hours = $stmt->fetchAll();
		
	/*-----------------------------------------------------------------------Grab all CSE's-------------------------------------------------------*/
		$query_events = "
			SELECT members.localid, COUNT(idqtyunit) AS total
			FROM greekDB.members
			LEFT JOIN greekDB.communityserviceevents
			ON members.localid = communityserviceevents.localid
			WHERE
			members.idorganization = ".$_SESSION['user']['idorganization']."
			AND
			members.idchapter = ".$_SESSION['user']['idchapter']."
			AND
			members.isAlumnus = 0
			AND
			communityserviceevents.idqtyunit = 3
			AND
		communityserviceevents.date <= '".$enddate."'
			AND
		communityserviceevents.date >= '".$startdate."'
			GROUP BY members.localid;";

		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_events); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        // Note: On a production website, you should not output $ex->getMessage(). 
		        // It may provide an attacker with helpful information about your code.  
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 

		    // Finally, we can retrieve all of the found rows into an array using fetchAll 
		    $events = $stmt->fetchAll();
	/*-----------------------------------------------------------------------Grab all CSE's-------------------------------------------------------*/
		$query_bdonations = "
			SELECT members.localid, COUNT(idqtyunit) AS total
			FROM greekDB.members
			LEFT JOIN greekDB.communityserviceevents
			ON members.localid = communityserviceevents.localid
			WHERE
			members.idorganization = ".$_SESSION['user']['idorganization']."
			AND
			members.idchapter = ".$_SESSION['user']['idchapter']."
			AND
			members.isAlumnus = 0
			AND
			communityserviceevents.idqtyunit = 1
			AND
		communityserviceevents.date <= '".$enddate."'
			AND
		communityserviceevents.date >= '".$startdate."'
			GROUP BY members.localid;";

		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_bdonations); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        // Note: On a production website, you should not output $ex->getMessage(). 
		        // It may provide an attacker with helpful information about your code.  
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 

		    // Finally, we can retrieve all of the found rows into an array using fetchAll 
		    $bdonations = $stmt->fetchAll();
			/*-----------------------------------------------------------------------Grab all CSE's-------------------------------------------------------*/
		$query_donations = "
			SELECT members.localid, SUM(qty) AS total
			FROM greekDB.members
			LEFT JOIN greekDB.communityserviceevents
			ON members.localid = communityserviceevents.localid
			WHERE
			members.idorganization = ".$_SESSION['user']['idorganization']."
			AND
			members.idchapter = ".$_SESSION['user']['idchapter']."
			AND
			members.isAlumnus = 0
			AND
			communityserviceevents.idqtyunit = 2
			AND
		communityserviceevents.date <= '".$enddate."'
			AND
		communityserviceevents.date >= '".$startdate."'
			GROUP BY members.localid;";

		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_donations); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        // Note: On a production website, you should not output $ex->getMessage(). 
		        // It may provide an attacker with helpful information about your code.  
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 

		    // Finally, we can retrieve all of the found rows into an array using fetchAll 
		    $donations = $stmt->fetchAll();
	}
	
	$totalHours = 0;
	$totalEvents = 0;
	$totalBlood = 0;
	$totalDonations = 0;
?>
<!doctype html>
<title>Community Service <? echo '('.$startdate.' - '.$enddate.')';?></title>
<div align="center">
	Community Service Report<br />
	<?echo $_SESSION['user']['organizationname']." - ".$_SESSION['user']['chapterdesignation'];?>
</div>
<div align="center">
	<form action="<?echo $_SERVER['PHP_SELF'];?>" action="GET">
		<div align="left" style="display:inline-block;margin-bottom:1px">
			<input type="date" name="startdate" value="<?echo $startdate?>">
		</div>
		through
		<div align="right" style="display:inline-block;margin-bottom:1px">
			<input type="date" name="enddate" value="<?echo $enddate?>">
		</div>
		<input type="submit" value="reload">
	</form>
	<table class="totals">
		<tr>
			<th>Bond #</th>
			<th>Name</th>
			<th>Hours</th>
			<th>Blood Donations</th>
			<th>Donations ($USD)</th>
		</tr>
    <?php 
		$index=0;
		foreach($members as $member):
			if($index%38 == 0 && $index != 0)
			{
				echo '</table><p><!-- pagebreak --></p><table>
		<tr>
			<th>Bond #</th>
			<th>Name</th>
			<th>Hours</th>
			<th>Blood Donations</th>
			<th>Donations ($USD)</th>
		</tr>';
			}
			$index++;
		?>
		<tr>
			<td><?php echo $member['localid'];?></td>
            <td><?php echo $member['name']?></td>  
            <td><?php foreach($hours as $hour): if($hour['localid'] == $member['localid']){echo $hour['total']." Hours";  $totalHours += $hour['total'];} endforeach;?></td>
			<td><?php foreach($bdonations as $bdonation): if($bdonation['localid'] == $member['localid']){echo $bdonation['total']." Blood Donations"; $totalBlood += $bdonation['total'];} endforeach;?></td> 
			<td><?php foreach($donations as $donation): if($donation['localid'] == $member['localid']){echo "\$".$donation['total']; $totalDonations += $donation['total'];} endforeach;?></td>
        </tr> 
    <?php endforeach; ?>
    <tr style="border: 3px solid black">
		<td><b>Total:</b></td>
        <td></td>  
        <td><?php echo $totalHours." Hours";?></td>
		<td><?php echo $totalBlood." Blood Donations";?></td>
		<td><?php echo "\$".$totalDonations;?></td>
    </tr>
	</table>
</div>
</html>
<footer align="center">
	Reports by:
	<br />
	<img src="https://s3.amazonaws.com/greekdb/resources/sessionbar/icons/greekDB-logo.png" alt="greekDB" height="18" width="80">
</footer>

<style type="text/css">
table {
	border-width: 2px;
	border-spacing: 2px;
	border-style: solid;
	border-color: gray;
	border-collapse: collapse;
	width: 100%;
}
table th {
	border-width: 1px;
	padding-left: 5px;
	padding-right: 5px;
	padding-top: 2px;
	padding-bottom: 2px;
	border-style: solid;
	border-color: gray;
}
table td {
	border-width: 1px;
	padding-left: 5px;
	padding-right: 5px;
	padding-top: 2px;
	padding-bottom: 2px;
	border-style: solid;
	border-color: gray;
}

.totals tr:nth-child(odd){
	background-color: #FFFFFF;
}

.totals tr:nth-child(even){
	background-color: #DFDFDF;
}
</style>