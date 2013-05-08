<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Twitter Downloader Manager</title>
        <script type="text/javascript" src="jquery-2.0.0.min.js"></script>
		<script type='text/javascript'>
			var loaded = false;		
			function getData(url,id){
				
				$('#loadingmessage'+id).show();  // show the loading message.   
				$.ajax({
				    url: "urlsanitizer.php?url='"+url+"'",
				    type: "POST",
				    cache: false,
				    //data: "&page="+ url,
				    //dataType: "html",			    
				    success : function(html){
				    	$("#log"+id).append(html);
				    	$("#log"+id).show();
				        
				        $('#loadingmessage'+id).hide(); // hide the loading message
				    },
				    error: function (xhr, status) { 
				        $('#loadingmessage'+id).hide(); // hide the loading message
					} 
			   });
			}
		</script>
    </head>
    <body>
        <h1>Twitter Downloader Manager</h1>

<?php
	include_once('include/rootDefiner.php');
	include_once(SERVER_DOC_ROOT . 'TwitterManager\include\database.inc');
	
	$count = 0;
	$screename ="";
	$strscreename ="";
	$from = 0;
	$nbr = 0;
	$maxtweet = 0;
	// Set parameters send by  GET or POST 
	if ( (isset($_GET['screename'])) or isset($_POST['screename'])) {
		if (isset($_GET['screename'])) {
			$screename = $_GET['screename'];
		}
		if (isset($_POST['screename'])) {
			$screename = $_POST['screename'];
		}
	}
	if ( (isset($_GET['nbr'])) or isset($_POST['nbr'])) {
		if (isset($_GET['nbr'])) {
			$nbr = $_GET['nbr'];
		}
		if (isset($_POST['nbr'])) {
			$nbr = $_POST['nbr'];
		}
	}
	if ( (isset($_GET['from'])) or isset($_POST['from'])) {
		if (isset($_GET['from'])) {
			$from = $_GET['from'];
		}
		if (isset($_POST['from'])) {
			$from = $_POST['from'];
		}
	}		

	
	
	
	try {
		$dbconnect = new PDO('mysql:host='.$PARAM_host.';port='.$PARAM_port.';dbname='.$PARAM_dbname, $PARAM_dbuser, $PARAM_password);
    } catch(Exception $e) {
	        echo 'Erreur : ' . $e->getMessage() . '<br />';
	        echo 'N° : ' . $e->getCode();
	} 
	$results=$dbconnect->query("SELECT idxTwitterUserName,screename FROM TwitterUserName ORDER BY screename ASC"); 
	$results->setFetchMode(PDO::FETCH_OBJ);
	echo '<select name="screename" onChange="var value = this.value; if (value != \'\' ) window.location.href = \'./viewer.php?screename=\' + value;">\n';
	if ( $screename == "") {
		echo "<option value='0' selected='selected'>Select a Screen Name</option>\n";
	} else {
		echo "<option value='0'>Select a Screen Name</option>\n";
	}
	while( $line = $results->fetch() ) 
	{
		echo "<option value='".$line->idxTwitterUserName."'";
		if ($screename == $line->idxTwitterUserName) {
			$strscreename = $line->screename;
			echo " selected='selected'";
		}
		echo ">".$line->screename."</option>\n";
	}
	echo "</select><br />\n";
	
	if ( $screename != "" and $screename != 0) {
		
		echo "<Form Name ='form1' Method ='POST' ACTION = 'viewer.php?screename=".$screename."&from=".$from."&nbr=".$nbr."'>\n";
		echo "View <input name='nbr' type='text' value='";
		if ($nbr != 0) {
			echo $nbr;
		} else {
			echo "30";
		}
		echo "' size='5'/> first tweets";
		$maxtweet = countTweets($_GET['screename']);
		echo " (".$maxtweet." max)<br />\n";
	}
	echo "<br />\n";

	
	echo "<input type='submit' value='View' />\n";
	echo "<input type='submit' value='Update' />\n";
	echo "</form>\n";

	if ($nbr != 0) {
		echo "<br /><hr /><br />\n";

		echo "<p align='center' style='font-size:20px'> Tweets from <b>".$strscreename."</b> </p> \n";

		echo "<TABLE BORDER=0><TR>\n";
		if ($from == 0) {
			echo "<TD width='100%'>&nbsp;</TD>\n";
		} else {
			echo "<TD width='100%'><a href='viewer.php?screename=".$screename."&from=".($from-$nbr)."&nbr=".$nbr."'>Previous</TD>\n";
		}

		echo "<TD>&nbsp;</TD>\n";
		if ($from+$nbr > $maxtweet) {
			echo "<TD width='100%'>&nbsp;</TD>\n";
		} else {
			echo "<TD width='100%'><a href='viewer.php?screename=".$screename."&from=".($from+$nbr)."&nbr=".$nbr."'>Next</TD>\n";
		}

		echo "</TR></TABLE>\n";
		echo "<TABLE BORDER='1'> 
		  
		  <TR>  
		 <TH> Date </TH> 
		 <TH> Content </TH> 
		 <TH> has URL ? </TH> 
		  </TR> "; 
		$from = getTweets($screename,$nbr,$from);
		  echo "		  
		</TABLE>";
		echo "<TABLE BORDER=0><TR>\n";
		if ($from == 0) {
			echo "<TD width='100%'>&nbsp;</TD>\n";
		} else {
			echo "<TD width='100%'><a href='viewer.php?screename=".$screename."&from=".($from-$nbr)."&nbr=".$nbr."'>Previous</TD>\n";
		}

		echo "<TD>&nbsp;</TD>\n";
		if ($from + $nbr> $maxtweet) {
			echo "<TD width='100%'>&nbsp;</TD>\n";
		} else {
			echo "<TD width='100%'><a href='viewer.php?screename=".$screename."&from=".($from+$nbr)."&nbr=".$nbr."'>Next</TD>\n";
		}

		echo "</TR></TABLE>\n"; 		
		//$arrHeaders = get_headers("http://t.co/fFxHR6r2xt",1);
		//print_r($arrHeaders['Location']);
		$results->closeCursor();
	}
	$dbconnect = null;
	
	function countTweets($usernameid) {
		return getTweets($usernameid,0,0);
    }	

	function getTweets($userNameId,$nbr=0,$from=0) {
		global $dbconnect;
		global $maxtweet;
		if ($nbr == 0) {
			$countresults = $dbconnect->query("SELECT tweets_idxTweets FROM TwitterUserName_has_tweets WHERE TwitterUserName_idxTwitterUserName=".$userNameId);

			$countresults->fetchall();
			$count = $countresults->rowCount();
			$countresults->closeCursor();
			return $count;
		} else {
			if (($from+$nbr) > $maxtweet) {
				$increment = $maxtweet - $from;
			} else {
				$increment = $nbr;
			}
			echo "<br />";
			//$resultats = $connexion->query("SELECT tweets_idxTweets FROM TwitterUserName_has_tweets WHERE TwitterUserName_idxTwitterUserName=".$userNameId." LIMIT ".$from.",".$increment);
			$results = $dbconnect->query("SELECT idtweet,tweetContent,tweetDate
					FROM tweets
					WHERE idxTweets IN
						( SELECT tweets_idxTweets
          					FROM TwitterUserName_has_tweets
          					WHERE TwitterUserName_idxTwitterUserName = ".$userNameId."
        				)
						ORDER BY tweets.tweetDate DESC
						LIMIT ".$from.",".$increment);
						
			for ($i=$from;$i < $from + $increment;$i++) {
				$tweet =  $results->fetch();
				//$atweet = $connexion->query("SELECT idtweet,tweetContent,tweetDate from tweets WHERE idxTweets=".$idxtweet[0]);
				//$tweet = $atweet->fetch();
				echo "
				 <TR> 
				 <TD><p style='font-size:10px'> ".$tweet[2]." </p></TD> 
				 <TD><p style='font-size:15px'> ".stripslashes($tweet[1])." </p></TD> 
				 <TD><p align='center' style='font-size:10px'> ".hasURL($tweet[1],$i)." </p></TD> 
				 </TR>";
				//$atweet->closeCursor(); 
			}
			$results->closeCursor();
			$from = $from + $nbr;
			return $from;			
		}
	}
	
	function hasURL($text,$id) {
		$url = "";
		$code ="";
		$text = stripslashes($text);
		$result = preg_match('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@',$text,$found);
		if (($result == true) or ($result == 1)) {
			$url = $found[0];
			if  ( ( strpos($url,'http') === FALSE) and (strpos($url,'ftp') ===FALSE)) {
				$code = "\n";	
			} else {
				$code = $url."<div id='loadingmessage".$id."' align='center' style='display:none'><img src='./images/ajax_waiting.gif' width='10%' height='10%'/></div>\n
						
						<div id='log".$id."' style='display:none'></div>\n
						<script type='text/javascript'> getData('".$url."'".",".$id.")</script>\n
						";
			}
		}
		return $code;
	}
	

	
	function strstr_after($haystack, $needle, $case_insensitive = false) {
	    $strpos = ($case_insensitive) ? 'stripos' : 'strpos';
	    $pos = $strpos($haystack, $needle);
	    if (is_int($pos)) {
	        return substr($haystack, $pos + strlen($needle));
	    }
	    // Most likely false or null
	    return $pos;
	}	
	
?>


    </body>
</html>
