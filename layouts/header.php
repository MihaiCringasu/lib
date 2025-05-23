<?php
session_start();



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Charm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

<!---Navbar--->

<nav class="navbar navbar-expand-lg bg-body-tertiary py-3 fixed-top">
  <div class="container">
    <a href="index.php" class="navbar-brand">
      <img src="assets/img/logo.png" alt="Logo" style="height: 40px;">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      
      <div class="nav-buttons d-flex align-items-center ms-5">
        <ul class="navbar-nav mb-2 mb-lg-0 d-flex flex-row">
          <li class="nav-item me-3">
            <a class="nav-link" href="index.php">Home</a>
          </li>
          <li class="nav-item me-3">
            <a class="nav-link" href="shop.php">Shop</a>
          </li>
          <li class="nav-item me-3">
            <a class="nav-link" href="contact.php">Contact us</a>
          </li>
                    <li class="nav-item me-3">
            <a class="nav-link" href="about_us.php">About Us</a>
          </li>
        
        <li class="nav-item">
         <a href="wishlist.php"><i class="fas fa-heart me-3 icons"></i></a>
           <a href="cart.php">
            <i class="fas fa-shopping-bag me-3 icons">
            <?php if(isset($_SESSION['quantity']) && $_SESSION['quantity'] !=0 ) {?>
              <span class="cart-quantity"><?php echo $_SESSION['quantity']; ?></span>
              <?php } ?>
            </i>
           </a>
        <a href="account.php"><i class="fas fa-user icons"></i></a>
        </li>
        </ul>
      </div>

              

    </div>
  </div>
</nav>