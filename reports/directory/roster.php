<?
	require("../../commonfiles/common.php");
	require("../../commonfiles/permissions.php");
	require("../../commonfiles/lists.php");
 
	if(empty($_SESSION['user'])) 
	{ 
	    header("Location: ../../login.php"); 
     
	    die("Redirecting to login.php"); 
	}

	$pagePermission = $_SESSION['user']['pdirectory'];
	
	if($pagePermission  == 0 || $pagePermission  == 1 || $pagePermission  == 3)
	{
		header("Location: ../../no_access.html"); 

		die("Redirecting to no_access.html"); 
	}
?>
<!--Common Title Block-->
	<!doctype html>
	<title>Chapter Roster</title>
	<div align="center">
		<?echo $_SESSION['user']['organizationname']." - ".$_SESSION['user']['chapterdesignation'];?>
	</div>
<!--Print Full Roster-->
<?
	if(isset($_GET['includeAlumni']))
	{
		$query_members = "
			SELECT localid, CONCAT(members.firstname, \" \", members.lastname) AS name, externalkey, email, phonenumber, major
			FROM greekDB.members
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
			ORDER BY members.localid;";
		
	    try 
	    { 
	        $stmt = $db->prepare($query_members); 
	        $stmt->execute(); 
	    } 
	    catch(PDOException $ex) 
	    { 
	        die("Failed to run query: " . $ex->getMessage()); 
	    } 

	    $members = $stmt->fetchAll();

		$half = floor(count($members)/2);

		$membersTop = array_slice($members,0,$half);
		$membersBottom = array_slice($members,$half);
?>
		<div align="center">
			<?
				echo '<a href='.$_SERVER['PHP_SELF'].'>All Brothers</a><br />';
			?>
			<table style="display:inline-block; margin-bottom: auto;">
			<?
				$index = 0;
			?>
		    <?php foreach($members as $member):
				if($index%11 == 0 && $index != 0)
				{
					echo '</table><table style="display:inline-block;">';
					if($index%22 == 0)
					{
						echo "<p><!-- pagebreak --></p>";
					}
				}
			?>
				    <tr>
						<th rowspan = "4" style="width: 30px;"><? echo $member['localid']; $index++?></th>
				        <th rowspan = "4"><img src="<? echo 'https://s3.amazonaws.com/greekdb/usercontent/'
							.$_SESSION['user']['organizationexternalkey'].'/'
							.$_SESSION['user']['chapterexternalkey'].'/'
							.$member['externalkey'].'/profileimage.jpg'; ?>"
								alt="<?echo $member['name'];?>">
						</th>
				        <td>Name: <?echo (strlen($member['name']) > 27 ? substr($member['name'],0,24)."..." : substr($member['name'],0,27));?></td>
				    </tr>
					<tr>
						<td>Major: <?echo (strlen($member['major']) > 29 ? substr($member['major'],0,26)."..." : substr($member['major'],0,29));?></td>
					</tr>
					<tr>
						<td>Phone: <?echo (strlen($member['phonenumber']) >= 10 ? '('.substr($member['phonenumber'],-10,3).') '.substr($member['phonenumber'],-7,3).'-'.substr($member['phonenumber'],-4,4) : '');?></td>
					</tr>
					<tr>
						<td style="width: 212px;">Email: <?echo strtolower(strlen($member['email']) > 28 ? substr($member['email'],0,25)."..." : substr($member['email'],0,28));?></td>
					</tr>
		    <?php endforeach; ?>
			<?
				while($index%22 != 0)
				{
					if($index%11 == 0)
						echo '</table><table style="display:inline-block;">';
				?>
					<tr>
						<th rowspan = "4" style="width: 30px;"></th>
				        <th rowspan = "4"><img></th>
				        <td>Name: </td>
				    </tr>
					<tr>
						<td>Major: </td>
					</tr>
					<tr>
						<td>Phone: </td>
					</tr>
					<tr>
						<td style="width: 212px;">Email: </td>
					</tr>
				<?
					$index++;
				}
			?>
			</table>
		</div>

<!--Print Active Roster-->
<?
	}
	else
	{
		$query_members = "
			SELECT localid, CONCAT(members.firstname, \" \", members.lastname) AS name, externalkey, email, phonenumber, major
			FROM greekDB.members
			WHERE
				members.idorganization = ".$_SESSION['user']['idorganization']."
					AND
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				members.isAlumnus = 0
			ORDER BY members.localid;";
			
	    try 
	    { 
	        $stmt = $db->prepare($query_members); 
	        $stmt->execute(); 
	    } 
	    catch(PDOException $ex) 
	    { 
	        die("Failed to run query: " . $ex->getMessage()); 
	    } 

	    $members = $stmt->fetchAll();

		$half = floor(count($members)/2);

		$membersTop = array_slice($members,0,$half);
		$membersBottom = array_slice($members,$half);
?>
	<div align="center">
		<?
			echo '<a href='.$_SERVER['PHP_SELF'].'?includeAlumni=TRUE>Active Membership</a><br />';
		?>
		<table style="display:inline-block; margin-bottom: auto;">
		<?
			$index = 0;
		?>
	    <?php foreach($members as $member):
			if($index%11 == 0 && $index != 0)
			{
				echo '</table><table style="display:inline-block;">';
				if($index%22 == 0)
				{
					echo "<p><!-- pagebreak --></p>";
				}
			}
		?>
			    <tr>
					<th rowspan = "4" style="width: 30px;"><? echo ($index++) +1;?><br /><?echo '<h6>('.$member['localid'].')</h6>'?></th>
			        <th rowspan = "4"><img src="<? echo 'https://s3.amazonaws.com/greekdb/usercontent/'
						.$_SESSION['user']['organizationexternalkey'].'/'
						.$_SESSION['user']['chapterexternalkey'].'/'
						.$member['externalkey'].'/profileimage.jpg'; ?>"
							alt="<?echo $member['name'];?>">
					</th>
			        <td>Name: <?echo (strlen($member['name']) > 27 ? substr($member['name'],0,24)."..." : substr($member['name'],0,27));?></td>
			    </tr>
				<tr>
					<td>Major: <?echo (strlen($member['major']) > 29 ? substr($member['major'],0,26)."..." : substr($member['major'],0,29));?></td>
				</tr>
				<tr>
					<td>Phone: <?echo (strlen($member['phonenumber']) >= 10 ? '('.substr($member['phonenumber'],-10,3).') '.substr($member['phonenumber'],-7,3).'-'.substr($member['phonenumber'],-4,4) : '');?></td>
				</tr>
				<tr>
					<td style="width: 212px;">Email: <?echo strtolower(strlen($member['email']) > 28 ? substr($member['email'],0,25)."..." : substr($member['email'],0,28));?></td>
				</tr>
	    <?php endforeach; ?>
		<?
			while($index%22 != 0)
			{
				if($index%11 == 0)
					echo '</table><table style="display:inline-block;">';
			?>
				<tr>
					<th rowspan = "4" style="width: 30px;"></th>
			        <th rowspan = "4"><img></th>
			        <td>Name: </td>
			    </tr>
				<tr>
					<td>Major: </td>
				</tr>
				<tr>
					<td>Phone: </td>
				</tr>
				<tr>
					<td style="width: 212px;">Email: </td>
				</tr>
			<?
				$index++;
			}
		?>
		</table>
	</div>
<?
	}
?>
<!--Print Common Footer-->
	<footer align="center">
		<img src="https://s3.amazonaws.com/greekdb/resources/sessionbar/icons/greekDB-logo.png" alt="greekDB" height="12" width="56">
	</footer>
</html>
<style type="text/css">
h6{
	margin:0px 0px 0px 0px;
	font-weight: 100;
}

td {
	font-size: small;
}

th img{
	width: 56px;
	height: 56px;
}

table {
	border-width: 2px;
	border-spacing: 2px;
	border-style: solid;
	border-color: gray;
	border-collapse: collapse;
}
table th {
	border-width: 1px;
	padding-left: 5px;
	padding-right: 5px;
	padding-top: 2px;
	padding-bottom: 2px;
	border-style: solid;
	border-color: gray;
}
table td {
	border-width: 1px;
	padding-left: 5px;
	padding-right: 5px;
	padding-top: 2px;
	padding-bottom: 2px;
	border-style: solid;
	border-color: gray;
}

tr:nth-child(8n){
	background-color: #DFDFDF;
}
tr:nth-child(8n+2){
	background-color: #DFDFDF;
}
tr:nth-child(8n+4){
	background-color: #DFDFDF;
}
tr:nth-child(8n+6){
	background-color: #DFDFDF;
}

</style>