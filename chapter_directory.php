<?php
	
    require("commonfiles/common.php");
	require("commonfiles/permissions.php");
     
    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
         
        die("Redirecting to login.php"); 
    }
	
	$pagePermission = $_SESSION['user']['pdirectory'];
	
	if($pagePermission == 0)
	{
        header("Location: no_access.html"); 
         
        die("Redirecting to no_access.html"); 
	}
    
	
	if($pagePermission == 1 || $pagePermission == 3)
	{
		$query = " 
			SELECT 
						localid, 
			            firstname, 
			            lastname,
						email,
						phonenumber,
						graduationyear
			FROM members
			WHERE (
				idorganization = '".$_SESSION['user']['idorganization']."'
					AND
				idchapter = '".$_SESSION['user']['idchapter']."'
					AND
				localid = '".$_SESSION['user']['localid']."'
				)
    			";
	}
	else
	{
		$query = " 
			SELECT 
						localid,
						isDeceased,
						isAlumnus,
			            firstname, 
			            lastname,
						email,
						email_lastvalidated,
						phonenumber,
						phonenumber_lastvalidated,
						graduationyear 
			FROM members
			WHERE (
				idorganization = '".$_SESSION['user']['idorganization']."'  
				AND
				idchapter = '".$_SESSION['user']['idchapter']."'
				)
			ORDER BY localid DESC;
    			";
	}
     
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
		<title>Chapter Directory</title>
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
		<h1>Chapter Directory</h1> 
		<table style="text-align:center"> 
		    <tr> 
		        <th>Bond #</th>
				<th>First Name</th> 
		        <th>Last Name</th>
				<th>Email</th>
				<th>Phone #</th>
				<th>Graduation Year</th>
		    </tr> 
		    <?php foreach($rows as $row):
		    	?><tr id="<? echo $row['localid'];?>"
				<?
						if($row['isDeceased'])
						{
							echo 'class="deceasedMember"';
						}
						elseif(!$row['isAlumnus'])
						{
							echo 'class="activeMember"';
						}
				?>>
					<td><?php print '<a href="javascript:viewMemberProfile('.$row['localid'].")\">".$row['localid']."</a>";?></td>
		            <td><?php echo htmlentities($row['firstname'], ENT_QUOTES, 'UTF-8'); ?></td>  
		            <td><?php echo htmlentities($row['lastname'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?php echo htmlentities($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
					<td><?echo (strlen($row['phonenumber']) >= 10 ? '('.substr($row['phonenumber'],-10,3).') '.substr($row['phonenumber'],-7,3).'-'.substr($row['phonenumber'],-4,4) : '');?></td> 
					<td><?php echo $row['graduationyear']; ?></td>
		        </tr>
		    <?php endforeach; ?> 
		</table>
		<?
			if($pagePermission == 5 || $pagePermission == 6)
			{
				echo "<a href=\"javascript:createNewMember()\">Add Member</a><br />";
			}
		?>
	</div>
</div>
	<?php require("commonfiles/footer.html");?>	
</html>

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

<style>		
	.application h1{
		margin-top: 2px;
		margin-bottom: 8px;
	}
	
	.deceasedMember {
		text-align:center;
		color:lightgrey;
	}
	
	.activeMember {
		text-align:center;
		color:#0081C6;
	}
	
	table
	{
		width: 100%;
	}
</style>

												<!--------------------Page Javascript Begins Here-------------------------->
<script>
	function createNewMember()
	{
	    window.open("memberinfo.php?localid=(new)",
			"_blank",
			"toolbar=no, scrollbars=no, top=20, left=200, width=833, height=500");
	}
</script>

<script>
	function viewMemberProfile(localid)
	{
	    window.open("memberinfo.php?localid="+localid,
			"_blank",
			"toolbar=no, scrollbars=no, top=20, left=200, width=833, height=500");
	}
</script>