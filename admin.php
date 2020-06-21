<?php
require_once('config.php');


if ($_SESSION['logged'] != 1) {
    header("Location: login.php");
    exit(); // logoutlarda bu sart !!!
}

 

if (isset($_POST['add'])) {

    $name = mysqli_real_escape_string($conn, $_POST['pname']);
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
}



?>
<!DOCTYPE html>
<html>


<head>
    <meta charset="utf-8" />
    <!--<meta http-equiv="refresh" content="60;url=logout.php" />-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Panel</title>
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


<body>


    <div id="page-wrapper">
        <div id="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="page-head-line"><strong>Dashboard</strong></h1>


                </div>
            </div>
            <!-- /. ROW  -->
            <div class="row">

                <div class="col-md-4">
                    <div class="main-box p-3 mb-pink">
                        <a href="www.php">
                            <i class="fa fa-shopping-cart fa-5x"></i>
                            <h5>All Orders</h5>
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="main-box mb-pink">
                        <a href="xxx.php">
                            <i class="fa fa-cart-arrow-down fa-5x"></i>
                            <h5>Recent Orders</h5>
                        </a>
                    </div>
                </div>




                <div class="col-md-4">
                    <div class="main-box mb-dull">
                        <a href="zzz.php">
                            <i class="fa fa-plus-square-o fa-5x"></i>
                            <h5>Add Product</h5>
                        </a>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="main-box mb-dull">
                        <a href="yyy.php">
                            <i class="fa fa-file-text fa-5x"></i>
                            <h5>Manage Inventory</h5>
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="main-box mb-dull">
                        <a href="index.php" target="_blank">
                            <i class="fa fa-coffee fa-5x"></i>
                            <h5>Go to Store</h5>
                        </a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class=" main-box mb-dull">
                        <a href="logout.php">
                            <i class="fa fa-sign-out fa-5x"></i>
                            <h5>Logout</h5>
                        </a>
                    </div>
                </div>


            </div>
            <!-- /. ROW  -->


        </div>
        <!-- /. PAGE INNER  -->
    </div>
    <!-- /. PAGE WRAPPER  -->



    <div id="footer-sec">
        <strong><?php echo $storeName; ?></strong>
    </div>

    <?php if (isset($message)) {
        echo '<script>alert("' . $message . '");</script>';
    } ?>


    <script src="js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="js/bootstrap.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="js/jquery.metisMenu.js"></script>
    <!-- CUSTOM SCRIPTS -->
    <script src="js/custom1.js"></script>

</body>

</html>