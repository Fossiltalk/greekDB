<?
	if($_SESSION['user']['pjudicial'] > 0)
	{
		$query = "SELECT SUM(punishmentqty) 
					AS punishmentqty, punishmenttype
					FROM judicialcases
					WHERE (
						idorganization = ".$_SESSION['user']['idorganization']."
							AND
						idchapter = ".$_SESSION['user']['idchapter']."
							AND
						localid = ".$_SESSION['user']['localid']."
							AND
						verdict = 1
							AND
						punishmentqty > 0
							AND
						dateOpened > NOW() - INTERVAL 4 MONTH
					)
					GROUP BY punishmenttype
					ORDER BY punishmenttype;";

		try 
		{ 
			$stmt = $db->prepare($query); 
			$stmt->execute(); 
		} 
		catch(PDOException $ex) 
		{ 
		 	die("Failed to run query: " . $ex->getMessage()); 
		} 

		$standardsTotals = $stmt->fetchAll();
	}
?>
<div class="standardsblock">
	<h3>Standards</h3>
		<table>
			<?foreach($standardsTotals as $total):?>
				<tr>
					<td>
						<?echo $total['punishmentqty']." ".$punishments[$total['punishmenttype']];?>
					</td>
				</tr>
			<?endforeach;?>
		</table>
		<?	
			if($standardsTotals == null)
			{
				echo 'No Penalties Levied';
			}
		?>
</div>

<style>
	div.standardsblock
	{
		width:33%;
		float:left;
	}
	
	div.standardsblock h3
	{
		margin: 0px 0px 0px 0px;
	}
</style>