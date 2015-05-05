<?
	require("../../commonfiles/common.php");
	require("../../commonfiles/permissions.php");
	require("../../commonfiles/lists.php");
 
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
	/*-----------------------------------------------------------------------Grab All Cases-------------------------------------------------------*/
		$startdate = $_GET['startdate'];
		$enddate = $_GET['enddate'];
		
		$query_cases = "
			SELECT judicialcases.title, judicialcases.dateopened, judicialcases.verdict, judicialcases.punishmentqty, judicialcases.punishmenttype, CONCAT(members.firstname, \" \", members.lastname) AS name
			FROM greekDB.judicialcases
			LEFT JOIN greekDB.members
			ON members.localid = judicialcases.localid
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				judicialcases.dateopened <= '".$enddate."'
					AND
				judicialcases.dateopened >= '".$startdate."'
					AND
				judicialcases.verdict >0;
			GROUP BY judicialcases.localid;";

		    try 
		    { 
		        // These two statements run the query against your database table. 
		        $stmt = $db->prepare($query_cases); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        // Note: On a production website, you should not output $ex->getMessage(). 
		        // It may provide an attacker with helpful information about your code.  
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 
 
		    // Finally, we can retrieve all of the found rows into an array using fetchAll 
		    $cases = $stmt->fetchAll();
		}
	?>
		
<!doctype html>
<title>Judicial Cases <? echo '('.$startdate.' - '.$enddate.')';?></title>
<div align="center">
	Judicial Cases Report<br />
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
			<th>Date Opened</th>
			<th>Name</th>
			<th>Offense</th>
			<th>Verdict</th>
			<th>Punishment</th>
		</tr>
    <?php foreach($cases as $case): ?> 
        <tr>
            <td><?php echo $case['dateopened'];?></td>
            <td><?php echo $case['name'];?></td>  
            <td><?php echo $case['title'];?></td>
            <td><?php echo $verdicts[$case['verdict']];?></td>
            <td><?php 
            	if($case['verdict'] > 1)
            		echo '';
    			else
            		echo $case['punishmentqty'].' ',$punishments[$case['punishmenttype']];?></td>
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