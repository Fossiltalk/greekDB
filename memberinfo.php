<?php 	
    // First we execute our common code to connection to the database and start the session 
    require("commonfiles/common.php");
	require("commonfiles/lists.php");
	require("commonfiles/permissions.php");
     
    // At the top of the page we check to see whether the user is logged in or not 
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
	
	$query = "
		SELECT localid, firstname, lastname
		FROM chapters
		JOIN members
		ON members.idchapter=chapters.idchapter
		WHERE
			(
				members.idchapter = ".$_SESSION['user']['idchapter']."
					AND
				members.idorganization = ".$_SESSION['user']['idorganization']."
			)
		ORDER BY localid DESC
			;
		";

	try 
 	{ 
		$stmt = $db->prepare($query); 
		$stmt->execute(); 
	} 
	catch(PDOException $ex) 
	{} 

	$members = $stmt->fetchAll();
	
	if(empty($_POST))
	{
		if($_GET['localid'] == '(new)')
		{
			if($pagePermission < 5)
			{
				header("Location: no_access.html"); 
				die("Redirecting to no_access.html"); 
			}
		
			$row['localid'] = '';
			$row['firstname'] = '';
			$row['middlename'] = '';
			$row['lastname'] = '';
			$row['isDeceased'] = '';
			$row['isAlumnus'] = '';
			$row['email'] = '';
			$row['email_lastvalidated'] = '';
			$row['website'] = '';
			$row['facebook'] = '';
			$row['linkedin'] = '';
			$row['company'] = '';
			$row['phonenumber'] = '';
			$row['phonenumber_lastvalidated'] = '';
			$row['major'] = '';
			$row['graduationyear'] = '';
			$row['legacy'] = '';
			$row['street'] = '';
			$row['streetcontinued'] = '';
			$row['city'] = '';
			$row['state'] = '';
			$row['zipcode'] = '';
			$row['bidwriter'] = '';
			$row['copyofbid'] = '';
			$row['acceptancedate'] = '';
			$row['initiationdate'] = '';
			$row['recruiter'] = '';
			$row['additionalinfo'] = '';
		}
		else
		{
			$query = " 
				SELECT 
						localid,
						externalkey,
						firstname,
						middlename, 
						lastname,
						isDeceased,
						isAlumnus,
						email,
						email_lastvalidated,
						website,
						facebook,
						linkedin,
						company,
						phonenumber,
						phonenumber_lastvalidated,
						major,
						graduationyear,
						legacy,
						street,
						streetcontinued,
						city,
						state,
						zipcode,
						bidwriter,
						copyofbid,
						acceptancedate,
						initiationdate,
						recruiter,
						additionalinfo
				FROM members
				WHERE
				(
					members.idorganization = ".$_SESSION['user']['idorganization']."
						AND
					members.idchapter = ".$_SESSION['user']['idchapter']."
						AND
					members.localid = ".intval ($_GET['localid'])."
				);";
     
			try 
			{ 
				$stmt = $db->prepare($query); 
				$stmt->execute(); 
			} 
			catch(PDOException $ex) 
			{} 
         
			$row = $stmt->fetch();
		}
	}
	else
	{
		if($_GET['localid'] == '(new)') 
		{
			if($pagePermission < 5)
			{
				header("Location: no_access.html"); 
				die("Redirecting to no_access.html"); 
			}
			
			$query=" 
		        INSERT INTO members(
					firstname,
					middlename,
					lastname,
					isAlumnus,
					isDeceased,
					idorganization,
					idchapter,
					localid,
					email,
					website,
					facebook,
					linkedin,
					company,
					phonenumber,
					major,
					graduationyear,
					legacy,
					street,
					streetcontinued,
					city,
					state,
					zipcode,
					bidwriter,
					acceptancedate,
					initiationdate,
					recruiter,
					additionalinfo,
					lastUpdated)
				VALUES(
		            :firstname,
					:middlename,
					:lastname,
					:isAlumnus,
					:isDeceased,
					'".$_SESSION['user']['idorganization']."',
					'".$_SESSION['user']['idchapter']."',
					:localid,
					:email,
					:website,
					:facebook,
					:linkedin, 
					:company, 
					:phonenumber, 
					:major, 
					:graduationyear, 
					:legacy,
					:street,
					:streetcontinued,
					:city,
					:state, 
					:zipcode,
					:bidwriter, 
					:acceptancedate, 
					:initiationdate,
					:recruiter,
					:additionalinfo,
					NOW()
					);";
				
	        $query_params = array( 
				':firstname' => $_POST['firstname'],
				':middlename' => $_POST['middlename'],
				':lastname' => $_POST['lastname'],
				':isAlumnus' => ($_POST['isAlumnus'] ? $_POST['isAlumnus'] : 0),
				':isDeceased' => ($_POST['isDeceased'] ? $_POST['isDeceased'] : 0),
				':localid' => intval($_POST['localid']),
				':email' => $_POST['email'],
				':website' => $_POST['website'],
				':facebook' => $_POST['facebook'],
				':linkedin' => $_POST['linkedin'],
				':company' => $_POST['company'],
				':phonenumber' => $_POST['phonenumber'],
				':major' => $_POST['major'],
				':graduationyear' => ($_POST['graduationyear'] ? $_POST['graduationyear'] : null),
				':legacy' => ($_POST['legacy'] ? $_POST['legacy'] : 0),
				':street' => $_POST['street'],
				':streetcontinued' => $_POST['streetcontinued'],
				':city' => $_POST['city'],
				':state' => $_POST['state'],
				':zipcode' => $_POST['zipcode'],
				':bidwriter' => ($_POST['bidwriter'] ? $_POST['bidwriter'] : null),
				':acceptancedate' => ($_POST['acceptancedate'] ? $_POST['acceptancedate'] : null),
				':initiationdate' => ($_POST['initiationdate'] ? $_POST['initiationdate'] : null),
				':recruiter' => ($_POST['recruiter'] ? $_POST['recruiter'] : null),
				':additionalinfo' => $_POST['additionalinfo']
	        );

	        try 
	        { 
	            $stmt = $db->prepare($query); 
	            $result = $stmt->execute($query_params); 
	        } 
	        catch(PDOException $ex) 
	        { 
	            die("Failed to run query: " . $ex->getMessage()); 
	        } 
		
			print("<script>close();</script>");
		}
		else
		{
			if($_GET['localid'] == $_SESSION['user']['localid'])
			{
				if($pagePermission == 1 || $pagePermission == 2 || $pagePermission == 5)
				{
					header("Location: no_access.html"); 
					die("Redirecting to no_access.html");
				}
			}
			else
			{
				if($pagePermission < 5)
				{
					header("Location: no_access.html"); 
					die("Redirecting to no_access.html");
				}
			}
		
		//Get old email and phone for comparison
			$query = " 
				SELECT 
					email,
					phonenumber
				FROM members
				WHERE
				(
					members.idorganization = ".$_SESSION['user']['idorganization']."
						AND
					members.idchapter = ".$_SESSION['user']['idchapter']."
						AND
					members.localid = ".intval ($_GET['localid'])."
				);";
     
			try 
			{ 
				$stmt = $db->prepare($query); 
				$stmt->execute(); 
			} 
			catch(PDOException $ex) 
			{} 
         
			$row = $stmt->fetch();
		
		//Update information
			$query=" 
		        UPDATE members	
				SET
					localid = :localid, 
		            firstname = :firstname,
					middlename = :middlename,
		            lastname = :lastname,
					isAlumnus = :isAlumnus,
					isDeceased = :isDeceased,
					email = :email,".
					(strcmp($row['email'],$_POST['email']) != 0 ? "email_lastvalidated = NOW()," : '')."
					website = :website,
					facebook = :facebook,
					linkedin = :linkedin,
					company = :company, 
					phonenumber = :phonenumber,".
					(strcmp($row['phonenumber'],$_POST['phonenumber']) != 0 ? "phonenumber_lastvalidated = NOW()," : '')."
					major = :major,
					graduationyear = :graduationyear, 
					street = :street,
					legacy = :legacy,
					streetcontinued = :streetcontinued, 
					city = :city, 
					state = :state,
					zipcode = :zipcode,
					bidwriter = :bidwriter, 
					acceptancedate = :acceptancedate, 
					initiationdate = :initiationdate,
					recruiter = :recruiter,
					additionalinfo = :additionalinfo,
					lastUpdated = NOW()
					WHERE
					(
						members.idorganization = ".$_SESSION['user']['idorganization']."
							AND
						members.idchapter = ".$_SESSION['user']['idchapter']."
							AND
						members.localid = ".intval ($_GET['localid'])."
					);";
	
	        $query_params = array( 
				':firstname' => $_POST['firstname'],
				':middlename' => $_POST['middlename'],
				':lastname' => $_POST['lastname'],
				':isAlumnus' => (isset($_POST['isAlumnus']) ? 1 : 0),
				':isDeceased' => (isset($_POST['isDeceased']) ? 1 : 0),
				':localid' => intval($_POST['localid']),
				':email' => $_POST['email'],
				':website' => $_POST['website'],
				':facebook' => $_POST['facebook'],
				':linkedin' => $_POST['linkedin'],
				':company' => $_POST['company'],
				':phonenumber' => $_POST['phonenumber'],
				':major' => $_POST['major'],
				':graduationyear' => ($_POST['graduationyear'] ? $_POST['graduationyear'] : null),
				':legacy' => (isset($_POST['legacy']) ? $_POST['legacy'] : 0),
				':street' => $_POST['street'],
				':streetcontinued' => $_POST['streetcontinued'],
				':city' => $_POST['city'],
				':state' => $_POST['state'],
				':zipcode' => $_POST['zipcode'],
				':bidwriter' => ($_POST['bidwriter'] ? $_POST['bidwriter'] : null),
				':acceptancedate' => ($_POST['acceptancedate'] ? $_POST['acceptancedate'] : null),
				':initiationdate' => ($_POST['initiationdate'] ? $_POST['initiationdate'] : null),
				':recruiter' => ($_POST['recruiter'] ? $_POST['recruiter'] : null),
				':additionalinfo' => $_POST['additionalinfo']
	        );

	        try 
	        { 
	            $stmt = $db->prepare($query); 
	            $result = $stmt->execute($query_params); 
	        } 
	        catch(PDOException $ex) 
	        { 
	            die("Failed to run query: " . $ex->getMessage()); 
	        }
	
		    header("Location: memberinfo.php?localid=".intval ($_GET['localid'])); 
			die("Redirecting to memberinfo.php?localid=".intval ($_GET['localid']));
		}
	}
/*---------------------------------------------------------------Here ends the update submission code.--------------------------------------------------------------*/
?> 
<!doctype html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $row['firstname']." ".$row['lastname'];?></title>
		<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/pop-up_std.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
		  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		  <link rel="stylesheet" href="/resources/demos/style.css">
		  <script>
		  $(function() {
		    $( "#tabs" ).tabs();
		  });
		  </script>
	</head>
	<div class="application">
		<h1><?php echo $row['firstname']." ".$row['lastname'];?></h1>
		<form action="<?=$_SERVER['PHP_SELF']."?localid=".$_GET['localid']?>" method="POST">
			ID:
				<input type="text"
					name="localid"
					value="<?php echo $row['localid']; ?>"
					size="4"
					/>
			<br />
			<div id="tabs">
			  <ul>
			    <li><a href="#tabs-1">Basic Information</a></li>
				<?if($pagePermission > 5 && $_GET['localid'] == $_SESSION['user']['localid']
						|| $pagePermission > 4 && $_GET['localid'] != $_SESSION['user']['localid'])
					{?>
			    <li><a href="#tabs-2">Historical Info</a></li>
					<?}?>
			    <li><a href="#tabs-3">Emergency Contact Info</a></li>
				<li><a href="#tabs-4">Additional Info</a></li>
			  </ul>
			  <div id="tabs-1">
					<img src="<? echo 'https://s3.amazonaws.com/greekdb/usercontent/'
					.$_SESSION['user']['organizationexternalkey']
					.'/'
					.$_SESSION['user']['chapterexternalkey']
					.'/'
					.$row['externalkey']
					.'/profileimage.jpg'; ?>"
						alt="<?echo $row['firstname']." ".$row['lastname'];?>"
						style="width:120px;height:120px">
					<br />
					<div id="contactInfo">
					First Name:
						<input type="text"
							name="firstname"
							value="<?php echo $row['firstname'];?>"
							size="10"
						/>
					<br />
					Middle Name:
						<input type="text"
							name="middlename"
							value="<?php echo $row['middlename'];?>"
							size="10"
						/>
					<br />
					Last Name:
						<input type="text"
							name="lastname"
							value="<?php echo $row['lastname'];?>"
							size="15"
						/>
					<br />
					Alumnus?:
						<input type="checkbox"
							name="isAlumnus"
							<?php
								if($row['legacy']==1)
									{echo 'checked ';}
								else
									{echo 'unchecked ';}
								if($_GET['localid'] == $_SESSION['user']['localid'] && $pagePermission <= 5
									|| $pagePermission <= 4 && $_GET['localid'] != $_SESSION['user']['localid'])
										{echo 'disabled';}
							?>/>
					<br />
					Deceased?:
						<input type="checkbox"
							name="isDeceased"
							<?php
								if($row['legacy']==1)
									{echo 'checked ';}
								else
									{echo 'unchecked ';}
								if($_GET['localid'] == $_SESSION['user']['localid'] && $pagePermission <= 5
									|| $pagePermission <= 4 && $_GET['localid'] != $_SESSION['user']['localid'])
										{echo 'disabled';}
							?>/>
					<br />
					Email:
						<input type="email"
							name="email"
							value="<?php echo $row['email'];?>"
							size="30"
						/>
						<input type="text"
							name="email_lastupdated"
							value="<?php echo $row['email_lastvalidated'];?>"
							size="10"
							disabled
						/>
					<br />
					Website:
						<input type="url"
							name="website"
							value="<?php echo $row['website'];?>"
							size="40"
							/>
						<br />
					Facebook:
						<input type="url"
							name="facebook"
							value="<?php echo $row['facebook'];?>"
							size="40"
							/>
						<br />
					LinkedIn:
						<input type="url"
							name="linkedin"
							value="<?php echo $row['linkedin'];?>"
							size="40"
							/>
						<br />
					Company:
						<input type="text"
							name="company"
							value="<?php echo $row['company'];?>"
							size="17"
						/>
					<br />
					Phone #:
						<input type="tel"
							name="phonenumber"
							value="<?php echo $row['phonenumber']?>"
							size="11"
						/>
						<input type="text"
							name="phonenumber_lastvalidated"
							value="<?php echo $row['phonenumber_lastvalidated'];?>"
							size="10"
							disabled
						/>
					<br />
					Major:
						<input type="text"
							name="major"
							value="<?php echo $row['major'];?>"
							size="20"
						/>
					<br />
					Graduation Year:
						<input type="text"
							name="graduationyear"
							value="<?php echo $row['graduationyear']; ?>"
							size="4"
						/>
					<br />
					Legacy?:
						<input type="checkbox"
							name="legacy"
							<?php
								if($row['legacy']==1)
									{echo 'checked ';}
								else
									{echo 'unchecked ';}
								if($_GET['localid'] == $_SESSION['user']['localid'] && $pagePermission <= 5
									|| $pagePermission <= 4 && $_GET['localid'] != $_SESSION['user']['localid'])
										{echo 'disabled';}
							?>/>
					</div>
					Address:
					<div id="address">
						<div id="col-1">
							Street:
								<input type="text"
									name="street"
									value="<?php echo $row['street'];?>"
									size="25"
								/>
							<br />
							Street (cont'd):
								<input type="text"
									name="streetcontinued"
									value="<?php echo $row['streetcontinued'];?>"
									size="20"
								/>
							<br />
						</div>
						<div id="col-2">
							City:
								<input type="text"
									name="city"
									value="<?php echo $row['city'];?>"
									size="25"
								/>
							<br />
							State:
								<select name="state">
									<option value="" selected></option>
									<?php
										for($i = 0;$i < count($us_state_abbrevs_names);next($us_state_abbrevs_names))
										{
											if($row['state'] == key($us_state_abbrevs_names))
											{
												print("<option value=\"".key($us_state_abbrevs_names)."\" selected>".pos($us_state_abbrevs_names)."</option>");
											}
											else
											{
												print("<option value=\"".key($us_state_abbrevs_names)."\">".pos($us_state_abbrevs_names)."</option>");
											}
				
											$i++;
										}
									?>
								</select> Zip:<input type="text"
									name="zipcode"
									value="<?php echo $row['zipcode']; ?>"
									size="10"
								/>
							<br />
						</div>
					</div>
			  </div>
			<?if($pagePermission > 5 && $_GET['localid'] == $_SESSION['user']['localid']
					|| $pagePermission > 4 && $_GET['localid'] != $_SESSION['user']['localid'])
				{?>
			  <div id="tabs-2">
	  			Bid Writer:
	  			<select name="bidwriter">
	  				<option value="" selected></option>
	  				<?
	  					foreach($members AS $member):
	  						if($member['localid'] == $row['bidwriter']
	  							|| ($_GET['localid'] == '(new)' && $member['localid'] == $_SESSION['user']['localid']))
	  						{
	  							print('<option value="'.$member['localid'].'" selected>'
	  								.$member['localid']." - ".$member['lastname'].", ".$member['firstname']
	  								."</option>");
	  						}
	  						else
	  						{
	  							print("<option value=\"".$member['localid']."\">"
	  								.$member['localid']." - ".$member['lastname'].", ".$member['firstname']
	  								."</option>");
	  						}
	  					endforeach; 
	  				?>
	  			</select>
	  			<br />
	  			Link to Bid Copy:
	  				<?php
	  					echo $row['copyofbid'];
	  				?>
	  			<br />
	  			Acceptance Date:	
	  				<input type="date"
	  					name="acceptancedate"
	  					value="<?php echo $row['acceptancedate']; ?>"
	  				/>(MM/DD/YYYY)
	  			<br />
	  			Initiation Date:
	  				<input type="date"
	  					name="initiationdate"
	  					value="<?php echo $row['initiationdate']; ?>"
	  				/>(MM/DD/YYYY)
	  			<br />
	  			Big Brother:
	  			<select name="recruiter">
	  				<option value="" selected></option>
	  				<?
	  					foreach($members AS $member):
	  						if($member['localid'] == $row['recruiter']
	  							|| ($_GET['localid'] == '(new)' && $member['localid'] == $_SESSION['user']['localid']))
	  						{
	  							print("<option value=\"".$member['localid']."\" selected>"
	  								.$member['localid']." - ".$member['lastname'].", ".$member['firstname']
	  								."</option>");
	  						}
	  						else
	  						{
	  							print("<option value=\"".$member['localid']."\">"
	  								.$member['localid']." - ".$member['lastname'].", ".$member['firstname']
	  								."</option>");
	  						}
	  					endforeach; 
	  				?>
	  			</select>
			  </div>
			  <?}?>
			  <div id="tabs-3">
			    
			  </div>
			  <div id="tabs-4">
	  			Additional Info:
	  			<br />
	  			<textarea name="additionalinfo"
	  				rows="4"
	  				cols="50"
	  				><?echo $row['additionalinfo'];?></textarea>
			  </div>
			</div>
<!--------------------------------------------------------------Here ends the input blanks.---------------------------------------------------------------------->
			<?
				if($_GET['localid'] == '(new)')
				{
					if($pagePermission == 5 || $pagePermission == 6)
					{
						print("<input type=\"submit\" value=\"Add\" />");
					}
					else
					{
						print("<input type=\"submit\" value=\"Add\" disabled/>");
					}
				}
				else
				{
					if(($pagePermission == 3 || $pagePermission == 4) && $_GET['localid'] == $_SESSION['user']['localid'] 
						|| ($pagePermission == 5 && $_GET['localid'] != $_SESSION['user']['localid'])
						|| $pagePermission == 6)
					{
						print("<input type=\"submit\" value=\"Update Info\" />");
					}
					else
					{
						print("<input type=\"submit\" value=\"Update Info\" disabled/>");
					}
				}
				echo '<a href="commonfiles/generate_vcard3.php?';
					echo "firstName=".$row['firstname'];
					echo "&lastName=".$row['lastname'];
					echo "&phoneNumber=".$row['phonenumber'];
					echo "&email=".$row['email'];
					echo "&company=".$row['company'];
					echo "&website=".$row['website'];
					echo "&street=".$row['street'];
					echo "&streetContinued=".$row['streetcontinued'];
					echo "&city=".$row['city'];
					echo "&state=".$row['state'];
					echo "&zipcode=".$row['zipcode'];
				echo '">Download Contact</a>';
			?>
		</form>
	</div>
</html>

<style>
#address
{
	border: 1px solid black;
}

#col-1
{
	float: left;
	right: 0px;
}

#tabs{
	min-width: 800px;
}
</style>