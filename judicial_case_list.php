<?php 
    require("commonfiles/common.php");
	require("commonfiles/lists.php");
	require("commonfiles/permissions.php");

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    }

	$pagePermission = $_SESSION['user']['pjudicial'];
	
	if($pagePermission == 0)
	{
        header("Location: no_access.html"); 
         
        die("Redirecting to no_access.html"); 
	}
     
	if($pagePermission == 1 || $pagePermission == 3)
	{
		$query = "
			SELECT idjudicialcases, title, dateopened, verdict, firstname, lastname, punishmentqty, punishmenttype, judicialcases.localid
			FROM members
			JOIN judicialcases
			ON judicialcases.localid=members.localid
			WHERE 
			(
				judicialcases.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				judicialcases.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				judicialcases.localid = ".$_SESSION['user']['localid']."
					AND
				dateclosed > DATE_SUB(now(), INTERVAL 4 MONTH)
			)
			ORDER BY dateopened DESC, members.localid ASC
			;
	    ";
	}
	else
	{
		$query = "
			SELECT idjudicialcases, title, dateopened, verdict, firstname, lastname, punishmentqty, punishmenttype, judicialcases.localid
			FROM chapters
			JOIN members
				ON members.idchapter=chapters.idchapter
			JOIN judicialcases
				ON judicialcases.localid=members.localid
				WHERE 
				(
					judicialcases.idorganization = ".$_SESSION['user']['idorganization']."
						AND
					judicialcases.idchapter = ".$_SESSION['user']['idchapter']."
						AND
					dateclosed > DATE_SUB(now(), INTERVAL 4 MONTH)
				)
			ORDER BY dateopened DESC, members.localid ASC
			;
	    ";
	}
     
    try 
    { 
        $stmt = $db->prepare($query); 
        $stmt->execute(); 
    } 
    catch(PDOException $ex) 
    { 
        die("Failed to run query: " . $ex->getMessage()); 
    } 
         
    $judicialCases = $stmt->fetchAll(); 
?>
<!doctype html class="grad">
	<head>
		<meta charset="utf-8">
		<title>Judicial Case List</title>
		<?php	require("commonfiles/header.html"); ?>
	</head>
	
	<?php   require("commonfiles/globalnavigationbar.php"); ?>
	<div id="content">
		<aside>
			<?
				require ("commonfiles/navigationbanner.php");
			?>
		</aside>
		<div class="application">
			<h1>Judicial Case List</h1> 
			<table> 
				<tr> 
					<th></th>
					<th>Date Opened</th>
					<th>Member</th>
					<th>Offense</th>
					<th>Verdict</th>
					<th>Punishment</th>
				</tr> 
				<?php foreach($judicialCases as $case): ?> 
					<tr>
						<td><?php 
							if($pagePermission == 6
								|| (($pagePermission == 3 || $pagePermission == 4) && $case['localid'] == $_SESSION['user']['localid'])
								|| ($pagePermission == 5 && $case['localid'] != $_SESSION['user']['localid']))
							{
								print "<a href=\"javascript:displayCase(".$case['idjudicialcases'].")\">edit</a>";
							} 
							elseif((($pagePermission == 1 || $pagePermission == 2 || $pagePermission == 5) && $case['localid'] == $_SESSION['user']['localid'])
								|| ($pagePermission == 2 || $pagePermission == 4) && $case['localid'] != $_SESSION['user']['localid'])
							{
								print "<a href=\"javascript:displayCase(".$case['idjudicialcases'].")\">details</a>";
							}
							?></a></td>
			
						<td><?php echo $case['dateopened']; ?></td> 
						<td><?php echo htmlentities($case['lastname'], ENT_QUOTES, 'UTF-8').", ".htmlentities($case['firstname'], ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlentities($case['title'], ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo ($case['verdict'] == 0 ? "<b>".$verdicts[$case['verdict']]."</b>" : $verdicts[$case['verdict']])?></td>
						<td><?php echo $case['punishmentqty'].' '.$punishments[$case['punishmenttype']]; ?></td>
					</tr> 
				<?php endforeach; ?> 
			</table>

			<?
				if($pagePermission != 1 && $pagePermission != 2)
				{
					print "<a href=\"javascript:createCase()\">Add Case</a>";
				}
			?>
		</div>
	</div>
	<?php require("commonfiles/footer.html");?>	
</html>
		
	
												<!--------------------Page CSS Begins Here---------------------------->
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
</style>

<?
	if(isMobile())
	{
		echo '<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/2panel.css">';
	}
	else
	{
		echo '<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/3panel.css">';
	}
?>

<style>
	.application h1{
		margin:auto;
	}
	
	table
	{
		width: 100%;
	}
</style>

												<!--------------------Page Javascript Begins Here---------------------------->
<script>
function displayCase(caseid)
{
    window.open("judicial_case_editor.php?idjudicialcases="+caseid,
		"_blank",
		"toolbar=no, scrollbars=no, resizable=no, top=100, left=400, width=380, height=385");
}

function createCase(caseid)
{
    window.open("judicial_case_creator.php",
		"_blank",
		"toolbar=no, scrollbars=no, resizable=no, top=100, left=400, width=430, height=400");
}
</script>