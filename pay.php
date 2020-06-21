<?php

use PHPSocketIO\Engine\Protocols\SocketIO;

require_once('config.php');


$payIDAdress = "";
$messages = "";
$icon = "";
$last_id = 0;

if (isset($_POST["checkout"]) && $_POST["checkout"] == "Checkout") {


  $sql = 'INSERT INTO `orders` (cost, pay_id, paid, complete) 
  VALUES(' . $_SESSION["article-price"]["ilcTotal"] . ', 
  (SELECT id FROM pay WHERE dispensed=1 LIMIT 1),
  "no",
  "no")';

  
  if ($conn->query($sql) === TRUE) {
    $last_id = $conn->insert_id;

    $slqPay = "SELECT p.id, p.address from pay as p Left JOIN orders as o on p.id=o.pay_id WHERE o.id=" . $last_id . ";";


    if ($result = $conn->query($slqPay)) {
      $payIDAdress = $result->fetch_assoc()["address"];
    }

    $sqlUpdatePay = "UPDATE `pay`
    SET dispensed = 2
    WHERE id = (SELECT pay_id FROM `orders` WHERE id=(SELECT MAX(id) FROM `orders`))";

    if ($conn->query($sqlUpdatePay) === TRUE) {
      $sqlInsertProductsHasOrders = 'INSERT INTO `products_has_orders` (products_id, orders_id, amount) VALUES ';

      if (isset($_SESSION["articles"]) && is_array($_SESSION["articles"]) && count($_SESSION["articles"]) > 0) {

        foreach ($_SESSION["articles"] as $key => $value) {

          $sqlInsertProductsHasOrders .= "(" . $key . ", " . $last_id  .  " ," . $value["amount"] . ")";

          end($_SESSION["articles"]);
          $lastKey = key($_SESSION["articles"]);

          $sqlInsertProductsHasOrders .= ($lastKey == $key) ? ";" : ",";
        }
        

        if ($conn->query($sqlInsertProductsHasOrders) === TRUE) {
          $messages = "Order has been added to database";
          $icon = "info";
        }
      }
    }
  } else {
    $messages = "There is no payment address in database, add new addresses to get payments";
    $icon = "error";
  }
}

?>

<!DOCTYPE html>
<html>

<head>
  <title>Pay</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="style-pay.css">
  <link href="css/font-awesome.css" rel="stylesheet" />
  <link href="css/bootstrap.css" rel="stylesheet" />


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>


  <script type="text/javascript" src="js/jquery.min.js"></script>
  <script type="text/javascript" src="js/qrcode.js"></script>
  <script src="https://ilcoinexplorer.com/socket.io/socket.io.js"></script>



  <script>
    const watchAddress = "<?= $payIDAdress; ?>";
    const ilcTotal = <?= number_format($_SESSION['article-price']["ilcTotal"], 8); ?>;

    var insight = io.connect('https://ilcoinexplorer.com:443/');
    insight.on('connect', function() {
      console.log('Connected.');
      insight.emit('subscribe', 'inv');
    });
    insight.on('tx', function(data) {
      const amount = data.vout.reduce(
        (accumulator, item) => accumulator + (Object.keys(item)[0] === watchAddress ? Object.values(item)[0] : 0),
        
        0
      );

      if (amount > 0 && ((amount / 100000000) >= ilcTotal )) {

       
        const message = '<a href="https://ilcoinexplorer.com/tx/' + data.txid + '">' +
          '' + amount / 100000000 + ' ILC received, click for TX details' +
          '</a><br>';
        $('#messages').prepend(message);

        $(document).ready(function() {

          $("#qrcode").css("display", "none");
          $("#tabmenu").css("display", "none");
          $("#tabmenu2").css("display", "none");
          $("#Copy").css("display", "none");
          $("#h7").css("display", "none");
          $("#scan").css("display", "none");
          $("#paybrand").css("display", "none");
          $("#payAmt").css("display", "none");
          $("#messages").css("display", "block");

          $.post("confirm.php", {
            confirmed: {
              order_id: <?= $last_id; ?>,
              ilcTotal: <?= $_SESSION['article-price']["ilcTotal"]; ?>,
              amount: (amount / 100000000),
              txid: data.txid
            }
          }, function(data, status) {
            
          });
        });

        var sound = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");
        sound.play();
      } else if (amount > 0 && ((amount / 100000000) < ilcTotal)) {
        const message = '<a href="https://ilcoinexplorer.com/tx/' + data.txid + '">' +
          '' + amount / 100000000 + ' ILC received, click for TX details' +
          '</a><br>';
        $('#missing').prepend(message);

        $(document).ready(function() {

          $("#qrcode").css("display", "none");
          $("#tabmenu").css("display", "none");
          $("#tabmenu2").css("display", "none");
          $("#Copy").css("display", "none");
          $("#h7").css("display", "none");
          $("#scan").css("display", "none");
          $("#paybrand").css("display", "none");
          $("#payAmt").css("display", "none");
          $("#messages").css("display", "none");
          $("#missing").css("display", "block");

          $.post("confirm.php", {
            confirmed: {
              order_id: <?= $last_id; ?>,
              ilcTotal: <?= $_SESSION['article-price']["ilcTotal"]; ?>,
              amount: (amount / 100000000),
              txid: data.txid
            }
          }, function(data, status) {
            
          });
        });

        var sound = new Audio("data:audio/wav;base64,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qJrx4KOQjahZxWbcZgztj2c49nKmkId44S71j0c8eV9yDK6uPRzx5X18eDvjvQ6yKo9ZSS6l//8elePK/Lf//IInrOF/FvDoADYAGBMGb7FtErm5MXMlmPAJQVgWta7Zx2go+8xJ0UiCb8LHHdftWyLJE0QIAIsI+UbXu67dZMjmgDGCGl1H+vpF4NSDckSIkk7Vd+sxEhBQMRU8j/12UIRhzSaUdQ+rQU5kGeFxm+hb1oh6pWWmv3uvmReDl0UnvtapVaIzo1jZbf/pD6ElLqSX+rUmOQNpJFa/r+sa4e/pBlAABoAAAAA3CUgShLdGIxsY7AUABPRrgCABdDuQ5GC7DqPQCgbbJUAoRSUj+NIEig0YfyWUho1VBBBA//uQZB4ABZx5zfMakeAAAAmwAAAAF5F3P0w9GtAAACfAAAAAwLhMDmAYWMgVEG1U0FIGCBgXBXAtfMH10000EEEEEECUBYln03TTTdNBDZopopYvrTTdNa325mImNg3TTPV9q3pmY0xoO6bv3r00y+IDGid/9aaaZTGMuj9mpu9Mpio1dXrr5HERTZSmqU36A3CumzN/9Robv/Xx4v9ijkSRSNLQhAWumap82WRSBUqXStV/YcS+XVLnSS+WLDroqArFkMEsAS+eWmrUzrO0oEmE40RlMZ5+ODIkAyKAGUwZ3mVKmcamcJnMW26MRPgUw6j+LkhyHGVGYjSUUKNpuJUQoOIAyDvEyG8S5yfK6dhZc0Tx1KI/gviKL6qvvFs1+bWtaz58uUNnryq6kt5RzOCkPWlVqVX2a/EEBUdU1KrXLf40GoiiFXK///qpoiDXrOgqDR38JB0bw7SoL+ZB9o1RCkQjQ2CBYZKd/+VJxZRRZlqSkKiws0WFxUyCwsKiMy7hUVFhIaCrNQsKkTIsLivwKKigsj8XYlwt/WKi2N4d//uQRCSAAjURNIHpMZBGYiaQPSYyAAABLAAAAAAAACWAAAAApUF/Mg+0aohSIRobBAsMlO//Kk4soosy1JSFRYWaLC4qZBYWFRGZdwqKiwkNBVmoWFSJkWFxX4FFRQWR+LsS4W/rFRb/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAU291bmRib3kuZGUAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAwNGh0dHA6Ly93d3cuc291bmRib3kuZGUAAAAAAAAAACU=");
        sound.play();
      } 
      // else {

      //   Swal.fire({
      //     icon: 'error',
      //     title: 'Oops...',
      //     text: 'Unexpected error ',

      //   })

      // }

    });
  </script>

</head>


<body>

  <div class="container-fluid">

    <div class="row">

      <div class="col-6" id="tabmenu">
        <button class="tablink" onclick="openCity('Copy', this, '#202020')"><b>ILCoin Gateway</b></button>
      </div>

      <div class="col-6" id="tabmenu2">
        <button class="tablink" onclick="openCity('scan', this, '#202020')" id="defaultOpen"><b>Amount to be paid:</b></button>
      </div>

    </div>

    <div class="row">

      <div class="col-6" id="paybrand">
        <div><b><?php echo $storeName; ?></b></div>
      </div>

      <div class="col-6" id="payAmt">
        <h1><b><?= number_format($_SESSION['article-price']["ilcTotal"], 8); ?> ILC</b></h1>
        <h4>~ $<?= number_format($_SESSION['article-price']["total"], 2); ?> USD</h4>
      </div>

    </div>

    <div class="row">


      <div id="Copy" class="col-6">

        <div id="copyarea">

          <br><br>
          <h4 style="color: white;">Your payment address: </h4><br>

          <div class="form-group input-group">
            <!-- <span class="input-group-addon"><i class="fa fa-arrow-circle-down"></i></span> -->
            <input type="text" class="form-control" id="payBox" value="<?= $payIDAdress; ?> " onclick="this.select();" readonly />
            <span class="buttoncopy" onclick="myFunction()"><i class="fa fa-clone"></i></span>
          </div>

          <br>
          <hr>
          <h6 style="color: white;">To confirm your order, send the exact amount of <b>ILC</b> to the given address to pay.</h6>
          <hr>
          <h6 style="color: white;">WARNING: DO NOT SEND FUNDS FROM ANY EXCHANGE WALLET</h6>
        </div>
      </div>


      <div id="scan" class="col-6">
        <div id="qrcode" class="col-6">
        </div>
      </div>


      <div id="messages" style="display:none;">
        <h1>Payment Confirmed</h1>
        <br>
        <img src="img/confirm.png" width="200px"><br>
        <p>Please go back to the merchant's site</p>
      </div>

      <div id="missing" style="display:none;">
        <h1>Payment Missing</h1>
        <br>
        <img src="img/error.png" width="200px"><br>
        <p>Please go back to the merchant's site</p>
      </div>

    </div>

  </div>


  <div id="h7">
    <div class="progressBar" data-amount="100">
      <div class="amount"></div>
    </div>
  </div>

  <div id="h6">
    <form action="./index.php" method="POST">
      <button class="btn btn-primary btn-lg btn-block" type="submit" name="reset_card">Close Invoice </button>
    </form>
  </div>



  <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
  <script src="https://cdn.rawgit.com/mgalante/jquery.redirect/master/jquery.redirect.js"></script>
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@9.14.0/dist/sweetalert2.all.min.js'></script>
  <script src="js/easy.qrcode.js" type="text/javascript" charset="utf-8"></script>


  <script>
    function openCity(cityName, elmnt, color) {
      var i, tabcontent, tablinks;
      tabcontent = document.getElementsByClassName("tabcontent");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }
      tablinks = document.getElementsByClassName("tablink");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].style.backgroundColor = "";
      }
      document.getElementById(cityName).style.display = "block";
      elmnt.style.backgroundColor = color;

    }
    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();

    (function() {
      function getTimer(duration) {
        let milliseconds = parseInt((duration % 1000) / 100),
          seconds = parseInt((duration / 1000) % 60),
          minutes = parseInt((duration / (1000 * 60)) % 60),
          hours = parseInt((duration / (1000 * 60 * 60)) % 24);

        hours = (hours < 10) ? "0" + hours : hours;
        minutes = (minutes < 10) ? "0" + minutes : minutes;
        seconds = (seconds < 10) ? "0" + seconds : seconds;

        //return hours + ":" + minutes + ":" + seconds + "." + milliseconds;
        return minutes + ":" + seconds;

        
      }


      $(document).ready(function() {
        let time = 5; // 10 minutes
        let second = 60
        let counter = time * second;
        const duration = time * second;
        let dataval = parseInt($('.progressBar').attr('data-amount'));

        
        if (dataval < 100) {
          $('.progressBar .amount').css('width', 100 - dataval + '%');
        }

        let stopBar = window.setInterval(() => {
          --counter;

          let output = parseInt(counter * 100 / (duration)); // Tamm % lig sayi
          let backgroundColor = "green";
          let fontColor = "white";

          if (output <= 75 && output > 50) {
            fontColor = "red";
            backgroundColor = "yellow";
          } else if (output <= 50 && output > 25) {
            fontColor = "white";
            backgroundColor = "orange";
          } else if (output <= 25 && output > 0) {
            backgroundColor = "red";
          }

          $('.progressBar').attr({
            'data-amount': output + '% - [' + getTimer(counter * 1000) + ' min left to pay your order]  ',
          }).css({
            'background-color': backgroundColor,
            'color': fontColor,
          });
          $('.progressBar .amount').css({
            'width': 100 - output + '%',
          });

          
          if (output <= 0 && counter <= 0) {
            clearTimeout(stopBar);
            $.redirect('index.php', {
              'reset_card': 'true'
            });
          }
        }, 1000);

        const ToastError = Swal.mixin({
          position: 'top-center',
        });

        const ToastInfo = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
          onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        });

        <?php
        if ($icon == "info") {
          echo "ToastInfo.fire({
              icon: '$icon',
              title: '$messages',
            });";
        } else {

          echo "ToastError.fire({
              icon: '$icon',
              title: '$messages',
              allowOutsideClick: false
              // showConfirmButton: true,
              
            }).then((result) => {              
              if (result.value) {
                $.redirect('index.php', {'reset_card': 'true'});
              }
            });";
        }
        ?>

      });
    }());

    var demoParams = {
      title: "",
      config: {
        text: "ilcoin:<?= $payIDAdress; ?>?amount=<?= number_format($_SESSION['article-price']["ilcTotal"], 8); ?>",

        width: 260, // Widht
        height: 260, // Height
        colorDark: "#000000", // Dark color
        colorLight: "#ffffff", // Light color

        // === Logo
        logo: "logo.png", // LOGO
        //					logo:"http://127.0.0.1:8020/easy-qrcodejs/demo/logo.png",  
        //					logoWidth:80, 
        //					logoHeight:80,
        logoBackgroundColor: '#ffffff', // Logo backgroud color, Invalid when `logBgTransparent` is true; default is '#ffffff'
        logoBackgroundTransparent: false, // Whether use transparent image, default is false

        timing_V: '#00B2EE',
        correctLevel: QRCode.CorrectLevel.H, // L, M, Q, H
        dotScale: 0.5
      }
    };

    
    var container = document.getElementById('qrcode');

    
    new QRCode(document.getElementById("qrcode"), demoParams.config);

    function myFunction() {
      var copyText = document.getElementById("payBox");
      copyText.select();
      copyText.setSelectionRange(0, 99999)
      document.execCommand("copy");
      // alert("Copied the text: " + copyText.value);
      $messages = "Copied the text: " + copyText.value;

      const Toast = Swal.mixin({
        toast: true,
        position: 'top-center',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        onOpen: (toast) => {
          toast.addEventListener('mouseenter', Swal.stopTimer)
          toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
      })
      <?php
      echo "Toast.fire({
            icon: 'success',
            title: 'Address copied to clipboard successfully'
          })"
      ?>
    }
  </script>

  
</body>

</html>