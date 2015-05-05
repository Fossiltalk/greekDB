<?
	require("../../commonfiles/common.php");
	require("../../commonfiles/permissions.php");
 
	if(empty($_SESSION['user'])) 
	{ 
	    header("Location: ../../login.php"); 
     
	    die("Redirecting to login.php"); 
	}

	$pagePermission = $_SESSION['user']['pbilling'];
	
	if($pagePermission  == 0 || $pagePermission  == 1 || $pagePermission  == 3)
	{
		header("Location: ../../no_access.html"); 

		die("Redirecting to no_access.html"); 
	}
	else
	{
	/*-----------------------------------------------------------------------Grab all members with outstanding balances-------------------------------------------------------*/	
		
		$query = '
			SELECT members.localid, totalCharges, CONCAT(members.firstname, " ", members.lastname) AS name, members.phonenumber
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
			ORDER BY
				localid;';
	
		    try 
		    { 
		        $stmt = $db->prepare($query); 
		        $stmt->execute(); 
		    } 
		    catch(PDOException $ex) 
		    { 
		        die("Failed to run query: " . $ex->getMessage()); 
		    } 
     
		    $dues_outstanding = $stmt->fetchAll();
	/*-----------------------------------------------------------------------Grab all Service Hour Punishments-------------------------------------------------------*/
	}

	
	$totalReceivable = 0;
	$totalPayable = 0;
?>
<!doctype html>
<title>Accounts Receivable</title>
<div align="center">
	Accounts Receivable Report<br />
	<?echo $_SESSION['user']['organizationname']." - ".$_SESSION['user']['chapterdesignation'];?>
</div>
<div align="center">
	<table class="totals">
		<tr>
			<th>Bond #</th>
			<th>Name</th>
			<th>Charges Outstanding</th>
		</tr>
    <?php foreach($dues_outstanding as $due_outstanding): 
    	if($due_outstanding['totalCharges'] > 0)
    	{
    		$totalReceivable += $due_outstanding['totalCharges'];
    	}
    	else
    	{
    		$totalPayable += -1*$due_outstanding['totalCharges'];
    	}?> 
        <tr>
			<td><?php echo $due_outstanding['localid'];?></td>
            <td><?php echo $due_outstanding['name'];?></td>  
			<td><?php echo $due_outstanding['totalCharges'];?></td>
        </tr> 
    <?php endforeach; ?>
		<tr><td colspan="3"></td></tr>
		<tr>
			<td><?php echo '$'.$totalReceivable.' receivable';?></td>
			<td><?php echo '$'.$totalPayable.' payable';?></td>  
			<td><?php echo 'Total: $'.($totalReceivable - $totalPayable);?></td>
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

.totals tr:last-child {
	background:#FFFFCC;
}
</style>