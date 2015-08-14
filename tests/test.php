<html>
<body>
<h1>ZIP Stat unit tests</h1>
<?php

require_once(dirname(__FILE__)."/testcase.php");

require_once(dirname(__FILE__)."/cases/dateformatter.php");
require_once(dirname(__FILE__)."/cases/legacydateparser.php");
require_once(dirname(__FILE__)."/cases/urlcomparator.php");
require_once(dirname(__FILE__)."/cases/EventCalculator.php");
require_once(dirname(__FILE__)."/cases/agentparsertest.php");
require_once(dirname(__FILE__)."/cases/arrayrotatetest.php");
require_once(dirname(__FILE__)."/cases/pathinfoparsertest.php");

	$tests = array();
	$tests[] = new DateFormatterTest();
	$tests[] = new LegacyDateParserTest();
	$tests[] = new UrlComparatorTest();
	$tests[] = new EventCalculatorTest();
	$tests[] = new AgentParserTest();
	$tests[] = new ArrayRotateTest();
	$tests[] = new PathInfoParserTest();
	
	//Testing
	for ($i = 0; $i < count($tests); $i++) {
		echo $tests[$i]->getName().": ";
		if ($tests[$i]->test() === TRUE) {
			echo "ok";
			echo "<br />\n";
		} else {
			echo "<br />\n<span style=\"color: red; margin-buttom: 1.5em;\">FAILED</span><br />\n";
		}
	}

?>
</body>
</html>