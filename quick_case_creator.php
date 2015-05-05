<?
	require("commonfiles/common.php");

	if(!$_SESSION['chaptersettings']['memberdefinedcases_enabled'])
	{
		header("Location: no_access.html"); 
		die("Redirecting to no_access.html"); 
	}
	
	if(!empty($_POST)) 
	{ 	
			$put=" 
	        INSERT INTO
				judicialcases
					(
						title,
						dateopened,
						localid,
						idchapter,
						idorganization,
						details
					)
			VALUES
				(
					'".
						htmlentities($_POST['title'], ENT_QUOTES, 'UTF-8')."', 
		            
						CURDATE(),
		            ".
						intval($_POST['memberselector']).",
		            ".
						intval($_SESSION['user']['idchapter']).",
		            ".
						intval($_SESSION['user']['idorganization']).",
					\"".
						htmlentities($_POST['details'], ENT_QUOTES, 'UTF-8')."\"
					
				);
	    ";
	
	    try 
	    { 
	        $stmt = $db->prepare($put); 
	        $stmt->execute(); 
	    } 
	    catch(PDOException $ex) 
	    { 
	    }
		print("<script>close();</script>");
	}
?>
<!doctype html>
	<form action="<?echo $_SERVER['PHP_SELF'];?>" method="POST">
		Offense:
			<input type="text"
				name="title"
				size="25"
			/>
		<br />
		Member Name:
			<?
				$query = "
					SELECT localid, firstname, lastname
					FROM chapters
					JOIN members
					ON members.idchapter=chapters.idchapter
					WHERE members.idchapter = ".
						$_SESSION['user']['idchapter']."
					ORDER BY localid DESC
						;
	    			";

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

	    		// Finally, we can retrieve all of the found rows into an array using fetchAll 
	    		$members = $stmt->fetchAll();
			?>
			<select name="memberselector">
				<?
					foreach($members AS $member):
						print("<option value=\"".$member['localid']."\">");
						echo htmlentities($member['localid']." - ".$member['lastname'].", ".$member['firstname'], ENT_QUOTES, 'UTF-8');
						print("</option>");
					endforeach; 
				?>
			</select>
		<br />
		Details:
			<textarea name="details"
				rows="4"
				cols="50"
				></textarea>
		<br />
		<input type="submit" value="Submit" />
		<br />
	</form>
</html>