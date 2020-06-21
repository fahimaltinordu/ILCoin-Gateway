<?php
require_once('config.php');



if($_SESSION['logged'] != 1){
   header("Location: login.php");
}

if(isset($_POST['add']))
{
   
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $cost = mysqli_real_escape_string($conn, $_POST['price']);
  if($cost < 0.01){ $cost = 0.01; }
  $desc = mysqli_real_escape_string($conn, $_POST['description']);
  $image = mysqli_real_escape_string($conn, $_POST['image']);
  $instock = 1;
  $queryAdd = "INSERT INTO products (name, price, description, image, in_stock) VALUES ('$name', '$cost', '$desc', '$image', '$instock')";
  $doAdd = mysqli_query($conn, $queryAdd) or die(mysqli_error($conn));
  $message = "New Item Added";
}

//check for payment buttons
$queryOrders2 = "SELECT * FROM orders ORDER BY date DESC LIMIT 10";
	 $doOrders2 = mysqli_query($conn, $queryOrders2) or die(mysqli_error($conn));
	 while($loopOrders2 = mysqli_fetch_assoc($doOrders2))
	 {
		if(isset($_POST[$loopOrders2['id']])){
		   $order_num = $loopOrders2['id'];
		   $address = $loopOrders2['pay_id'];
		   $getBalance = file_get_contents("https://ilcoinexplorer.com/api/addr/".$address."/balance");
		   $getUnconfirmed = file_get_contents("https://ilcoinexplorer.com/api/addr/".$address."/unconfirmedBalance");
		   if($getBalance > 0)
		   {
		   $queryUpdate = "UPDATE orders SET paid = 1, recd = $getBalance WHERE id = '$order_num'";
		   $doUpdate = mysqli_query($conn, $queryUpdate) or die(mysqli_error($conn));
		   $message = "Payment successfully done, click order ID to see details";
//		   header("Location: admin.php");
		   } elseif($getUnconfirmed > 0){
		   $utxConvert = $getUnconfirmed / 100000000;
		   $utxConvert = number_format($utxConvert, 8);
		   $message = "Unconfirmed payment pending: ".$utxConvert." ILC";
		   }else {
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
    <title>Manage Inventory</title>
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
                        <h1 class="page-head-line">Manage Inventory  
						
						</h1>

                    </div>
                </div>
				</div>
				
		   			
		



<style>
#doj .ui-datepicker-calendar
{
display:none;
}

</style>
		
		<div class="panel panel-default">
		
                        
                       <div class="panel-body">
                            <div class="table-sorting table-responsive" id="subjectresult">
                                <table class="table table-striped table-bordered table-hover" id="tSortable22">
                                    <thead>
                                        <tr>
                                          
                                            <th>Product ID</th>                                            
                                            <th>Name</th>
											<th>Price</th>
											<th>Description</th>
											<th>In Stock</th>
											<th>Manage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
								    </tbody>
									<?php
	 $queryProducts = "SELECT * FROM products ORDER BY id ASC";
	 $doProducts = mysqli_query($conn, $queryProducts) or die(mysqli_error($conn));
	 while($loopProducts = mysqli_fetch_assoc($doProducts))
	 {
	 echo "<tr>";
	 echo "<td>".$loopProducts['id']."</td>";
	 echo "<td>".$loopProducts['name']."</td>";
	 echo "<td>$".$loopProducts['price']."</td>";
	 echo "<td>".substr($loopProducts['description'], 0, 250)."</td>";
	 
	 if($loopProducts['in_stock'] == 1){ $loopStock = "Yes"; } else { $loopStock = "No"; }
	 echo "<td>".$loopStock."</td>";
	 echo "<td><a href='qqq.php?item=".$loopProducts['id']."'>Edit/Remove</a></td>";
	 echo "<tr>";
	 }
	 ?>
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
	<?php  if(isset($message)){ echo '<script>alert("'.$message.'");</script>'; }?>
	
	<script src="js/jquery-1.10.2.js"></script>	
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="js/bootstrap.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="js/jquery.metisMenu.js"></script>
       <!-- CUSTOM SCRIPTS -->
    <script src="js/custom1.js"></script>
