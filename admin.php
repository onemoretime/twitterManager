<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
         "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="jquery-2.0.0.min.js"></script>
		<script type='text/javascript'>
		    /*window.onload = setupRefresh;
		    function setupRefresh()
		    {
		        setInterval("refreshBlock();",30000);
		    }*/
		    
		    function refreshBlock(idblock,url)
		    {
		       $('#'+idblock).load(url);
		    }		
				
			showOrHide = 0;
			function Toggle(id) {
				$('#' + id).toggle(showOrHide);
			}
			function Get(id){
				// Get All Tweets : id=AllTweets
				$('#loadingmessage'+id).show();  // show the loading message.
				$.ajax({
				    //url: "get.php?screenname='all'",
				    url : "current.php?action=get&screenname=all",
				    type: "POST",
				    cache: false,
				    //data: "&page="+ url,
				    //dataType: "html",			    
				    success : function(html){
				    	$("#log"+id).append(html);
				    	$("#log"+id).show();
				        
				        $('#loadingmessage'+id).hide(); // hide the loading message
				    },
			   });
			}
			function Add(id) {
				screenNameToAdd = $("input:text[name='ScreenNameToAdd']").val();
				$.ajax({
				    //url: "get.php?screenname='all'",
				    url : "addremove.php?action=add&screenname='"+screenNameToAdd+"'",
				    type: "POST",
				    cache: false,
				    async: false,
				    data: {screenname: screenNameToAdd},		    
				    success : function(){
				    	$("Success"+id).show();
				    },
			   });
			   //refreshBlock(statusframe,'status.php');
			}
		</script>		
		<title>
			
		</title>
	</head>
	<body>
		<center><h1>Page d'admin</h1></center>
		<br />
		<br />
		<?php
		include_once('include/rootDefiner.php');
		include_once(SERVER_DOC_ROOT . 'TwitterManager\include\database.inc');		
			
			try {
				$dbconnect = new PDO('mysql:host='.$PARAM_host.';port='.$PARAM_port.';dbname='.$PARAM_dbname, $PARAM_dbuser, $PARAM_password);
		    } catch(Exception $e) {
			        echo 'Error : ' . $e->getMessage() . '<br />';
			        echo 'N° : ' . $e->getCode();
			} 	
		
			if ( (isset($_GET['function'])) or isset($_POST['function'])) {
				if (isset($_GET['function'])) {
					$function = $_GET['function'];
				}
				if (isset($_POST['function'])) {
					$function = $_POST['function'];
				}
			}			
		/*<!--		
		Add a twitter account to use for private screenname<br />
		Add a ScreenName to follow, and bind it to an account (if necessary) - Need to check <br />
		Delete one or more ScreenName <br />
		Dump a ScreenName Env <br />
		Update a dump of a screnName <br />

		Dumping a ScreenName Env includes:
			image
			Fullname
			bio
			Location
			URL
		For example :
		In https://twitter.com/climagic Content: 
		<img class="avatar size73" alt="Command Line Magic" src="https://si0.twimg.com/profile_images/535876218/climagic-icon_bigger.png"> 
		<h1 class="fullname editable-group">
			<span class="profile-field">Command Line Magic</span>
		</h1>
		
		<div class="bio-container editable-group">
			<p class="bio profile-field">Cool Unix/Linux Command Line tricks you can use in 140 characters or less.</p>
		</div>
		<p class="location-and-url">
			<span class="location-container editable-group">
				<span class="location profile-field"> BASHLAND </span>
			</span>
			<span class="divider">·</span>
			<span class="url editable-group">
				<span class="profile-field">
					<a href="http://www.climagic.org/" rel="me nofollow" target="_blank"> http://www.climagic.org/ </a>
				</span>
			</span>
		</p>	
		-->*/
		
		/************************** Schedule automatic actions ***********************************/
		echo "<a href='#ScheduleActions' id='hrefScheduleActions' onclick='Toggle(\"ScheduleActions\");'> Schedule Automatic Actions </a><br />
			  <div id='ScheduleActions' style='display:none'>";
			/************************** Update a dump of a ScreenName Env ***********************************/
			echo "<li>Update a dump of a ScreenName Env</li>";
			echo "<li>Schedule download of tweets: by screenname selection/ all. </li>";
			echo "<li>Check private msgs for all accounts/ all. </li>";
		echo "</div>\n";
		/************************** Private Msgs ***********************************/
		echo "<a href='#PrivateMsg'> View Private Messages </a><br />";		
		/************************** Trigger an immediate tweets download ***********************************/
		echo "<a href='current.php?action=get&screenname=all'' id='hrefGetAllTweets' target=current>Download ALL tweets for ALL ScreenName NOW !</a><br />";
		/************************** Add a twitter Account ***********************************/
		echo "<a href='#AddAccount' id='hrefAddAccount' onclick='Toggle(\"AddAccount\");'> Add a twitter Account </a><br />
			  <div id='AddAccount' style='display:none'><br />";
		echo "Add @<input id='AccountToAdd' name='AccountToAdd' type='text'><br />";
		echo "Password : <input id='PasswordToAdd' name='PasswordToAdd' type='text' value=''><br />";
		echo "<input id='validAddAcc' type='button' value='Add' onclick='Add(\"Account\");' />";
		echo "</div>\n";
		/************************** Add a ScreenName ***********************************/
		echo "<a href='#AddScreenName' id='hrefAddScreeName' onclick='Toggle(\"AddScreenName\");'> 
		     Add a ScreenName to follow and link it with a twitter Account</a></br />
		      <div id='AddScreenName' style='display:none'><br />";
		echo "Add @<input id='ScreenNameToAdd' name='ScreenNameToAdd' type='text' value=''><br />";
		echo "<input id='validAddScreenName' type='button' value='Add' onclick='Add(\"ScreenName\");' />";
		echo "</div>\n";
		echo "<div id='SuccessAddScreenName' style='display:none'>";
		echo "ScreenName Added.";
		echo "</div>";
		/************************** Delete one or more ScreenName ***********************************/
		echo "Delete one or more ScreenName<br />";
		/************************** Dump one or more ScreenNames Env ***********************************/
		echo "<a href='#DumpEnvAccount' id='hrefDumpEnvAccount' onclick='Toggle(\"DumpEnvAccount\");'> Dump one or more ScreenNames Env </a><br />
			 <div id='DumpEnvAccount' style='display:none'><br />";
		$results=$dbconnect->query("SELECT idxTwitterUserName,screename FROM TwitterUserName ORDER BY screename ASC"); 
		$results->setFetchMode(PDO::FETCH_OBJ);
		echo "<select multiple name='DumpEnvAccount[]'>\n";
		while( $line = $results->fetch() ) {
			echo "<option value='".$line->idxTwitterUserName."'";
			echo ">".$line->screename."</option>\n";
		}
		$results->closeCursor();
		echo "</select><br />\n";
		echo "<input id='validDumpValidAccount' type='button' value='Dump' onclick='Dump(\"Account\");' />";
		echo "</div>\n";


	
	$dbconnect = null;
	?>
		
	</body>
</html>