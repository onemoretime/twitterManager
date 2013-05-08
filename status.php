<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
         "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />	
		
		<title>
			
		</title>
	</head>
	<body>
		<div id='statusframe'>
		<center>Status Page</center>
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
			// ScreenName count
			$countresults = $dbconnect->query("SELECT idxTwitterUserName FROM TwitterUserName ORDER BY screename ASC");
			$countresults->fetchall();
			$countScreenName = $countresults->rowCount();
			$countresults->closeCursor();
			// Tweets count			
			$countresults = $dbconnect->query("SELECT tweets_idxTweets FROM TwitterUserName_has_tweets");
			$countresults->fetchall();
			$countTweets = $countresults->rowCount();
			$countresults->closeCursor();
			// tinyURL count
			$countresults = $dbconnect->query("SELECT idxtinyURL FROM tinyURL");
			$countresults->fetchall();
			$countTinyURL = $countresults->rowCount();
			$countresults->closeCursor();
			
			//DB size
			$dbsizeresult = $dbconnect->query("SHOW TABLE STATUS");
			$dbsize = 0;
			while($row = $dbsizeresult->fetch()) {
    			$dbsize += $row["Data_length"] + $row["Index_length"];
			}			
			
			echo "Registered Twitter Account: <br />";
			echo "ScreenName Followed: ".$countScreenName."<br />";
			echo "Tweets amount : " .$countTweets."<br />";
			echo "Known TinyUrl: ".$countTinyURL."<br />";
			echo "Private Messages sent to Twitter Accounts: <br />";
			echo "Tweets downloaded per day: <br />";
			echo "Database Size: ".round((($dbsize/1024)/1024),1)." Mo <br />";
			$dbconnect = null;
			
			/* Pour multiple jobs survey :
			 * Yes, it is doable. It is not a jQuery feature since it requires server-side integration. Here is a rough sketch on how to implement it:

    Have a database table (or other equivalent kinds of storage) that stores key-value pairs. It is simplest if both the keys and the values are strings.
    When the job is initiated, generate a unique string which is the key and value 0 which you store in the database table. The user is also redirected to an URL that contains that unique string. Eg. "/mysite/ERxQl3ew".
    Using jQuery, the users client should poll the url "/getkeyvalue/ERxQl3ew". This url should check the database and get the job status, say "45.3" to indicate that the job is 45.3% done. It's simplest if the page is a JSON string.
    The server should update the row with the key ERxQl3ew whenever the status or progress of that job changes.
    When the job is completed, the server could set the rows value to "final" to indicate that the job is done. When the client javascript sees that value, it should redirect the user to the final destination page or otherwise indicate to the user that the job is done.

Maybe it sounds more complicated than it really is. It is fairly trivial to implement.
			 * 
			 */
			?>
			
			
	</body>
</html>