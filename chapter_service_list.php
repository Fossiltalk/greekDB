<?php 

    require("commonfiles/common.php");
	require("commonfiles/lists.php");
	require("commonfiles/permissions.php");

    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
    }

	$pagePermission = $_SESSION['user']['pservice'];
	
	if($pagePermission == 0)
	{
        header("Location: no_access.html"); 
         
        die("Redirecting to no_access.html"); 
	}
     
	if($pagePermission == 1 || $pagePermission == 3)
	{
		$query = "
			SELECT idcommunityserviceevents, serviceperformed, date, members.localid, qty, idqtyunit, firstname, lastname
			FROM members
			JOIN communityserviceevents
				ON
				(
					communityserviceevents.idorganization=members.idorganization
						AND
					communityserviceevents.idchapter=members.idchapter
						AND
					communityserviceevents.localid=members.localid
				)
			WHERE
				(
					communityserviceevents.idorganization = ".$_SESSION['user']['idorganization']."
						AND
					communityserviceevents.idchapter = ".$_SESSION['user']['idchapter']."
						AND
					communityserviceevents.localid = ".$_SESSION['user']['localid']."
						AND
					dateclosed > DATE_SUB(now(), INTERVAL 4 MONTH)
				)
			ORDER BY date DESC
			;
	    ";
	}
	else
	{
		$query = "
			SELECT idcommunityserviceevents, serviceperformed, date, members.localid, qty, idqtyunit, firstname, lastname
			FROM chapters
			JOIN members
				ON members.idchapter=chapters.idchapter
			JOIN communityserviceevents
				ON
				(
					communityserviceevents.idorganization=members.idorganization
						AND
					communityserviceevents.idchapter=members.idchapter
						AND
					communityserviceevents.localid=members.localid
				)
			WHERE (members.idchapter = ".$_SESSION['user']['idchapter']."
				AND
			date > DATE_SUB(now(), INTERVAL 4 MONTH))
			ORDER BY date DESC
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
         
    $serviceEvents = $stmt->fetchAll(); 
?> 
<!doctype html class="grad">
	<head>
		<meta charset="utf-8">
		<title>Chapter Service List</title>
	</head>
	<?php   require("commonfiles/globalnavigationbar.php"); ?>
	<div id="content">
		<aside>
			<?
				require ("commonfiles/navigationbanner.php");
			?>
		</aside>
	
		<div class="application">
			<h1>Chapter Service List</h1> 
			<table> 
				<tr> 
					<th></th>
					<th>Event Date</th>
					<th>Member</th>
					<th>Description</th>
					<th>Quantity</th>
				</tr> 
				<?php foreach($serviceEvents as $event): ?> 
					<tr>
						<td><?php 
							if($pagePermission == 6
								|| ($pagePermission == 5 && $event['localid'] != $_SESSION['user']['localid'])
							)
							{
								print "<a href=\"javascript:displayEvent(".$event['idcommunityserviceevents'].",210)\">edit</a>";
							} ?></a></td>
						<td><?php echo $event['date']; ?></td> 
						<td><?php echo htmlentities($event['lastname'], ENT_QUOTES, 'UTF-8').", ".htmlentities($event['firstname'], ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo htmlentities($event['serviceperformed'], ENT_QUOTES, 'UTF-8'); ?></td>
						<td><?php echo $event['qty']." ".$units[$event['idqtyunit']]; ?></td>
					</tr> 
				<?php endforeach; ?> 
			</table>

			<?
				if($pagePermission >= 3)
				{
					print "<a href=\"javascript:createEvent()\">Add Event</a><br />";
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
		margin-top: 2px;
		margin-bottom: 8px;
	}
	
	table
	{
		width: 100%;
	}
</style>
	
<script>
function displayEvent(eventid, size)
{
    window.open("service_event_editor.php?idevent="+eventid,
		"_blank",
		"toolbar=no, scrollbars=no, resizable=no, top=100, left=400, width=420, height=300");
}

function createEvent()
{
    window.open("service_event_creator.php",
		"_blank",
		"toolbar=no, scrollbars=no, resizable=no, top=100, left=400, width=420, height=370");
}
</script>