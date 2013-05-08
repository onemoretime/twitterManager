<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Frameset//EN"
         "http://www.w3.org/TR/REC-html40/frameset.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type='text/javascript'>
		function updateFrame() {
			window.top.frames['status'].location.href='status.php';
		}
	</head>
         

<?php
	include_once('include/rootDefiner.php');
	include_once(SERVER_DOC_ROOT . 'TwitterManager\include\database.inc');
	
	ob_start();
	// addremove.php?action=add&screenname='tyjtyj'
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
	
	try {
		$dbconnect = new PDO('mysql:host='.$PARAM_host.';port='.$PARAM_port.';dbname='.$PARAM_dbname, $PARAM_dbuser, $PARAM_password);
    } catch(Exception $e) {
	        echo 'Error : ' . $e->getMessage() . '<br />';
	        echo 'Nmr : ' . $e->getCode();
	} 
	$results=$dbconnect->prepare("INSERT INTO TwitterUserName (idxTwitterUserName,screename) VALUES (?,?)" ); 
	try {
        $dbconnect->beginTransaction();
        $results->execute( array('', $screenname));
        $dbconnect->commit();
    } catch(PDOExecption $e) {
        $dbconnect->rollback();
        print "Error!: " . $e->getMessage() . "</br>";
    } 	
	
	$results->closeCursor();
	$dbconnect = null;
	
	// updating status page
	//echo "<script  type='text/javascript'>updateFrame();</script>";
	
	ob_flush();
	echo "<script>top.frames['status'].location.href='status.php'</script>";
	
?>
</html>