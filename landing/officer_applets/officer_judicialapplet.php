<?
	if($_SESSION['user']['pjudicial'] > 4)
	{
		$query = "SELECT COUNT(*) AS opencaseqty
					FROM greekDB.judicialcases
					WHERE(
							judicialcases.idorganization = ".$_SESSION['user']['idorganization']."
								AND
							judicialcases.idchapter = ".$_SESSION['user']['idchapter']."
								AND
							verdict = 0
								AND
							dateOpened >= NOW() - INTERVAL 4 MONTH
						)
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
		$opencases = $stmt->fetch();
		
		$query = "SELECT lastname, COUNT(*) AS totalCases
					FROM greekDB.judicialcases
					LEFT JOIN members
					ON judicialcases.localid = members.localid
					WHERE(
						judicialcases.idorganization = ".$_SESSION['user']['idorganization']."
							AND
						judicialcases.idchapter = ".$_SESSION['user']['idchapter']."
							AND
						verdict = 1
							AND
						dateclosed >= CURDATE() - INTERVAL 4 MONTH
							AND
						members.isAlumnus = 0
						)
					GROUP BY judicialcases.localid
					HAVING totalCases > 0
					ORDER BY RAND()
					LIMIT 4;";
		try 
		{ 
			$stmt = $db->prepare($query); 
			$stmt->execute(); 
		} 
		catch(PDOException $ex) 
		{ 
		 	die("Failed to run query: " . $ex->getMessage()); 
		} 
		$topOffenders = $stmt->fetchAll();
		
/*----------------------------------------------------------------------------  ----------------------------------------------------------------------------*/
	}
?>
<div class="standardsblock">
	<h3>Standards</h3>
	<?echo $opencases['opencaseqty']." Cases Pending\n";?>
	<?echo "<br />------------------<br />";?>
	<?
		foreach($topOffenders as $topOffender):
			echo $topOffender['lastname']." - ".$topOffender['totalCases']." total cases"."<br />";
		endforeach;
	?>
</div>

<style>
	div.standardsblock
	{
		width:33%;
		float:left;
	}
</style>