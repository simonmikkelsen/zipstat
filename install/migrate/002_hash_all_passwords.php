<?php
die('The administrator must uncomment this line for this script to run.');
/*
This script will run through all users.
The script will hash all passwords which are in clear text.
This script must be run while the passwords still exists in clear text. It should not be harmfull to run it afterwards.
On the zipstat.dk server the script times out (it has 60 seconds to run),
but it can just be run several times untill it does not print usernames anymore.
to do a dry run.
*/
require "../../Html.php";
require "../../Stier.php";

$options = new Stier();
$mysqli = new mysqli($options->getOption('DB_hostname'), $options->getOption('DB_username'), $options->getOption('DB_password'), $options->getOption('DB_database'));

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$res = $mysqli->query("SELECT username FROM ".$options->getOption('DB_tablename_main') . " WHERE hash is null or hash = \"\"");
header('content-type: text/plain');
while ($row = $res->fetch_assoc()) {
  $username =  $row['username'];
  $datafil = DataSource::createInstance($username, $options);
  $resX = $datafil->hentFil();
  $password = $datafil->getLine(6);
  if ($password == "") {
    // Generate random password - then the user can do a reset.
    $b = openssl_random_pseudo_bytes(128);
    $password = base64_encode($b);
  }
  $authFactory = new AuthenticationFactory($options);
  $auth = $authFactory->create();
  if ($password !== '') {
    $auth->doAuthenticate($username, $password, $password);
    echo $username."\n";
    flush();
  }

}
?>
