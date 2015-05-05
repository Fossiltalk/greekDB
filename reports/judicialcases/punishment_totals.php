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
	/*-----------------------------------------------------------------------Grab all Service Hour Punishments-------------------------------------------------------*/
		$query_hours = "
			SELECT members.localid, SUM(punishmentqty) AS total
			FROM greekDB.members
			LEFT JOIN greekDB.judicialcases
			ON members.localid = judicialcases.localid
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				members.isAlumnus = 0
					AND
				judicialcases.punishmenttype = 0
					AND
				judicialcases.verdict = 1
					AND
				judicialcases.dateopened <= '".$enddate."'
					AND
				judicialcases.dateopened >= '".$startdate."'
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
		
	/*-----------------------------------------------------------------------Grab all Demerits-------------------------------------------------------*/
		$query_demerits = "
			SELECT members.localid, SUM(punishmentqty) AS total
			FROM greekDB.members
			LEFT JOIN greekDB.judicialcases
			ON members.localid = judicialcases.localid
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				members.isAlumnus = 0
					AND
				judicialcases.punishmenttype = 1
					AND
				judicialcases.verdict = 1
					AND
				judicialcases.dateopened <= '".$enddate."'
					AND
				judicialcases.dateopened >= '".$startdate."'
			GROUP BY members.localid;";

		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_demerits); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        // Note: On a production website, you should not output $ex->getMessage(). 
		        // It may provide an attacker with helpful information about your code.  
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 

		    // Finally, we can retrieve all of the found rows into an array using fetchAll 
		    $demerits = $stmt->fetchAll();
	/*-----------------------------------------------------------------------Grab all Fines-------------------------------------------------------*/
		$query_fines = "
			SELECT members.localid, SUM(punishmentqty) AS total
			FROM greekDB.members
			LEFT JOIN greekDB.judicialcases
			ON members.localid = judicialcases.localid
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				members.isAlumnus = 0
					AND
				judicialcases.punishmenttype = 2
					AND
				judicialcases.verdict = 1
					AND
				judicialcases.dateopened <= '".$enddate."'
					AND
				judicialcases.dateopened >= '".$startdate."'
			GROUP BY members.localid;";

		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_fines); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        // Note: On a production website, you should not output $ex->getMessage(). 
		        // It may provide an attacker with helpful information about your code.  
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 

		    // Finally, we can retrieve all of the found rows into an array using fetchAll 
		    $fines = $stmt->fetchAll();
			/*-----------------------------------------------------------------------Grab all DD Shift Punishments-------------------------------------------------------*/
		$query_dds = "
			SELECT members.localid, SUM(punishmentqty) AS total
			FROM greekDB.members
			LEFT JOIN greekDB.judicialcases
			ON members.localid = judicialcases.localid
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				members.isAlumnus = 0
					AND
				judicialcases.punishmenttype = 3
					AND
				judicialcases.verdict = 1
					AND
				judicialcases.dateopened <= '".$enddate."'
					AND
				judicialcases.dateopened >= '".$startdate."'
			GROUP BY members.localid;";

		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_dds); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        // Note: On a production website, you should not output $ex->getMessage(). 
		        // It may provide an attacker with helpful information about your code.  
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 

		    // Finally, we can retrieve all of the found rows into an array using fetchAll 
		    $dds = $stmt->fetchAll();
	
				/*-----------------------------------------------------------------------Grab all Detail Punishments-------------------------------------------------------*/
		$query_details = "
			SELECT members.localid, SUM(punishmentqty) AS total
			FROM greekDB.members
			LEFT JOIN greekDB.judicialcases
			ON members.localid = judicialcases.localid
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				members.isAlumnus = 0
					AND
				judicialcases.punishmenttype = 4
					AND
				judicialcases.verdict = 1
					AND
				judicialcases.dateopened <= '".$enddate."'
					AND
				judicialcases.dateopened >= '".$startdate."'
			GROUP BY members.localid;";

		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_details); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        // Note: On a production website, you should not output $ex->getMessage(). 
		        // It may provide an attacker with helpful information about your code.  
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 

		    // Finally, we can retrieve all of the found rows into an array using fetchAll 
		    $details = $stmt->fetchAll();
	}

	
	$totalHours = 0; //0
	$totalDemerits = 0; //1
	$totalFines = 0; //2
	$totalDDs = 0; //3
	$totalDetails = 0; //4
?>
<!doctype html>
<title>Punishments Levied <? echo '('.$startdate.' - '.$enddate.')';?></title>
<div align="center">
	Judicial Punishments Report<br />
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
			<th>Demerits</th>
			<th>Fines</th>
			<th>DD Shifts</th>
			<th>Extra Details</th>
		</tr>
    <?php foreach($members as $member): ?> 
        <tr>
			<td><?php echo $member['localid'];?></td>
            <td><?php echo $member['name']?></td>  
            <td><?php foreach($hours as $hour): if($hour['localid'] == $member['localid']){echo $hour['total']." Hours";  $totalHours += $hour['total'];} endforeach;?></td>
			<td><?php foreach($demerits as $demerit): if($demerit['localid'] == $member['localid']){echo $demerit['total']." Demerits"; $totalDemerits += $demerit['total'];} endforeach;?></td>
			<td><?php foreach($fines as $fine): if($fine['localid'] == $member['localid']){echo "\$".$fine['total']; $totalFines += $fine['total'];} endforeach;?></td> 
			<td><?php foreach($dds as $dd): if($dd['localid'] == $member['localid']){echo $dd['total']." Shifts"; $totalDDs += $dd['total'];} endforeach;?></td>
			<td><?php foreach($details as $detail): if($detail['localid'] == $member['localid']){echo $detail['total']." Details";  $totalDetails += $detail['total'];} endforeach;?></td>
        </tr> 
    <?php endforeach; ?>
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