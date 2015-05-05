<?
	include("commonfiles/functions.php");
	
	if(!isMobile())
	{
		$query = "
				SELECT googlecalendarembedcode
				FROM chapters
				WHERE (idchapter = ".$_SESSION['user']['idchapter'].");";

		try 
		{ 
			$stmt = $db->prepare($query); 
			$stmt->execute(); 
		} 
		catch(PDOException $ex) 
		{ 
			die("Failed to run query: " . $ex->getMessage()); 
		} 

		$apiData = $stmt->fetch();
		echo $apiData['googlecalendarembedcode'];
	}
?>
		
<link rel="stylesheet" type="text/css" href="https://s3.amazonaws.com/greekdb/resources/css/navigationbanner.css">	
<h3 style="margin-bottom: 0px;">Member Tools</h3>
<section class="membertoolslist">
	<?
		if($_SESSION['user']['pdirectory'] > 0)
		{
			echo "<a href=\"javascript:displayMyProfile()\">My Profile</a><br />";
		}
		if($_SESSION['user']['pservice'] > 0 && $_SESSION['user']['pservice'] < 5)
			echo "<a href=\"chapter_service_list.php\">My Community Service</a><br />";
		if($_SESSION['user']['pjudicial'] > 0 && $_SESSION['user']['pjudicial'] < 5)
			echo "<a href=\"judicial_case_list.php\">My Judicial Cases</a><br />";
		if($_SESSION['user']['pdirectory'] < 5  && $_SESSION['user']['pdirectory'] > 0)
			echo "<a href=\"chapter_directory.php\" >Chapter Directory</a><br />";
	?>
</section>

<h3 style="margin-bottom: 0px;">Officer Tools</h3>
<section class="officertoolslist">
	<?
		if($_SESSION['user']['pdirectory'] > 4)
			echo "<a href=\"chapter_directory.php\" >Directory</a><br />";
		if($_SESSION['user']['pservice'] > 4)
			echo "<a href=\"chapter_service_list.php\">Service List</a><br />";
		if($_SESSION['user']['pjudicial'] > 4)
			echo "<a href=\"judicial_case_list.php\">Judicial Cases</a><br />";
		if($_SESSION['user']['pofficerpermissions'] > 4)
			echo "<a href=\"officer_permissions_manager.php\">Officer Permissions</a><br />";
		if($_SESSION['user']['pmasscommunication'] > 0)
			echo "<a href=\"announcer.php\">Mass-Announcer</a><br />";
		if($_SESSION['user']['pbilling'] > 4)
		{
			echo "<br /><i><b>Treasurer Tools</b></i><br />";
			echo "<a href=\"open_accounts.php\">Open Accounts</a><br />";
			echo "<a href=\"javascript:displayEasyBiller()\">Easy-Biller</a><br />";
		}
	?>
</section>

<h3 style="margin-bottom: 0px;">Reports</h3>
<section class="reportslist">
	<?
		if($_SESSION['user']['pservice'] > 3 || $_SESSION['user']['pservice'] == 2)
			echo "<a href=\"javascript:displayMemberServiceReport()\">Service Totals</a><br />";

		if($_SESSION['user']['pjudicial'] > 3 || $_SESSION['user']['pjudicial'] == 2)
			echo "<a href=\"javascript:displayJudicialPunishmentsReport()\">Punishment Totals</a><br />";

		if($_SESSION['user']['pjudicial'] > 3)
			echo "<a href=\"javascript:displayCaseList()\">Recent Cases</a><br />";

		if($_SESSION['user']['pbilling'] > 4)
			echo "<a href=\"javascript:displayAccountsReceivable()\">Accounts Receivable</a><br />";
		
		if($_SESSION['user']['pdirectory'] > 3 || $_SESSION['user']['pdirectory'] == 2)
			echo "<a href=\"javascript:displayRoster()\">Chapter Roster</a><br />";
		
		if($_SESSION['user']['pdirectory'] > 2)
			echo "<a href=\"familytree.php#".$_SESSION['user']['localid']."\" >Lineage Tree</a><br />";
	?>
</section>


<script>
	function displayEasyBiller()
	{
	    window.open("easybiller.php", "_blank", "toolbar=no, scrollbars=no, top=100, left=200, width=820, height=294");
	}

	function displayMyProfile()
	{
	    window.open("memberinfo.php?localid=<?echo $_SESSION['user']['localid']?>",
			"_blank",
			"toolbar=no, scrollbars=no, top=20, left=200, width=833, height=500");
	}

	function displayQuickCase()
	{
	    window.open("quick_case_creator.php", "_blank", "toolbar=no, scrollbars=no, resizable=no, top=100, left=200, width=410, height=170");
	}
	
	function displayMemberServiceReport()
	{
		var date = new Date();
	    window.open("reports/communityservice/memberservicetotals.php?startdate="
		+(date.getFullYear()-1)+"-"+("0"+(date.getMonth()+1)).substr(-2,2)+"-"+("0"+date.getDate()).substr(-2,2)
		+"&enddate="+date.getFullYear()+"-"+("0"+(date.getMonth()+1)).substr(-2,2)+"-"+("0"+date.getDate()).substr(-2,2),
			"_blank",
			"toolbar=no, scrollbars=no, top=100, left=200, width=660, height=480");
	}
	
	function displayJudicialPunishmentsReport()
	{
		var date = new Date();
	    window.open("reports/judicialcases/punishment_totals.php?startdate="
		+(date.getFullYear()-1)+"-"+("0"+(date.getMonth()+1)).substr(-2,2)+"-"+("0"+date.getDate()).substr(-2,2)
		+"&enddate="+date.getFullYear()+"-"+("0"+(date.getMonth()+1)).substr(-2,2)+"-"+("0"+date.getDate()).substr(-2,2),
			"_blank",
			"toolbar=no, scrollbars=no, top=100, left=200, width=660, height=480");
	}
	
	function displayCaseList()
	{
		var date = new Date();
	    window.open("reports/judicialcases/case_list.php?startdate="
		+(date.getFullYear())+"-"+("0"+(date.getMonth())).substr(-2,2)+"-"+("0"+date.getDate()).substr(-2,2)
		+"&enddate="+date.getFullYear()+"-"+("0"+(date.getMonth()+1)).substr(-2,2)+"-"+("0"+date.getDate()).substr(-2,2),
			"_blank",
			"toolbar=no, scrollbars=no, resizable=no, top=100, left=200, width=660, height=480");
	}
	
	function displayAccountsReceivable()
	{
	    window.open("reports/financial/accounts_receivable.php",
			"_blank",
			"toolbar=no, scrollbars=no, resizable=no, top=100, left=200, width=660, height=480");
	}
	
	function displayRoster()
	{
	    window.open("reports/directory/roster.php",
			"_blank",
			"toolbar=no, scrollbars=no, resizable=no, top=100, left=200, width=700, height=480");
	}
</script>