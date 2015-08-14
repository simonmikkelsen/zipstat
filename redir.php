<html>
<body>
<h1>Vidersending...</h1>
<?php

if (isset($HTTP_POST_VARS["safeurl"]) and strlen($_SERVER["QUERY_STRING"]) === 0) {
	//This is post, and there is nothing to hide.
	
	$safeurl = $HTTP_POST_VARS["safeurl"];
	
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