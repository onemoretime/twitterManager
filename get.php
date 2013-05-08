<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
         "http://www.w3.org/TR/REC-html40/frameset.dtd">
<?php

	include_once('include/rootDefiner.php');
	include_once(SERVER_DOC_ROOT . 'TwitterManager\include\database.inc');
	
	// get.php?screenname='all'
	if ( (isset($_GET['screenname'])) or isset($_POST['screenname'])) {
		if (isset($_GET['screenname'])) {
			$screenname = $_GET['screenname'];
		}
		if (isset($_POST['screenname'])) {
			$screenname = $_POST['screenname'];
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
	
	while( $line = $results->fetch() ) {
		echo "<br />Downloading tweets from ".$line->screename." : \n";
		$_value = 2;
		$output = array();
		$i = 0;
		set_time_limit(0);
		while ($_value > 1) {
			exec("c:\Python27\python.exe Main.py ".$line->screename,$output,$return_value);
			$tempArr = explode(':',$output[0]);
			echo $tempArr[1]." tweets";
			$_value = $return_value;
		}
		flush();
	}
	$results->closeCursor();
	$dbconnect = null;
	set_time_limit(30);
	echo "<script>top.frames['status'].location.href='status.php'</script>";
?>
	
