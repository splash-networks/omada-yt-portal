<?php

require 'header.php';
include 'config.php';

########### Set the following parameters according to your environment ###########

// IP, port and ID of Omada Controller

$controllerIP = '24.144.83.81';
$controllerPort = '8043';
$controllerID = 'a4d3107367bfe1c7133895cd766b1333';

// Time duration in ms for which the client will be authorized on the network

$seconds = 3600000;

// Username/password of operator (created in Hotspot Manager => Operators)

$username = 'operator1';
$password = 'operator1';

########### The code below this line does not need to be modified ###########

$clientMac = $_SESSION["clientMac"];
$apMac = $_SESSION["apMac"];
$ssidName = $_SESSION["ssidName"];
$t = $_SESSION["t"];
$radioId = $_SESSION["radioId"];
$site = $_SESSION["site"];

$curl = curl_init();

$postData = [
  "name" => $username,
  "password" => $password
];

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://'.$controllerIP.':'.$controllerPort.'/'.$controllerID.'/api/v2/hotspot/login',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_COOKIEFILE => '',
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode($postData),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

if ($response !== false) {
  $json = json_decode($response, true);
  $csrfToken = $json['result']['token'];
}
else {
  die("Error: check with your network administrator");
}

$postData2 = [
  "clientMac" => $clientMac,
  "apMac" => $apMac,
  'ssidName' => $ssidName,
  'radioId' => $radioId,
  'authType' => 4,
  'time' => $seconds
];

$url = 'https://'.$controllerIP.':'.$controllerPort.'/'.$controllerID.'/api/v2/hotspot/extPortal/auth';

curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_SSL_VERIFYHOST => false,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => json_encode($postData2),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Csrf-Token: ' . $csrfToken,
  ),
));

$res = curl_exec($curl);

curl_close($curl);

if ($res !== false) {
  $json = json_decode($res, true);
  $code = $json['errorCode'];
  if ($code == "0") {
    // echo "You are now authorized on the WiFi network";
  } else {
    die("Error: check with your network administrator");
  }
}
else {
  die("Error: check with your network administrator");
}

$url = 'https://www.google.com';

if ($_SESSION["user_type"] == "new") {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];

    mysqli_query($con, "
    CREATE TABLE IF NOT EXISTS `$table_name` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `firstname` varchar(45) NOT NULL,
    `lastname` varchar(45) NOT NULL,
    `email` varchar(45) NOT NULL,
    `mac` varchar(45) NOT NULL,
    `last_updated` varchar(45) NOT NULL,
    PRIMARY KEY (`id`)
    )");

    mysqli_query($con,"INSERT INTO `$table_name` (firstname, lastname, email, mac, last_updated) VALUES ('$fname', '$lname', '$email', '$clientMac', NOW())");
}

mysqli_close($con);

?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <title>
      <?php echo htmlspecialchars($business_name); ?> WiFi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="assets/styles/bulma.min.css"/>
    <link rel="stylesheet" href="vendor/fortawesome/font-awesome/css/all.css"/>
    <meta http-equiv="refresh" content="2;url=<?php echo htmlspecialchars($url); ?>" />
    <link rel="icon" type="image/png" href="assets/images/favicomatic/favicon-32x32.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="assets/images/favicomatic/favicon-16x16.png" sizes="16x16"/>
    <link rel="stylesheet" href="assets/styles/style.css"/>
</head>
<body>
<div class="page">

    <div class="head">
        <br>
        <figure id="logo">
            <img src="assets/images/logo.png">
        </figure>
    </div>

    <div class="main">
        <seection class="section">
            <div class="container">
                <div id="margin_zero" class="content has-text-centered is-size-6">Please wait, you are being</div>
                <div id="margin_zero" class="content has-text-centered is-size-6">authorized on the network</div>
            </div>
        </seection>
    </div>

</div>

</body>
</html>