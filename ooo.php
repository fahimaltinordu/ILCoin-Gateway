<?php
require_once('config.php');


if ($_SESSION['logged'] != 1) {
  header("Location: login.php");
}

$icon = "";
$order = "";

if (isset($_GET) && isset($_GET['id']) && $_GET["id"] > 0) {
  $order = $_GET['id'];


  $sql = "SELECT o.id as order_id, o.cost, o.pay_id, p2.address, o.paid, o.recd, 
  p.id as product_id, p.name, p.price, p.image, p.in_stock, p.description, po.amount FROM orders as o 
  LEFT JOIN pay AS p2 on o.pay_id=p2.id
  LEFT JOIN products_has_orders as po ON o.id=po.orders_id
  LEFT JOIN products as p ON po.products_id=p.id
  WHERE o.id = " . $order;


  if ($stmt = $conn->query($sql)) {
    // $result = $stmt->fetch_assoc();

    $products = [];
    $order_item = [];


    while ($result = $stmt->fetch_assoc()) {
      array_push($products, $result);
      $order_item = ["id" => $result["order_id"], "cost" => $result["cost"], "address" => $result["address"]];
    }


    if (!empty($order_item) && is_array($order_item) && count($order_item) > 0 && isset($_GET["checkPayment"]) && $_GET["checkPayment"] == true) {

      $message = checkPayment($order_item);
      $icon = "info";
    }
  }
}



if (isset($_POST['complete'])) {
  $queryComplete = "UPDATE orders SET complete = 1 WHERE id = '$order'";
  $doComplete = mysqli_query($conn, $queryComplete) or die(mysqli_error($conn));
  $message = "Order Marked Complete";
  $icon = "success";
}

?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order</title>
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
        <h1 class="page-head-line">Order

        </h1>

      </div>
    </div>


    <style>
      #doj .ui-datepicker-calendar {
        display: none;
      }

      #confirmShip {
        float: center;
        width: 100%;
        text-align: right;
        padding: 15px;
      }

      #viewTitleProduct {
        font-size: 16px;
        float: left;
        color: #17477e;
      }
    </style>

    <div class="panel panel-default">
      <div class="panel-body">
        <div id="viewCart">

          <span id="viewTitleProduct"><b>Order: #<?= $order; ?></b></span><br>
          <form method="post">
            <button style="float:right" class="btn btn-primary" class='checkPmt' name="complete" type="submit" value="Mark Order Complete">Mark Order Complete</button>
            <!-- /.<input class="btn btn-primary" class='checkPmt' type="submit" name="complete" value="Mark Order Complete">-->
          </form>
          <?php
          $paidMsg = "";

          if (is_array($products) && count($products) > 0) {
            if ($products[0]["paid"] == "yes") {
              $paidMsg = "Yes - <a href='https://ilcoinexplorer.com/address/" . $products[0]["address"] . "' target='_blank'>View on Block Explorer</a>";
            } else {
              $paidMsg = "No";
            }
          }

          ?>

          <b>Paid:</b> <?= $paidMsg; ?><br>
          <a class='btn btn-info' href='ooo.php?id=<?= $order; ?>&checkPayment=true'>Check For Payment</a><br>
          <br><b>Amount Paid:</b> <?= number_format($products[0]["recd"], 8); ?> <b>ILC</b><br>
          <b>Order Amount:</b> <?= $products[0]["cost"]; ?> <b>ILC</b><br>
          <b>Difference:</b> <?= ($products[0]["cost"] > $products[0]["recd"]) ? "under" : "above"; ?><br>



          <br><b style="color: #17477e">Receiving Address:</b> <?= $products[0]["address"]; ?>
          <br><br>

          <b id="viewTitleProduct">Order(s):</b><br><br>
          <div class=" confirmShip">
            <table class="table table-hover" id="tSortable22">
              <thead>
                <tr>

                  <th>Name</th>
                  <th>Price</th>
                  <th>Amount</th>

                </tr>
              </thead>
              <tbody>
                <?php

                if (is_array($products) && count($products) > 0) {
                  foreach ($products as $key => $value) {
                    echo "<tr>";
                    echo "<td>" . $value["name"] . "</td>";
                    echo "<td>" . $value["price"] . "</td>";
                    echo "<td>" . $value["amount"] . "</td>";
                    echo "</tr>";
                  }
                }


                ?>

              </tbody>
            </table>




            <?php
            
            ?>
          </div>
        </div><br>
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

  if ($icon == "success") {
    echo "
                Swal.fire({    
                icon: '$icon',
              title: '$message',
            allowOutsideClick: false})  
                .then((result) => {
  if (result.value) {     
                        location = 'ooo.php?id=$order';  
                    }});
                     
            ";
  } else if ($icon == "info"){

    echo "
                Swal.fire({    
                icon: '$icon',
              title: '$message',
            allowOutsideClick: false})  
                .then((result) => {
  if (result.value) {     
                        location = 'ooo.php?id=$order';  
                    }});
                     
            ";
  }

  

  ?>
</script>




<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="js/jquery-1.10.2.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="js/bootstrap.js"></script>
<!-- METISMENU SCRIPTS -->
<script src="js/jquery.metisMenu.js"></script>
<!-- CUSTOM SCRIPTS -->
<script src="js/custom1.js"></script>