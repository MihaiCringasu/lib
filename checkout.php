<?php


session_start();

if( !empty($_SESSION['cart']) ) {

  //let user in




  //send user to home page
}else{

  header('location: index.php');

}

?>


<?php include('layouts/header.php'); ?>



    <!--Checkout-->
        <section class="my-5 py-5">
        <div class="container text-center mt-3 pt-5">
            <h2 class="form-weight-bold">Checkout</h2>
            <hr class="mx-auto">
        </div>
        <div class="mx-auto container">

            <form id="checkout-form" method="POST" action="server/place_order.php">
              <p class="text-center" style="color: red;"><?php if(isset($_GET['message'])){  echo $_GET['message']; }?>
              <?php if(isset($_GET['message'])) { ?>

                <a href="login.php" class="btn btn-primary">Login</a>


                <?php }?>
                
                
                </p>
                    <div class="form-group checkout-small-element">
                    <label>Name</label>
                    <input type="text" class="form-control" id="checkout-name" name="name" placeholder="name" required/>
                </div>
                <div class="form group checkout-small-element">
                    <label>Email</label>
                    <input type="text" class="form-control" id="checkout-email" name="email" placeholder="email" required/>
                </div>
                    <div class="form-group checkout-small-element">
                    <label>Phone</label>
                    <input type="tel" class="form-control" id="checkout-phone" name="phone" placeholder="phone" required/>
                </div>
                <div class="form-group checkout-small-element">
                    <label>City</label>
                    <input type="text" class="form-control" id="checkout-city" name="city" placeholder="City" required/>
                </div>
                    <div class="form-group checkout-large-element">
                    <label>Address</label>
                    <input type="text" class="form-control" id="checkout-address" name="address" placeholder="Address" required/>
                </div>
                    <div class="form-group checkout-btn-container">
                      <p>Total amount: $ <?php echo $_SESSION['total']; ?></p>
                    <input type="submit" class="btn" id="checkout-btn" name="place_order"value="Place Order"/>
                </div>
            </form>
        </div>
    </section>  




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>
