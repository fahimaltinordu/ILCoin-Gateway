<?php

require_once('config.php');

$queryProducts = "SELECT * FROM products WHERE in_stock > 0 ORDER BY id ASC";
$resultH = mysqli_query($conn, $queryProducts) or die("error fetching products table");

if (!isset($_SESSION["articles"])) {
    $_SESSION["articles"] = [];
}

if (isset($_POST) && is_array($_POST) && count($_POST) > 0) {
    if (isset($_POST["article"]) && is_array($_POST) && count($_POST) > 0) {

        list($id, $name, $price) = explode("_", $_POST["article"]);

        if (!in_array($id, $_SESSION["articles"]) && !array_key_exists($id, $_SESSION["articles"])) {
            $_SESSION["articles"][$id] = ["amount" => 1, "name" => $name, "price" => $price];
        } else if (array_key_exists($id, $_SESSION["articles"])) {
            $_SESSION["articles"][$id]["amount"] += 1;
        }
    } else if (isset($_POST["reset_card"])) {
        unset($_SESSION["articles"]);
        $_SESSION["articles"] = [];
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo $storeName; ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="ILCoin Market - The Worlds First Online Shop purely dedicated to ILCoin">
    <meta name="keywords" content="ILCoin,ILC,shopping,market,payment,accept,crypto,cryptocurrency">
    <link rel="shortcut icon" href="icons/favicon.ico">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="offcanvas.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-md navbar-dark">
            <a class="navbar-brand">
                <h2><?php echo $storeName; ?></h2>
            </a>
        </nav>
    </header>

    <main role="main">
        <div class="container-fluid">
            <div class="row row-offcanvas row-offcanvas-right">
                <div class="col-12 col-md products">
                    <p class="float-right hidden-up">
                        <button type="button" class="btn btn-warning" data-toggle="offcanvas">Your Cart</button>
                    </p>
                    <div class="d-flex flex-row flex-wrap ">

                        <form id="scrollbar-custom" action="index.php" method="POST">

                            <?php
                            while ($outputsH = mysqli_fetch_assoc($resultH)) {

                                echo '                                
                                <div class="d-inline-flex flex-column articles">
<div class="image" style="background-image:url(' . $outputsH['image'] . ');"></div>
<button class="btn content" type="submit" name="article" value="' . $outputsH['id'] . '_'  . $outputsH['name'] . '_' . $outputsH['price'] . '">
' . $outputsH['name'] . '
<span class="p-2">
$' . $outputsH['price'] . '
</span>
</button>
</div>
';
                            }

                            ?>
                            <div class="w-100" style="margin-top:10px; height:10px;"></div>
                        </form>
                    </div>
                </div>
                <div id="sidebar" class="col-6 col-md-auto sidebar-offcanvas sidebar">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <h2>Your Cart</h2>
                                <p>Table</p>
                                <p>#<?= array_sum(array_map(function ($element) {
                                        return $element["amount"];
                                    }, $_SESSION["articles"])); ?></p>

                                <div id="scrollbar-custom" class="container card-section1">
                                    <div class="row">
                                        <div class="col">Order</div>
                                        <div class="col-3">Quantity</div>
                                        <div class="col-4">Price</div>
                                    </div>
                                    <?php

                                    if (isset($_SESSION["articles"]) && is_array($_SESSION["articles"]) && count($_SESSION["articles"]) > 0) {

                                        foreach ($_SESSION["articles"] as $key => $value) {
                                            echo '<div class="row">
                                            <div class="col">' . $value["name"] . '</div>
                                            <div class="col-2">' . $value["amount"] . '</div>
                                            <div class="col-4">' . number_format($value["price"], 2) . '</div>
                                            </div>';
                                        }
                                    }

                                    

                                    $subtotal = array_sum(array_map(function ($element) {
                                        return $element["amount"] * $element["price"];
                                    }, $_SESSION["articles"]));
                                    $taxes = $subtotal * $tax;
                                    $total = $subtotal + $taxes;
                                    ?>
                                </div>


                                <div class="container card-section2">
                                    <div class="row">
                                        <div class="col-8">Subtotal</div>
                                        <div class="col-4">$ <?= number_format($subtotal, 2); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8">Taxes</div>
                                        <div class="col-4">$ <?= number_format($taxes, 2); ?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-8">Total</div>
                                        <div class="col-4">$ <?= number_format($total, 2); ?></div>
                                    </div>
                                </div>

                                <div class="container card-section3">
                                    <div class="row justify-content-center text-center">
                                        <div class="col-6">
                                            <form action="./index.php" method="POST">
                                                <button class="btn rounded-pill" type="submit" name="reset_card">Clear</button>
                                            </form>
                                        </div>
                                        <div class="col-6">
                                            <a class='btn btn-success rounded-pill' href="cart.php">View Cart</a>
                                            <!-- <form action="cart.php" method="POST">
                                                <input class='btn btn-success rounded-pill' type="submit" name="test" value="View Cart">
												
                                </form> -->
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="offcanvas"]').click(function() {
                $('.row-offcanvas').toggleClass('active')
            });
        });
    </script>
    
</body>

</html>