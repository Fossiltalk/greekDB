<?php
    require("commonfiles/common.php");
	require("commonfiles/permissions.php");

	$pagePermission = $_SESSION['user']['pdirectory'];

	if($pagePermission == 0)
	{
        header("Location: login.php"); 
        die("Redirecting to login.php"); 
	}

	if($pagePermission < 3)
	{
        header("Location: no_access.html"); 
        die("Redirecting to no_access.html"); 
	}

	if($pagePermission >= 3)
	{
		$query = " 
			SELECT 
						localid,
						isDeceased,
						isAlumnus,
			            firstname, 
			            lastname,
						recruiter,
						initiationdate
			FROM members
			WHERE (
				idorganization = '".$_SESSION['user']['idorganization']."'  
				AND
				idchapter = '".$_SESSION['user']['idchapter']."'
				AND 
				recruiter != 0
				)
			ORDER BY localid ASC";
	}
 
    try 
    { 
        // These two statements run the query against your database table. 
        $stmt = $db->prepare($query); 
        $stmt->execute(); 
    } 
    catch(PDOException $ex) 
    { 
        die("Failed to run query: " . $ex->getMessage()); 
    } 
     
    $rows = $stmt->fetchAll(); 
?>

<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["orgchart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');
        data.addColumn('string', 'ToolTip');

        data.addRows([
			<?
				foreach($rows as $row):
					$v = '"'.$row['localid'].'"';
					$a = '<a href=javascript:viewMemberProfile('.$row['localid'].')>'.$row['localid'].'</a>';
					$f = 
						'<div id='.$row['localid'].'>'
							.$a.'<br />'
							.$row['firstname'].' '.$row['lastname']
						.'</div>';
					$bigB = $row['recruiter'];
					$initiated = $row['initiationdate'];
				
		  			echo '[{v:'.$v.', f:"'.$f.'"}, "'.$bigB.'","'.$initiated.'"],';
				endforeach;
			?>
        ]);

        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        chart.draw(data, {allowHtml:true,allowCollapse:true});
      }
	  </script>

	  <script>
	  	function viewMemberProfile(localid)
	  	{
	  	    window.open("memberinfo.php?localid="+localid,
	  			"_blank",
	  			"toolbar=no, scrollbars=no, top=20, left=200, width=400, height=700");
	  	}
	  </script>
	  <title>Lineage Tree</title>
	  <style>
	  a {
		  text-decoration: none;
	  }
	  </style>
    </head>
  <body>
    <div id="chart_div"></div>
  </body>
</html>