<?php
require_once('config.php');


if ($_SESSION['logged'] != 1) {
    header("Location: login.php");
}

if (isset($_POST['add'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cost = mysqli_real_escape_string($conn, $_POST['price']);
    if ($cost < 0.01) {
        $cost = 0.01;
    }

    $image = mysqli_real_escape_string($conn, $_POST['image']);
    $instock = 1;
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $queryAdd = "INSERT INTO products (name, price, image, in_stock, description) VALUES ('$name', '$cost', '$image', '$instock', '$desc')";
    $doAdd = $conn->query($queryAdd);
    $message = "New Item Added";
    $icon = "success";
}

//check for payment buttons
$queryOrders2 = "SELECT * FROM orders ORDER BY date DESC LIMIT 10";
$doOrders2 = mysqli_query($conn, $queryOrders2) or die(mysqli_error($conn));
while ($loopOrders2 = mysqli_fetch_assoc($doOrders2)) {
    if (isset($_POST[$loopOrders2['id']])) {
        $order_num = $loopOrders2['id'];
        $address = $loopOrders2['pay_id'];
        $getBalance = file_get_contents("https://ilcoinexplorer.com/api/addr/" . $address . "/balance");
        $getUnconfirmed = file_get_contents("https://ilcoinexplorer.com/api/addr/" . $address . "/unconfirmedBalance");
        if ($getBalance > 0) {
            $queryUpdate = "UPDATE orders SET paid = 1, recd = $getBalance WHERE id = '$order_num'";
            $doUpdate = mysqli_query($conn, $queryUpdate) or die(mysqli_error($conn));
            $message = "Payment successfully done, click order ID to see details";
            //		   header("Location: admin.php");
        } elseif ($getUnconfirmed > 0) {
            $utxConvert = $getUnconfirmed / 100000000;
            $utxConvert = number_format($utxConvert, 8);
            $message = "Unconfirmed payment pending: " . $utxConvert . " ILC";
        } else {
            $message = "No Payment Yet";
        }
    }
}



?>


<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Product</title>
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




</head>
<?php
include("header.php");
?>


<div id="page-wrapper">
    <div id="page-inner">
        <div class="row">
            <div class="col-md-12">
                <h1 class="page-head-line">Add Product

                </h1>

            </div>
        </div>


       
        <style>
            #doj .ui-datepicker-calendar {
                display: none;
            }

            .text {
                height: 30px;
                width: 60%;
                padding: 5px 10px;
                font-size: 16px;
                border-radius: 5px;
                border: 1px solid gray;
            }

            .inputArea {
                height: 100px;
                max-height: 100px;
                min-height: 100px;
                width: 100%;
                max-width: 100%;
                min-width: 60%;
                padding: 7px;
                border: 1px solid gray;
                border-radius: 5px;
            }
        </style>

        <div class="panel panel-default">
            <div class="panel-body">


                <form class="add-item-form" method="post">
                    <b>Product Name</b><br>
                    <input type="text" class="text" name="name"><br><br>
                    <b>Price USD</b><br>
                    <input type="text" class="text" name="price"><br><br>
                    <b>Image Link</b> (example: http://i.stack.imgur.com/m9uaE.png)<br>
                    <input type="url" class="text" name="image"><br><br>
                    <b>Description</b><br>
                    <textarea class="inputArea" name="description"></textarea><br><br>
                    <input class="btn btn-primary btn-lg btn-block" type="submit" id="add" name="add" value="Add Product">
                </form>

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
                        location = 'yyy.php';  
                    }});
                     
            ";
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