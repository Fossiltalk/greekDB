<?
	require("commonfiles/lists.php");
	
	if($_SESSION['user']['pservice'] > 4)
	{
		$query = "SELECT idqtyunit, SUM(qty) AS serviceqty
				FROM greekDB.communityserviceevents
				WHERE(
					communityserviceevents.idorganization = ".$_SESSION['user']['idorganization']."
						AND
					communityserviceevents.idchapter = ".$_SESSION['user']['idchapter']."
						AND
					date >= NOW() - INTERVAL 1 YEAR
				)
				GROUP BY communityserviceevents.idqtyunit
			;";

		try 
		{ 
			$stmt = $db->prepare($query); 
			$stmt->execute(); 
		} 
		catch(PDOException $ex) 
		{ 
		 	die("Failed to run query: " . $ex->getMessage()); 
		} 
		$serviceTotals = $stmt->fetchAll();
	}
?>
<div class="communityserviceblock">
	<h3>Community Service</h3>
	Service Totals
	<br />
	------------------
	<?
		foreach($serviceTotals as $serviceTotal):
			echo ("<br />".$serviceTotal['serviceqty']." ".$units[$serviceTotal['idqtyunit']]);
		endforeach;
	?>
</div>

<style>
	div.serviceblock
	{
		width:33%;
		float:left;
	}
</style>