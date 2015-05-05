<?
	if($_SESSION['user']['pservice'] > 0)
	{
		$query = "SELECT SUM(qty) 
					AS qty, idqtyunit
					FROM communityserviceevents
					WHERE (
						idorganization = ".$_SESSION['user']['idorganization']."
							AND
						idchapter = ".$_SESSION['user']['idchapter']."
							AND
						localid = ".$_SESSION['user']['localid']."
							AND 
						date > NOW() - INTERVAL 4 MONTH)
					GROUP BY idqtyunit
					ORDER BY idqtyunit;";
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
		<table>
			<?foreach($serviceTotals as $total):?>
				<tr>
					<td>
						<?echo intval($total['qty'])." ".$units[$total['idqtyunit']];?>
					</td>
				</tr>
			<?endforeach;?>
		</table>
		<?
			if($serviceTotals == null)
			{
				echo 'No Events Posted';
			}
		?>
</div>

<style>
	div.communityserviceblock
	{
		width:33%;
		float:left;
	}
	
	div.communityserviceblock h3
	{
		margin: 0px 0px 0px 0px;
	}
</style>