<?
	if($_SESSION['user']['pbilling'] > 0)
	{
/*--------------------------------------------------------------Gather Charges----------------------------------------------------------------*/
		$query = "
			SELECT SUM(amountcharged) AS totalCharges 
			FROM greekDB.membercharges
			WHERE
			(
				membercharges.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				membercharges.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				membercharges.localid = ".$_SESSION['user']['localid']."
			)
			;"
		;	
		try 
		{ 
			$stmt = $db->prepare($query); 
			$stmt->execute(); 
		} 
		catch(PDOException $ex) 
		{ 
		 	die("Failed to run query: " . $ex->getMessage()); 
		} 

		$totalCharges = $stmt->fetch();
		
/*--------------------------------------------------------------Gather Fines Info----------------------------------------------------------------*/
	$query = "
			SELECT SUM(punishmentqty) AS totalFines 
			FROM greekDB.judicialcases
			WHERE(
				judicialcases.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				judicialcases.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				judicialcases.localid = ".$_SESSION['user']['localid']."
					AND
				judicialcases.punishmenttype = 2)
			;"
		;
		
		// punismenttype 2 => Fines in lists.php
		
		try 
		{ 
			$stmt = $db->prepare($query); 
			$stmt->execute(); 
		} 
		catch(PDOException $ex) 
		{ 
		 	die("Failed to run query: " . $ex->getMessage()); 
		}

		$totalFines = $stmt->fetch();
	}
?>
<!----------------------------------------------------------------Print Data-------------------------------------------------------------------->	
<div class="treasuryblock">
	<h3>Billing</h3>
		<?php
			$balance = $totalCharges['totalCharges'] + $totalFines['totalFines'];
				
			if($balance > 0.0)
			{
				echo("Still Owed: $".number_format($balance,2,'.',','));
			}
			else
			{
				echo("Balance: $".number_format($balance * -1,2,'.',','));
			}
		?>
</div>

<style>
	div.treasuryblock
	{
		width:33%;
		float:left;
	}
	
	div.treasuryblock h3
	{
		margin: 0px 0px 0px 0px;
	}
</style>