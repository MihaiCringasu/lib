<?php 

session_start();

if(isset($_POST['add_to_cart'])){
  //if user has already added in cart
    if(isset($_SESSION['cart'])){

      $products_array_ids = array_column($_SESSION['cart'], "product_id"); //array with all products ids
      //check if product has already been added or not
      if(!in_array($_POST['product_id'], $products_array_ids) ){


          $product_id = $_POST['product_id'];

           $product_array = array(
                      'product_id' => $_POST['product_id'],
                      'product_name' => $_POST['product_name'],
                      'product_price' => $_POST['product_price'],
                      'product_image' => $_POST['product_image'],
                      'product_quantity' => $_POST['product_quantity'],
      );

      $_SESSION['cart'][$product_id] = $product_array; 
        //product has already been added
      }else{

        echo '<script>alert("Produsul deja a fost adaugat")</script>';
        
      }

      //if this is the first product
    }else{

      $product_id = $_POST['product_id'];
      $product_name = $_POST['product_name'];
      $product_price = $_POST['product_price'];
      $product_image = $_POST['product_image'];
      $product_quantity = $_POST['product_quantity'];

     $product_array = array(
                      'product_id' => $product_id,
                      'product_name' => $product_name,
                      'product_price' => $product_price,
                      'product_image' => $product_image,
                      'product_quantity' => $product_quantity
      );

      $_SESSION['cart'][$product_id] = $product_array;  
    }

    //calculate total
  
    calculateTotalCart();


//remove products from cart
}else if(isset($_POST['remove_product'])){
  
  $product_id = $_POST['product_id'];
  unset($_SESSION['cart'][$product_id]);
}elseif(isset($_POST['edit_quantity']) ) {
  //we get id and quantity from the form
  $product_id = $_POST['product_id'];
  $product_quantity = $_POST['product_quantity'];
  //get the product array from the session
  $product_array = $_SESSION['cart'][$product_id];
  //update product quantity
  $product_array['product_quantity'] = $product_quantity;
  //return array back its place
  $_SESSION['cart'][$product_id] = $product_array;
}


function calculateTotalCart(){

      $total = 0;
      $total_quantity = 0;

  foreach($_SESSION['cart'] as $key => $value){

        $product = $_SESSION['cart'][$key];

        $price = $product['product_price'];

        $quantity = $product['product_quantity'];

        $total_price = $total_price + ($price * $quantity);
        $total_quantity = $total_quantity + $quantity;
  }

  $_SESSION['total'] = $total_price;
  $_SESSION['quantity'] = $total_quantity;
}



?>


<?php include('layouts/header.php'); ?>




    <!--Cart-->
    <section class="cart container my-5 py-5">
        <div class="container mt-5">
            <h2 class="font-weight-bolde"> your cart</h2>
                <hr>
        </div>

        <table class="mt-5 pt-5">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Subtotal</th>
            </tr>
            
<?php if(isset($_SESSION['cart']) && is_array($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
  <?php foreach($_SESSION['cart'] as $key => $value): ?>
  <tr>
      <td>
          <div class="product-info">
              <img src="assets/img/<?php echo $value['product_image']; ?>"/>
              <div>
                  <p><?php echo $value['product_name'];?></p>
                  <small><span>$</span><?php echo $value['product_price'];?></small>
                  <br>
                  <form method="POST" action="cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $value['product_id']; ?>" /> 
                    <input type="submit" name="remove_product" class="remove-btn" value="remove"/>
                  </form>
              </div>
          </div>
      </td>

      <td>
          <form method="POST" action="cart.php">
            <input type="hidden" name="product_id" value="<?php echo $value['product_id'];?>"/>
            <input type="number" name="product_quantity" value="<?php echo $value['product_quantity']; ?>" />
            <input type="submit" value="edit" name="edit_quantity"/>
          </form>
      </td>

      <td>
          <span>$</span>
          <span class="product-price"><?php echo $value['product_quantity'] * $value['product_price']; ?></span>
      </td>
  </tr>
  <?php endforeach; ?>
<?php else: ?>
  <tr>
    <td colspan="3" style="text-align:center; padding: 20px;">Coșul este gol.</td>
  </tr>
<?php endif; ?>
        </table>


        <div class="cart-total">
            <table>
                <tr>
                    <td>Total </td>
                   <td>$<?php echo isset($_SESSION['total']) ? $_SESSION['total'] : "0.00"; ?></td>
                </tr>
            </table>
        </div>
    </section>

    <div class="checkout-container">
      <form method="POST" action="checkout.php">
        <input type="submit" class="btn checkout-btn" value="checkout" name="checkout">
      </form>
      
    </div>


      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>
