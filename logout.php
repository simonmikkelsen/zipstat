<?php

require_once(dirname(__FILE__)."/Stier.php");
require_once(dirname(__FILE__)."/lib/session.php");

$options = new Stier();
$sessionFactory = new SessionFactory($options);
$session = $sessionFactory->create();
$session->closeSession();
header("Location: ".$options->getOption('ZSHomePage'));
?>
