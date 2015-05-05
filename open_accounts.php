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
			totalCharges > 0
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

	
		$totalReceivable = 0;
		$totalPayable = 0;
?>

<!doctype html class="grad">
	<head>
		<meta charset="utf-8">
		<title>Open Accounts</title>
	</head>
	<?php   require("commonfiles/globalnavigationbar.php"); ?>
	<div id="content">
		<aside>
			<?
				require ("commonfiles/navigationbanner.php");
			?>
		</aside>
		<div class="application">
			<h1>Balance-Holding Members</h1>
			<table class="totals">
				<tr>
					<th>Remind</th>
					<th>Name</th>
					<th class="bar">Charges Outstanding</th>
				</tr>
		    <?php foreach($dues_outstanding as $due_outstanding): 
		    	if($due_outstanding['totalCharges'] > 0)
		    	{
		    		$totalReceivable += $due_outstanding['totalCharges'];
		    	}?> 
		        <tr>
					<td style="text-align:center;"><a href="javascript:textReminder(<?echo $due_outstanding['localid'];?>)">Text</a></td>
		            <td><?php echo $due_outstanding['name'];?></td>  
					<td class="bar"><?php echo $due_outstanding['totalCharges'];?></td>
		        </tr> 
		    <?php endforeach; ?>
				<tr><td colspan="3"></td></tr>
				<tr>
					<td colspan="2" style="text-align: right;"><b><?php echo count($dues_outstanding).' Open Accounts';?></b></td>  
					<td class="bar"><b><?php echo 'Total: $'.($totalReceivable - $totalPayable);?></b></td>
				</tr>
			</table>
		</div>
	</div>
	<?php require("commonfiles/footer.html");?>	
</html>

												<!--------------------Page CSS Begins Here-------------------------->
<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/2panel.css">

<style>
	html{
		background-image: -webkit-gradient(
			linear,
			left top,
			left bottom,
			color-stop(0, #002B54),
			color-stop(1, #0081C6)
		);
		background-image: -o-linear-gradient(bottom, #002B54 0%, #0081C6 100%);
		background-image: -moz-linear-gradient(bottom, #002B54 0%, #0081C6 100%);
		background-image: -webkit-linear-gradient(bottom, #002B54 0%, #0081C6 100%);
		background-image: -ms-linear-gradient(bottom, #002B54 0%, #0081C6 100%);
		background-image: linear-gradient(to bottom, #002B54 0%, #0081C6 100%);
	}

	.application h1{
		margin-top: 2px;
		margin-bottom: 8px;
	}
	
	table {
	    border-collapse: collapse;
	}
	
	.bar{
		border-left: 1px solid gray;
	}
	
	.totals tr:nth-child(odd){
		background-color: #FFFFFF;
	}

	.totals tr:nth-child(even){
		background-color: #DFDFDF;
	}

	.totals tr:last-child {
		background:#FFFFCC;
		border-bottom:none;
	}
	
	td {
		padding-left: 8px;
		padding-right: 8px;
	}
</style>

<script>
	function textReminder(localid)
	{
	    window.open("open_accounts/send_reminder.php?type=text&member="+localid,
			"_blank",
			"toolbar=no, scrollbars=no, top=0, left=0, width=0, height=0");
	}
</script>