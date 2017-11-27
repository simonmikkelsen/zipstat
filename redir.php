<html>
<body>
<h1>Vidersending...</h1>
<?php

function matchesPrefix($url, $haystack) {
    $url= strtolower($url);
    foreach ($haystack as $prefix) {
        if (strpos($url, $prefix) === 0) {
            return true;
        } 
    }
    return false;
}

if (isset($_POST["safeurl"]) and strlen($_SERVER["QUERY_STRING"]) === 0) {
	//This is post, and there is nothing to hide.
	
	$safeurl = $_POST["safeurl"];
	
	//Hidden, does the actual stuff.
	$html  = "<script language='javascript' type='text/javascript'>\n".
	$html .= "	<!--\n";
	$html .= "	location.href='".htmlentities(addslashes($safeurl))."';\n";
	$html .= "	//-->\n";
	$html .= "</script>\n";
	
	//For the user.
	$html .= "<p>Hvis du ikke bliver sendt automatisk videre, s tryk p linket:</p>\n";
	$html .= "<a href='".htmlentities($safeurl)."'>".htmlentities($safeurl)."</a></p>\n";
} else {
	$url = $_REQUEST["url"];
        $prefixes = array('http://zipstat.dk/', 'http://www.zipstat.dk/', 'https://zipstat.dk/', 'https://www.zipstat.dk/');

	if (! matchesPrefix($_SERVER['HTTP_REFERER'], $prefixes)) {
              die($_SERVER['HTTP_REFERER']."$host Fejlet sikkerhedstjek: Referer svarer ikke til zipstat.dk. P&aring; dansk: Hvis alt er godt kan du kun komme til denne side fra adressen zipstat.dk. Det ser ikke ud til at v&aelig; tilf&aelig;det og derfor kan det link du har klikket p&aring; v&aelig;re lavet til at franarre dig og misbruge personlige oplysninger. Hvad skal du g&oslash;re nu? Luk dit browservindue og kom aldrig tilbage til den side du var p&aring;. S&aring; b&oslash;r du v&aelig;re sikker.<br>Failed safety check: The website that sent you here may try to perform a phishing attach in order to gain and abuse your personal or financial information. What should you do? Close you web browser and never go back to that site. Then you are probably safe this time.");
	}
	//This is UNSAFE - redirect to the safe page.
	$html = "<p>Hvis du ikke bliver sendt automatisk videre, s tryk p knappen:</p>\n";
	$html .= "<form action='redir.php' method='POST' name='redirform'>\n";
	$html .= "<input type='hidden' name='safeurl' value='".htmlentities($url)."' />\n";
	$html .= "<p><input type='submit' value='".htmlentities($url)."' /></p>\n";
	
	$html .= "<script language='javascript' type='text/javascript'>\n";
	$html .= "	<!--\n";
	$html .= "	document.redirform.submit();\n";
	$html .= "	//-->\n";
	$html .= "</script>\n";
	
	$html .= "</form>\n";
}

echo $html;
?>
</body>
</html>
