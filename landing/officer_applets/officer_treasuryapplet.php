<?
	if($_SESSION['user']['pbilling'] > 4)
	{
/*----------------------------------------------------------------------------  ----------------------------------------------------------------------------*/
		$query = 'SELECT members.localid, balance, members.lastname, members.phonenumber
			FROM
			(	
				SELECT localid, SUM(charges) AS balance
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
				balance > 0
				ORDER BY RAND()
				LIMIT 4
			;';

		try 
		{ 
			$stmt = $db->prepare($query); 
			$stmt->execute(); 
		} 
		catch(PDOException $ex) 
		{ 
		 	die("Failed to run query: " . $ex->getMessage()); 
		} 
		$outstandingDues = $stmt->fetchAll();
	}
?>
<div class="treasuryblock">
	<h3>Billing</h3>
	Payments Outstanding
	<br />
	------------------
	<?
		foreach($outstandingDues as $outstanding):
			echo ("<br />".$outstanding['lastname']." - $".number_format($outstanding['balance'],2,'.',','));
		endforeach;
	?>
</div>

<style>
	div.treasuryblock
	{
		width:33%;
		float:left;
	}
</style>