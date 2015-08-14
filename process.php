<?php

require "Html.php";
require "Mstat.php";
require "Stier.php";
require "lib/ZipStatEngine.php";
include "lib/SiteContext.php";
include "lib/Localizer.php";
include "lib/Logger.php";

$stier = new Stier();

if (!isset($ind))
	$ind = array();

$datafil = NULL;
$lib = new Html($ind, $datafil);
$lib->setStier($stier);

$siteContext = new SiteContext($lib, $stier, $ind,'da');
$engine = new ZipStatEngine($lib);
$logger = new Logger($stier);
$logger->setProcessor($engine);
echo "start processign\n";
$logger->doProcess();
echo "end processing\n";
?>