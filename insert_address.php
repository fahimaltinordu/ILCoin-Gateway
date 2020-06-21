<?php

use PHPSocketIO\Engine\Protocols\SocketIO;

require_once('config.php');

if (isset($_POST) && isset($_POST["data"]) && count($_POST["data"]) > 0) {
  $data = $_POST["data"];
  $sql = "INSERT INTO `pay` (address, private_key) VALUES ";

  foreach($data as $key => $value) {
    $sql .= "('" . $value["address"] . "', '" . $value["privateKey"] . "')";

    if ($key < count($data) - 1) {
      $sql .= ", ";
    }
  }
  $sql .= ";";

  if ($conn->query($sql) === TRUE) {
    echo "Addresses have been added to DB";
  } else {
    echo "Error, try again later";
  }


}

?>
