<?php


// sadece produktif oldugunda parantez icerisine "E_ALL" yerine 0 yazman gerekiyor
// E_ALL demek, tasarim sirasinda sana tüm hatalari net gösteriyor ;-)
error_reporting(0);

//database login info
$dbuser = "";
$dbpw = "";
$db = "";


//Specific to you the store owner
$storeName = "ILCoin Point of Sale | Pro";
$rootURL = "https://ilcointools.com/ilcoinshop-demo"; //example https://mysite.org  or http://yourhomepage.com/store
$yourEmail = "";  //email notifications will be sent to this email when a new order is placed

//determine the tax rate according to your Country
$tax = 0.18;


// ilc/usd price
$url = "https://api.coingecko.com/api/v3/simple/price?ids=ilcoin&vs_currencies=usd";

//pw to access the admin pages
$adminUN = "admin";
$adminPW = "admin"; 


//connect to the database
try {
  $conn = new mysqli("localhost", $dbuser, $dbpw, $db);
  if(!$conn){
    throw new Exception("Connection error check server log");
  }
} catch (Exception $error) {
  die($error->getMessage());
}

if (!isset($_SESSION)) {
    session_start();
}

function pre($input = array()) {
  echo "<pre>";
  print_r($input);
  echo "</pre>";
}

function checkPayment($order_item = array())
{
  global $conn;

  $getBalance = file_get_contents("https://ilcoinexplorer.com/api/addr/" . $order_item["address"] . "/balance");
  $getUnconfirmed = file_get_contents("https://ilcoinexplorer.com/api/addr/" . $order_item["address"] . "/unconfirmedBalance");

  if ($getBalance > 0) {
    $getBalance = $getBalance / 100000000;


    $paid = ($getBalance > 0) ? "yes" : "no";
    $difference = ($order_item["cost"] > $getBalance) ? "under" : "above";

    $sql = "UPDATE `orders` SET paid = '" . $paid . "', recd = " . $getBalance . ", difference = '" . $difference . "'  WHERE id = " . $order_item["id"] . ";";

    if ($conn->query($sql) === TRUE) {
      return "Payment successfully done, check details";
    }
    $conn->close();

  } elseif ($getUnconfirmed > 0) {
    $utxConvert = $getUnconfirmed / 100000000;
    $utxConvert = number_format($utxConvert, 8);
    return "Unconfirmed payment pending: " . $utxConvert . " ILC";
  } else {
    return "No Payment Yet";
  }

  return "An Unexpected Error Occurred!";
}

?>
