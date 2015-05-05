<?php
	function getTwitterFeed($widgetid, $twitterhandle, $height)
	{
		print("<a class=\"twitter-timeline\" 
			href=\"https://twitter.com/\"".$twitterhandle."\" 
			data-widget-id=\"".$widgetid."\"
			width=\"700\"
			data-chrome=\"noheader nofooter\">
				Tweets by @".$twitterhandle."
			</a>");
		print("
			<script>
				!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\"://platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");
			</script>");	
	}
	
	function getchaptersettings()
	{
		$query = "
			SELECT *
			FROM chaptersettings
			WHERE(
				chaptersettings.idorganization = ".
				$_SESSION['user']['idorganization']."
					AND
				chaptersettings.idchapter = ".
				$_SESSION['user']['idchapter']."
				)
				;
			";

		try 
	 	{ 
			$stmt = $db->prepare($query); 
			$stmt->execute(); 
		} 
		catch(PDOException $ex) 
		{ 
		 	die("Failed to run query: " . $ex->getMessage()); 
	 	} 

		$chaptersettings = $stmt->fetch();
	}
	
	function generateContactCard($firstName, $lastName, $phoneNumber, $email, $company, $website,
									$street, $streetContinued, $city, $state, $zipcode)
	{
		$tempFile = tempnam("/tmp", "vCard.VCF");
		
		$handle = fopen($tempFile, "w");
		
		$text =
			"BEGIN:VCARD\n"
			."VERSION:2.1\n"
			."N:$lastName;$firstName\n"
			."FN:$firstName $lastName\n"
			."ORG:$company\n"
			."TEL;HOME;VOICE;PREF:$phoneNumber\n"
			."ADR;HOME;POSTAL;PARCEL;DOM;PREF:;;$street;$city;$state;$zipcode;United States of America\n"
			."EMAIL;INTERNET;PREF:$email\n"
			."URL;HOME;PREF:$website\n"
			."CATEGORIES:HOME\n"
			."END:VCARD\n";
			
			fwrite($handle, $text);
			fclose($handle);
		
			header('Content-type: text/vcard');
			header('Content-Disposition: attachment; filename="' . "$lastName, $firstName.vcf" . '"');
			header('Content-Transfer-Encoding: binary');
			readfile($tempFile);
	}
	
	function generateMemberCheckBoxTable($members, $columns)
	{	
		echo '
			<script language="JavaScript">
				function toggle(source) {
		  			checkboxes = document.getElementsByClassName("memberCheckBox");
		  			for(var i=0, n=checkboxes.length;i<n;i++) {
		    			checkboxes[i].checked = source.checked;
		  			}}
			</script>';
		
		print('<div class="memberCheckTable">');
		
		$width = $columns;
		echo "<table><tr>";
			
		foreach($members AS $member):
		{
			if($width == 0)
			{
				echo "</tr><tr>";
				$width = $columns;
			}
			print('<td><input type="checkbox" class="memberCheckBox" name="'.$member['localid'].'" value="1">'.$member['localid'].' - '.$member['firstname'].' '.$member['lastname'].'</td>');
			$width --;
		}
		endforeach;
		echo '</tr><tr><td><input type="checkbox" onClick="toggle(this)" /> Toggle All</td></tr>';
		echo "</table></div>";
	}
	
	function generateMemberSelectorBox($members, $readonly, $preselectedMember)
	{
		if($readonly)
			echo '<select name="memberselector" disabled>';
		else
			echo '<select name="memberselector">';

		foreach($members AS $member):
			if(intval($member['localid']) == intval($preselectedMember))
			{
				print("<option value=\"".$member['localid']."\" selected>");
				echo htmlentities($member['localid']." - ".$member['lastname'].", ".$member['firstname'], ENT_QUOTES, 'UTF-8');
				print("</option>");
			}
			elseif($_GET['idevent'] == '(new)' && intval($member['localid']) == intval($_SESSION['user']['localid']))
			{
				print("<option value=\"".$member['localid']."\" selected>");
				echo htmlentities($member['localid']." - ".$member['lastname'].", ".$member['firstname'], ENT_QUOTES, 'UTF-8');
				print("</option>");
			}
			else
			{
				print("<option value=\"".$member['localid']."\">");
				echo htmlentities($member['localid']." - ".$member['lastname'].", ".$member['firstname'], ENT_QUOTES, 'UTF-8');
				print("</option>");
			}
		endforeach; 
		echo '</select>';
		echo '<br />';
	}
	
	function isMobile() {
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
?>