<?php

/*
 not paid
 paid
 shipped
 delivered


*/
include('server/connection.php');

if(isset($_POST['order_details_btn']) && isset($_POST['order_id'])){
    
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");

    $stmt->bind_param('i',$order_id);

    $stmt->executed();

    $order_details = $stmt->get_result();

    $order_total_price = calculateTotalOrderPrice($order_details);

}else{

    header('location: account.php');
    exit;
}



function calculateTotalOrderPrice($order_details){

      $total = 0;

      foreach( $order_details as $row){

        $product_price = $row['product_price'];
        $product_quantity = $row['product_quantity'];
        $total = $total + ($product_price * $product_quantity);
      }


        return $total;
}






?>



<?php include('layouts/header.php'); ?>



    <!--Order details-->
 <section id="orders" class="orders container my-5 py-3">
      <div class="container mt-5">
        <h2 class="font-weight-bold text-center">Order details</h2>
        <hr class="mx-auto">
      </div>

      <table class="mt-5 pt-5 mx-auto">
        <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
        </tr> 


        <?php foreach($order_details as $row) { ?>
            
          <tr>
              <td>
                  <div class="product-info">
                    <img src="assets/img/<?php echo $row['product_image']; ?>"/>
                 <div>
                    <p class="mt-3"><?php echo $row['product_name']; ?></p>
                 </div>
                  </div>
             </td>  

             <td>
              <span>$<?php echo $row['product_price']; ?></span>
             </td>

             <td>
              <span><?php echo $row['product_quantity']; ?></span>
             </td>



          </tr>


            <?php } ?>


    </table> 


            <?php 
            
            if($order_status == "not paid"){ ?>
                <form style="float: right;" method="POST" action="payment.php"> <!-- de la method asa se activeaza butonu sa te duca unde vr-->
                  <input type="hidden" name="order_total_price" value="<?php echo $order_total_price; ?>" />
                  <input type="hidden" name="order_status" value="<?php echo $order_status; ?>" />
                   <input type="submit" name="order_pay_btn" class="btn btn-primary"value="pay now"/> 
            </form>
            
        
        <?php } ?>
            
            

        </section>






 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>
