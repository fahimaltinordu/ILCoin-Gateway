<?php
require_once('config.php');

if (isset($_POST) && is_array($_POST) && isset($_POST['empty'])) {
   unset($_SESSION["articles"]);
   $_SESSION["articles"] = [];
   
}



$total = 0;
$ilcPrice = 0;
$ilcTotal = 0;

try {
   if (isset($url)) {
      $ilcPrice = json_decode(file_get_contents($url), TRUE)["ilcoin"]["usd"];
   }
   if (is_null($ilcPrice) && json_last_error() !== JSON_ERROR_NONE) {
      throw new Exception("Define error Message here!");
   }
} catch (Exception $error) {
   // Hata oldugunda, onu buraya basiyor.
   
   echo $error->getMessage();
}

?>

<!DOCTYPE html>
<html>

<head>
   <title>Cart</title>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" type="text/css" href="style-admin.css">
   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>


<body>
   <div id="baslık">
      <div><b><?php echo $storeName; ?></b></div>
   </div>

   <div id="viewCartconfirm">
      <span id="viewTitleProduct">Your Cart</span>

      <br><br><br>


      <table id="customers">
         <thead>
            <th>Product(s)</th>
            <th>Amount</th>
            <th>Price</th>
         </thead>
         <tbody>
            <?php
            if (isset($_SESSION["articles"]) && is_array($_SESSION["articles"]) && count($_SESSION["articles"]) > 0) {

               foreach ($_SESSION["articles"] as $key => $value) {
                  echo '<tr>
            <td>' . $value["name"] . '</td>
            <td> x' . $value["amount"] . '</td>
            <td>$ ' . number_format($value["price"], 2) . '</td>
            </tr>';
               }

               $total = array_sum(array_map(function ($element) use ($tax) {
                  return $element["amount"] * $element["price"] * (1 + $tax);
               }, $_SESSION["articles"]));
            }

            $ilcTotal = ($ilcPrice > 0) ? ($total / $ilcPrice) : 0;

            // Bu satir cok önemli, bunu kesinlikle silme !!!
            $_SESSION["article-price"] = ["total" => $total, "ilcTotal" => $ilcTotal];
            ?>

         </tbody>

         <tfoot>
            <?php
            echo '<tr>
         <td colspan="2"><b>Order Total USD (includes tax):</b></td>
         <td><b> $' . number_format($total, 2) . '<b></td>
         </tr>';

            echo '<tr>
         <td colspan="2"><b>Order Total ILC:</b></td>
         <td><b>' . number_format($ilcTotal, 8) . ' ILC </b></td>
         </tr>';
            ?>
         </tfoot>




         
      </table>
      <br>
      <div style="display:inline-block">
         <button class="btn btn-primary rounded-pill" onclick="location.href='index.php'">Back to Products</button>
      </div>

      <div style="display:inline-block">
         <form method="post">
            <input type="submit" value="Empty Cart" name="empty" class="btn btn-danger rounded-pill">
         </form>
      </div>

      <div style="float:right" style="display:inline-block">
         <form action="pay.php" method="POST">

            <input type="submit" value="Checkout" class="btn btn-success rounded-pill" name="checkout" <?= empty($_SESSION["articles"]) ? "disabled" : ""; ?> />
         </form>
      </div>


      <?php if (isset($message)) {
         echo '<script>alert("' . $message . '");</script>';
      } ?>
   </div>

</body>

</html>