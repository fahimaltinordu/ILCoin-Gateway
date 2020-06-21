<?php
require_once('config.php');


$message = '';
if(!isset($_SESSION['lasttime'])){
  $_SESSION['lasttime'] = 0;
}

if(isset($_POST['submit'])){
  if($_SESSION['lasttime'] > 5){
  die("Too many invalid logins");
  }
  $hashedPW = md5($adminPW);
  $pw =  md5($_POST['pw']);
  
  $hashedUN = md5($adminUN);
  $un =  md5($_POST['un']);

  if($un=='' || $pw=='')
    {
       $error='All fields are required';
    }
  
  if($pw === $hashedPW && $un === $hashedUN){
  $_SESSION['logged'] = 1;
  header('Location: admin.php');
  } else {
  $message = "Invalid Username or Password";
  $_SESSION['lasttime']++;
  }
}

?>

<!DOCTYPE html>
<html>
    <head>
      <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
	
	 <!-- BOOTSTRAP STYLES-->
    <link href="css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="css/font-awesome.css" rel="stylesheet" />
	<!-- GOOGLE FONTS-->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'/>
     <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" /> 
	<style>
	
.myhead{
margin-top:0px;
margin-bottom:0px;
text-align:center;
}

</style>  
	  
    </head>

	<body>
	
	<div class="container">
        
         <div class="row ">
               
                <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                          
                            <div class="panel-body" style="background-color: #E2E2E2; margin-top:50px; border:solid 3px #0e0e0e;">
							  <h3 class="myhead"><?php echo $storeName; ?></h3>
                                
								<form role="form" action="login.php" method="post">
                                    <hr />
									<?php
									if($message!='')
									{									
									echo '<h5 class="text-danger text-center">'.$message.'</h5>';
									}
									?>
									
                                   
                                     <div class="form-group input-group">
                                            <span class="input-group-addon"><i class="fa fa-tag"  ></i></span>
                                            <input type="username" class="form-control" placeholder="Your Username " name="un" required />
                                        </div>
                                        
									<div class="form-group input-group">
                                            <span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
                                            <input type="password" class="form-control"  placeholder="Your Password" name="pw" required />
                                        </div>
										
                                                                         
                                     <button class="btn btn-primary" type= "submit" name="submit">Login Now</button>
                                   
                                    </form>
                            </div>
                           
                        </div>
                
                
        </div>
    </div>
	
		 
		
</body>
</html>