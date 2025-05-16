<?php


session_start();

include('connection.php');


    //if user is not logged in
if(!isset($_SESSION['logged_in'])) {
    header('location: ../checkout.php?message=Please login/register to place an order');
    exit;



    //if user is logged in
}else{


    



if(isset($_POST['place_order'])){

}

    //1.get user info and store it in databaes

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $order_cost = $_SESSION['total'];
    $order_status = "Not paid";
    $user_id = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO orders (order_cost,order_status,user_id,user_phone,user_city,user_address,order_date)
                    VALUES (?,?,?,?,?,?,?); ");

    $stmt->bind_param('isiisss',$order_cost,$order_status,$user_id,$phone,$city,$address,$order_date);
    
    $stmt_status = $stmt->execute();

    if(!$stmt_status){
        header('location: index.php');
        exit;
    }

        //3.store order info in database
    $order_id = $stmt->insert_id;




    
    //2.get products from cart

    foreach($_SESSION['cart'] as $key => $value) {

        $product = $_SESSION['cart'][$key];
        $product_id = $product['product_id'];
        $product_name = $product['product_name'];
        $product_image = $product['product_image'];
        $product_price = $product['product_price'];
        $product_quantity = $product['product_quantity'];

         //4. store each single item in order_items database
        $conn -> prepare("INSERT INTO order_item (order_id,product_id,product_name,product_image,product_price,product_quantity,user_id,order_date)
                            VALUES (?,?,?,?,?,?,?,?)");

        $stmt1->bind_param('iissiiis', $order_id,$product_id,$product_name,$product_image,$product_price,$product_quantity,$user_id,$order_date);

        $stmt->execute();
    } 



    //5. remove everything from cart


    //6. inform user whether everything is fine or there is a problem
    header('location: ../payment.php?order_status="order place succesfully"');



}



?>