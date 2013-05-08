<?php

	include_once('include/rootDefiner.php');
	include_once(SERVER_DOC_ROOT . 'TwitterManager\include\database.inc');
	
	$arrURL = array();
	$isRedirected = FALSE;
	
	try {
		$dbconnect = new PDO('mysql:host='.$PARAM_host.';port='.$PARAM_port.';dbname='.$PARAM_dbname, $PARAM_dbuser, $PARAM_password);
    } catch(Exception $e) {
	        echo 'Error : ' . $e->getMessage() . '<br />';
	        echo 'N° : ' . $e->getCode();
	} 

	
	
	
	$url = "";
	$sanitized_url ="";
	if ( (isset($_GET['url'])) or isset($_POST['url'])) {
		if (isset($_GET['url'])) {	 
			$url = str_replace(array('\'', '"'), '', $_GET['url']);
		}
		if (isset($_POST['url'])) {
			$url = str_replace(array('\'', '"'), '', $_POST['url']);
		}
	}
	// Si on a une short URL, la convertir et mettre à jour la table des tinyurl
	// si c'est une image, la downloader
	// proposer de traiter l'url (sanitization des swf, ...)
	
	if (isTinyURL($url)) {
			sleep(5 + rand(0,5));
			//echo "<br />".$url."<br />";
			$arrURL = getRealURL($url);
			$isRedirected = TRUE;
	}
	
	
	// end of checks
	if ($isRedirected) {
		foreach ($arrURL as $itemurl) {
			echo "<p align='center' style='font-size:10px'>".$itemurl."<br /></p>";
		}
	} else {
		echo "<p align='center' style='font-size:10px'>NO REDIRECTION<br /></p>";
	}
	$dbconnect = null;
	
	//echo print_r(get_headers('http://bit.ly/11oNWsm'));
	/************************************************************************************/

	function isTinyURL($url) {
		global $dbconnect;
		$boolValue = FALSE;
		$results=$dbconnect->query("SELECT fqdnTinyURL FROM tinyURL ORDER BY fqdnTinyURL ASC"); 
		$results->setFetchMode(PDO::FETCH_OBJ);
		while( $line = $results->fetch() ) 
		{
			if (strpos($url,$line->fqdnTinyURL) !== FALSE) {
				$boolValue = TRUE;
				break;
			}
		}		
		//echo "<br /> Found a tinyURL :".$url."<br />";
		$results->closeCursor();
		return $boolValue;
	}
	
	function getRealURL($thisurl) {
		// return an array of locations
		$arrLocation = array();
		$tempURL = $thisurl;
		while (TRUE) {
			//echo "<br /> URL Passe : ".$tempURL."<br />";
			try {
				$arrHeaders = get_headers($tempURL);
			} catch(Execption $e) {
				echo "Unable to get Headers : ".$e."<br />";
			}
			if ($arrHeaders === FALSE) {
				$arrLocation[] = 'unavailable';
				break;
			}
			//echo "<br /> Les Headers : ".print_r($arrHeaders)."<br />";
			$boolRedirection = hasRedirection($tempURL,$arrHeaders);
			if ($boolRedirection) {
				//echo "<br /> l'URL ".$tempURL." possede une redirection<br />";
				  // l'url passe offre une redirection
				$redirectedURL = getRelocation($arrHeaders);
				updateTinyURL($tempURL,$redirectedURL);
				//echo "<br /> elle est redirige vers ".$tempURL."<br />";
				$arrLocation[] = $redirectedURL;
				$tempURL = $redirectedURL;		
				//echo "<br />Le tableau des Locations est : ".print_r($arrLocation)."<br />";
			} else {
				break;
			}

		}
		//echo "<br />Le tableau final des Locations est : ".print_r($arrLocation)."<br />";
		return $arrLocation;
	}
	
	function updateTinyURL($urlRedirecting,$urlRedirected) {
		global $dbconnect;
		$fqdnRedirecting = extractFQDN($urlRedirecting);  // url we came from
		$fqdnRedirected = extractFQDN($urlRedirected);   // url we are going to
		if ( ($fqdnRedirected != $fqdnRedirecting) and (! isTinyURL($fqdnRedirecting)) and (! ($fqdnRedirecting = '') ) ) {
			// Il ne s'agit pas d'une redirection de interne
			// et le fqdn d'origine n'est pas dans la base. On met à jour la base.
			//echo "<br /> On doit mettre à jour la base<br />";
			$results=$dbconnect->prepare("INSERT INTO tinyURL (idxtinyURL,fqdnTinyURL) VALUES (?,?)" ); 
			try {
		        $dbconnect->beginTransaction();
		        $results->execute( array('', $fqdnRedirecting));
		        $dbconnect->commit();
		    } catch(PDOExecption $e) {
		        $dbconnect->rollback();
		        print "Error!: " . $e->getMessage() . "</br>";
		    } 
		}// else {
		//	echo "<br />Pas besoin de mettre à jour la base des tinyURL.<br />";
		//}
	}
	
	function extractFQDN($uri){
		$fqdn = '';
		if (stripos($uri,'https://') !== FALSE ){
			$fqdn = strstr_after($uri, 'https://',TRUE);
		} else {
			$fqdn = strstr_after($uri, 'http://',TRUE);
		}
		$fqdn = substr($fqdn,0,strpos($fqdn,'/'));
		//echo "<br /> FQDN : ".$fqdn."<br />";
		return $fqdn;		
	}
	
	function getRelocation ($arrHeaders) {
		$strLocation = "";
		foreach ($arrHeaders as $itemHeaders) {
			if (stripos($itemHeaders,'location:') !== FALSE) { //Recupération de l'url
				$strLocation = strstr_after($itemHeaders,'location: ',true);
				break;
			}
		}		
		return $strLocation;
	}
	
	function hasRedirection($url,$arrHeaders) {
		$boolValue = FALSE;
		foreach ($arrHeaders as $itemHeaders) {
			if (stripos($itemHeaders,strtolower('301 Moved')) !== FALSE) {
				//echo "<br>Found a Redirection<br />"; 
				$boolValue = TRUE;
				break;
			}
		}

		return $boolValue;
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