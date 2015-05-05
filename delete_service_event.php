<?
	require("commonfiles/common.php");
	
	$query = "DELETE FROM greekDB.communityserviceevents
				WHERE communityserviceevents.idcommunityserviceevents = ".$_GET['idevent'].";";
	
    try 
    { 
        // These two statements run the query against your database table. 
        $stmt = $db->prepare($query); 
        $stmt->execute(); 
    } 
    catch(PDOException $ex) 
    { 
        // Note: On a production website, you should not output $ex->getMessage(). 
        // It may provide an attacker with helpful information about your code.  
        die("Failed to run query: " . $ex->getMessage()); 
    }
	print("<script>close()</script>");
?>