<?php
die('The administrator must uncomment this line for this script to run.');
/*
This script will run through all users.
If a user has user passwords, additional passwords, for the stat site, the main password is removed and a bit of cleaning is done.
This script must be run while the passwords still exists in clear text. It should not be harmfull to run it afterwards.
On the zipstat.dk server it runs in about 1000 users / second, but you can try to uncomment the $datafil->gemFil() line in order
to do a dry run.
*/
require "Html.php";
require "Stier.php";

$options = new Stier();
$mysqli = new mysqli($options->getOption('DB_hostname'), $options->getOption('DB_username'), $options->getOption('DB_password'), $options->getOption('DB_database'));

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$res = $mysqli->query("SELECT username FROM ".$options->getOption('DB_tablename_main'));
header('content-type: text/plain');
while ($row = $res->fetch_assoc()) {
  $username =  $row['username'];
  $datafil = DataSource::createInstance($username, $options);
  $resX = $datafil->hentFil();
  $userPass = $datafil->getLine(57);
  $userPass = str_replace("\r", "", $userPass);
  $passList = explode("::", $userPass);
  $password = $datafil->getLine(6);

  if (strlen($userPass) > 0) {
	  if (in_array($password, $passList)) {
	    array_splice($passList, array_search($password, $passList), 1);
	  }
	  if (in_array("1", $passList)) {
	    array_splice($passList, array_search("1", $passList), 1);
	  }
	  $passList = array_map("trim", $passList);
	  $passList = array_filter($passList);
	  $passList = array_unique($passList);
          $datafil->setLine(57, implode("::", $passList));
	  $datafil->gemFil();
	  echo "$username\n";
          
  }
}
?>
