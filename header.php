<body>
    <div id="wrapper">

        <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <a class="navbar-brand" href="admin.php">
                    <h5>Welcome <?php echo $adminUN; ?>!</h5>
                </a>
            </div>

        </nav>
        <!-- /. NAV TOP  -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">



                    <li>
                        <a href="admin.php"><i class="fa fa-th-large "></i>Dashboard</a>
                    </li>

                    <li>
                        <a href="www.php"><i class="fa fa-shopping-cart "></i>All Orders</a>
                    </li>

                    <li>
                        <a href="xxx.php"><i class="fa fa-cart-arrow-down "></i>Recent Orders</a>
                    </li>

                    <li>
                        <a href="yyy.php"><i class="fa fa-file-text "></i>Manage Inventory</a>
                    </li>
                    <li>
                        <a href="zzz.php"><i class="fa fa-plus-square-o "></i>Add Product</a>
                    </li>
                    <li>
                        <a href="add_address.html" target="_blank"><i class="fa fa-plus-circle "></i>Add Address</a>
                    </li>

                    <li>
                        <a href="logout.php"><i class="fa fa-sign-out "></i>Logout</a>
                    </li>


                </ul>

            </div>

        </nav>
        <!-- /. NAV SIDE  -->
    </div>
</body>