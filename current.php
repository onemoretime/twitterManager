<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
         "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="jquery-2.0.0.min.js"></script>
		<script type='text/javascript'>
			showOrHide = 0;
			function Toggle(id) {
				$('#' + id).toggle(showOrHide);
			}	
			function Get(id){
				// Get All Tweets : id=AllTweets
				$('#loadingmessage'+id).show();  // show the loading message.
				$.ajax({
				    url : "get.php?screenname='all'",
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
		</script>		
		<title>
			Current tasks page.
		</title>
	</head>
	<?php
	
	include_once('include/rootDefiner.php');
	include_once(SERVER_DOC_ROOT . 'TwitterManager\include\database.inc');
		
	$function = array();
	$paramFunction = array();
	$action = "";
	$screenname = "";
	
	
	if ( (isset($_GET['action'])) or isset($_POST['action'])) {
		if (isset($_GET['action'])) {
			$action = $_GET['action'];
		}
		if (isset($_POST['action'])) {
			$action = $_POST['action'];
		}
	}	
	if ( (isset($_GET['screenname'])) or isset($_POST['screenname'])) {
		if (isset($_GET['screenname'])) {
			$screenname = $_GET['screenname'];
		}
		if (isset($_POST['screenname'])) {
			$screenname = $_POST['screenname'];
		}
	}	
	
	// Set scripts to load while page is loading
	if ($action == "get") {
		$function[] = "Get";
		if ($screenname == "all") {
			$paramFunction[] = "AllTweets";
		}
	}
	echo "<body ";
	if ( (sizeof($function) > 0) and (sizeof($paramFunction) > 0)) {
		echo "onload='".$function[0]."(\"";
		echo $paramFunction[0]."\");'";
	}

	echo ">";
	
	
	echo "<center>Current Task </center>";
	echo "<center>Find a trick to update to update/add current tasks without reloading this page or all frontend</center>";
	echo"<div id='loadingmessageAllTweets' align='center' style='display:none'>
		 Downloading all tweets for all known ScreenName<br><center>
		 <img src='./images/ajax_waiting.gif' width='10%' height='10%'/></center>
		 </div>\n			
		<div id='logAllTweets' style='display:none'>
				Result : xxx tweets downloaded for yy known ScreenName.	
		</div>\n";
		
		
		?>
	</body>
</html>