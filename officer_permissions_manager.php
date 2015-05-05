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
	
	$query = 
		"
			SELECT idofficers, title, pservice, pdirectory, pjudicial, pbilling, pofficerpermissions
			FROM greekDB.officers
			WHERE 
				idorganization = ".$_SESSION['user']['idorganization']."
					AND
				idchapter = ".$_SESSION['user']['idchapter']."
			ORDER BY title;
		";
		
	    try 
	    {  
	        $stmt = $db->prepare($query); 
	        $stmt->execute(); 
	    } 
	    catch(PDOException $ex) 
	    {} 

	    $rows = $stmt->fetchAll(); 
?>

<!doctype html class="grad">
	<head>
		<meta charset="utf-8">
		<title>Officer Permissions</title>
	</head>
	<?php   require("commonfiles/globalnavigationbar.php"); ?>
	<div id="content">
		<aside>
			<?
				require ("commonfiles/navigationbanner.php");
			?>
		</aside>
		<div class="application">
			<h1>Officer Permissions</h1>
			<table> 
				<tr> 
					<th>Officer</th>
					<th>Community Service</th> 
					<th>Chapter Directory</th>
					<th>Judical Cases</th>
					<th>Billing</th>
					<th>Permissions Management</th>
				</tr> 
			<?php foreach($rows as $row):?> 
					<tr>
						<td>
							<a href="javascript:viewOfficerInfo(<?php echo $row['idofficers'];?>)">
								<?php echo $row['title'];?>
							</a>
						</td>
	
						<td style="text-align:center;">
							<? echo (empty($row['pservice'])? '' : $descriptions[$row['pservice']]);?>
						</td>
						<td style="text-align:center;">
							<? echo (empty($row['pdirectory']) ? '' : $descriptions[$row['pdirectory']]);?>
						</td>
						<td style="text-align:center;">
							<? echo (empty($row['pjudicial']) ? '' : $descriptions[$row['pjudicial']]);?>
						</td>
						<td style="text-align:center;">
							<? echo (empty($row['pbilling']) ? '' : $descriptions[$row['pbilling']]);?>
						</td>
						<td style="text-align:center;">
							<? echo (empty($row['pofficerpermissions']) ? '' : $descriptions[$row['pofficerpermissions']]);?>
						</td>
					</tr> 
				<?php endforeach; ?> 
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
		width: 100%;
		min-height: 100%;
	}

	th, td {
	    border-top: 1px solid gray;
		border-left: 1px solid gray;
	}
</style>

<script>
	function viewOfficerInfo(officer)
	{
	    window.open("officer_permissions_editor.php?officer="+officer,
			"_blank",
			"toolbar=no, scrollbars=no, top=20, left=200, width=400, height=200");
	}
</script>