<?php
require_once("./config.php");

if (isset($_REQUEST) && is_array($_REQUEST) && !empty($_REQUEST["confirmed"])) {
    // print_r($_REQUEST["confirmed"]);

    $order_id = $_REQUEST["confirmed"]["order_id"];
    $ilcTotal = $_REQUEST["confirmed"]["ilcTotal"];
    $amount = $_REQUEST["confirmed"]["amount"];
    $txid = $_REQUEST["confirmed"]["txid"];

    $paid = ($amount > 0) ? "yes" : "no";
    $difference = ($ilcTotal >= $amount) ? "under" : "above";

    $sql = "UPDATE `orders` SET recd = " . $amount . ", paid = '" . $paid . "', difference = '" . $difference . "' WHERE id = " . $order_id . ";";

    if ($conn->query($sql) === TRUE) {
        echo "Güncellendi <br/> ödenen miktar: " . $amount; 
    } else {
        echo "Hata oldu";
    }
    $conn->close();
}