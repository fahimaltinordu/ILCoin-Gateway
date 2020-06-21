<?php
require_once('config.php');



if ($_SESSION['logged'] != 1) {
    header("Location: login.php");
}

$product = mysqli_real_escape_string($conn, $_GET['item']);

$queryItem = "SELECT * FROM products WHERE id = '$product'";
$doItem = mysqli_query($conn, $queryItem) or die(mysqli_error($conn));
$fetchItem = mysqli_fetch_assoc($doItem);
$iname = $fetchItem['name'];
$iprice = $fetchItem['price'];
$idesc = $fetchItem['description'];
$iimage = $fetchItem['image'];
$istock = $fetchItem['in_stock'];

if (isset($_POST['update'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cost = mysqli_real_escape_string($conn, $_POST['price']);
    if ($cost < 0.01) {
        $cost = 0.01;
    }
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    $instock = mysqli_real_escape_string($conn, $_POST['stock']);
    $queryUpdate = "UPDATE products SET name = '$name', price = '$cost', description = '$desc', image = '$image', in_stock = '$instock' WHERE id = '$product'";
    $doUpdate = mysqli_query($conn, $queryUpdate) or die(mysqli_error($conn));
    $message = "Product Updated";
    $icon = "success";



    //update form 
    $queryItem = "SELECT * FROM products WHERE id = '$product'";
    $doItem = mysqli_query($conn, $queryItem) or die(mysqli_error($conn));
    $fetchItem = mysqli_fetch_assoc($doItem);
    $iname = $fetchItem['name'];
    $iprice = $fetchItem['price'];
    $idesc = $fetchItem['description'];
    $iimage = $fetchItem['image'];
    $istock = $fetchItem['in_stock'];
}

if (isset($_POST['delete'])) {
    $queryDelete = "DELETE FROM products WHERE id = '$product'";
    $doDelete = mysqli_query($conn, $queryDelete) or die(mysqli_error($conn));
    $message = "Item Removed";
    $icon = "info";
}

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit Product</title>
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
                <h1 class="page-head-line">Edit Product

                </h1>

            </div>
        </div>


        </div>

        <style>
            #doj .ui-datepicker-calendar {
                display: none;
            }

            .text {
                height: 30px;
                width: 50%;
                padding: 5px 10px;
                font-size: 16px;
                border-radius: 5px;
                border: 1px solid gray;
            }

            .inputArea {
                height: 120px;
                max-height: 140px;
                min-height: 80px;
                width: 75%;
                max-width: 85%;
                min-width: 50%;
                padding: 7px;
                border: 1px solid gray;
                border-radius: 5px;
            }
        </style>

        <div class="panel panel-default">
            <div class="panel-body">


                <form class="add-item-form" method="post" onsubmit="return confirm('Are you sure?');">
                    <b>Product Name</b><br>
                    <input type="text" class="text" name="name" value="<?php echo $iname; ?>"><br>
                    <b>Price USD</b><br>
                    <input type="text" class="text" name="price" value="<?php echo $iprice; ?>"><br>
                    <b>Description</b><br>
                    <textarea class="inputArea" name="description"><?php echo $idesc; ?></textarea><br><br>
                    <b>Image Link</b> example: http://i.stack.imgur.com/m9uaE.png<br>
                    <input type="url" class="text" name="image" value="<?php echo $iimage; ?>"><br><br>
                    <b>Item In Stock?</b> Marking it "No" will hide the item from visitors<br>
                    <input type="radio" name="stock" <?php if (isset($istock) && $istock == "1") {
                                                            echo "checked";
                                                        } ?> value="1">Yes &nbsp &nbsp
                    <input type="radio" name="stock" <?php if (isset($istock) && $istock == "0") {
                                                            echo "checked";
                                                        } ?> value="0">No <br><br>
                    <input class="btn btn-info" type="submit" id="update" name="update" value="Update"> &nbsp
                    <input class="btn btn-danger" type="submit" id="delete" name="delete" value="Delete">
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
    } else if
    ($icon == "info") {

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