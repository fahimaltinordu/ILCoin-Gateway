<?php
require_once('config.php');


if (isset($_SESSION) && !isset($_SESSION["orders_item"])) {
    $_SESSION["orders_item"] = [];
}

if ($_SESSION['logged'] != 1) {
    header("Location: login.php");
}

if (isset($_POST['add'])) {

    $name = mysqli_real_escape_string($conn, $_POST['pname']);
    $cost = mysqli_real_escape_string($conn, $_POST['price']);
    if ($cost < 0.01) {
        $cost = 0.01;
    }
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    $instock = 1;
    $queryAdd = "INSERT INTO products (name, price, description, image, in_stock) VALUES ('$name', '$cost', '$desc', '$image', '$instock')";
    $doAdd = mysqli_query($conn, $queryAdd) or die(mysqli_error($conn));
    $message = "New Item Added";
}

if (isset($_GET) && isset($_GET['order_id']) && isset($_GET["checkPayment"]) && $_GET["order_id"] > 0 && $_GET["checkPayment"] == "true") {

    $order_item = $_SESSION["orders_item"][$_GET["order_id"]];

    if (!empty($order_item) && is_array($order_item) && count($order_item) > 0) {
        $message = checkPayment($order_item);
        $icon = "info";
    }
}



?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>All Orders</title>
    <link rel="shortcut icon" href="icons/favicon.ico">
    <!-- BOOTSTRAP STYLES-->
    <link href="css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="css/font-awesome.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
    <!--CUSTOM BASIC STYLES-->
    <link href="css/basic.css" rel="stylesheet" />
    <!--CUSTOM MAIN STYLES-->
    <link href="css/custom.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

    <script src="js/jquery-1.10.2.js"></script>
    

</head>
<?php
include("header.php");
?>


<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line">All Orders

                </h1>

            </div>
        </div>



        <style>
            #doj .ui-datepicker-calendar {
                display: none;
            }
        </style>

        <div class="panel panel-default">

            <div class="panel-heading">
                <a href="xxx.php">View recent orders</a>
            </div>
            <div class="panel-body">
                <div class="table-sorting table-responsive" id="subjectresult">
                    <table class="table table-striped table-bordered table-hover" id="tSortable22">
                        <thead>
                            <tr>

                                <th>Order ID</th>
                                <th>Cost</th>
                                <th>Paid?</th>
                                <th>Completed?</th>
                                <th>Date</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php

                           
                            $queryOrders = "SELECT o.id, o.cost, p.address, o.paid, o.complete, DATE_FORMAT(o.`date`, '%d. %M %Y %H:%i:%s') as 'date' 
                        FROM orders as o LEFT JOIN pay as p on o.pay_id=p.id ORDER BY o.date DESC ;";


                            $doOrders = $conn->query($queryOrders);
                            if ($doOrders === TRUE) {
                                unset($_SESSION["oders_item"]);
                                $_SESSION["oders_item"] = [];
                            }

                            while ($loopOrders = $doOrders->fetch_assoc()) {
                                $_SESSION["orders_item"][$loopOrders['id']] = ["id" => $loopOrders["id"], "cost" => $loopOrders["cost"], "address" => $loopOrders["address"]];

                                echo "<tr>";
                                echo "<td><a class='btn btn-primary' href='ooo.php?id=" . $loopOrders['id'] . "'>" . $loopOrders['id'] . "</a>
                            <a class='btn btn-info' href='www.php?order_id=" . $loopOrders['id'] . "&checkPayment=true'>Check For Payment</a></td>";
                                //  echo "<td><a href='ooo.php?id=" . $loopOrders['id'] . "'>" . $loopOrders['id'] . "</a>
                                //  <form method='post'><input class='checkPmt' type='submit' value='Check For Payment' name='" . $loopOrders['id'] . "'></form></td>";

                                echo "<td>" . $loopOrders['cost'] . "</td>";
                                //  if($loopOrders['paid'] == 1){ $loopPaid = "Yes"; } else { $loopPaid = "No"; }
                                echo "<td>" . $loopOrders['paid'] . "</td>";
                                //  if($loopOrders['complete'] == 1){ $loopComplete = "Yes"; } else { $loopComplete = "No"; }
                                echo "<td>" . $loopOrders['complete'] . "</td>";
                                echo "<td>" . $loopOrders['date'] . "</td>";
                                echo "</tr>";
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-------->




    </div>
    <!-- /. PAGE INNER  -->
</div>
<!-- /. PAGE WRAPPER  -->

<!-- /. WRAPPER  -->
<div id="footer-sec">
    <strong><?php echo $storeName; ?></strong>
</div>



<script src='https://cdn.jsdelivr.net/npm/sweetalert2@9.14.0/dist/sweetalert2.all.min.js'></script>

<script>
    <?php
    if ($icon == "info") {
        echo "Swal.fire({
              icon: '$icon',
              title: '$message',
              position: 'top-end',
            });";
    } 
   
    ?>
</script>

<script src="js/jquery-1.10.2.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="js/bootstrap.js"></script>
<!-- METISMENU SCRIPTS -->
<script src="js/jquery.metisMenu.js"></script>
<!-- CUSTOM SCRIPTS -->
<script src="js/custom1.js"></script>