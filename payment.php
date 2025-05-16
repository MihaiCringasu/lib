<?php


session_start();


?>


<?php include('layouts/header.php'); ?>



    <!--Payment-->
        <section class="my-5 py-5">
        <div class="container text-center mt-3 pt-5">
            <h2 class="form-weight-bold">Payment</h2>
            <hr class="mx-auto">
        </div>
        <div class="mx-auto container text-center">
            <p><?php if(isset($_GET['order_status'])) {echo $_GET['order_status'];}?></p>
            <p>Total payment: $<?php if(isset($_SESSION['total'])) { echo $_SESSION['total']; }?></p>
            <?php if(isset($_SESSION['total'])) {?>
            <input class="btn btn-primary" type="submit" value="Pay Now"/>
            <?php } ?>
            <?php if(isset($_GET['order_status']) && $_GET['order_status'] == "not paid") {?>
            <input class="btn btn-primary" type="submit" value="Pay Now"/>
            <?php } ?>
        </div>
    </section>  




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>
</html>
