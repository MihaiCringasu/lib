<?php 
session_start();
include('server/connection.php');

if(!isset($_SESSION['logged_in'])){
  header('location: login.php');
  exit;
}

if(isset($_GET['logout'])){
  if(isset($_SESSION['logged_in'])){
    unset($_SESSION['logged_in']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    unset($_SESSION['user_image']);
    header('location: login.php');
    exit;
  }
}

if(isset($_POST['change_password'])){
  $password = $_POST['password'];
  $confirmPassword = $_POST['confirmPassword'];
  $user_email = $_SESSION['user_email'];

  if($password !== $confirmPassword){
    header('location: account.php?error=passwords dont match');
  } else if(strlen($password) < 6){
    header('location: account.php?error=password must be at least 6 characters');
  } else {
    $stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE user_email = ?");
    $stmt->bind_param('ss',md5($password),$user_email);

    if($stmt->execute()){
      header('location: account.php?message=password has been updated succesfully');
    } else {
      header('location: account.php?error=could not update password');
    }
  }
}

// get orders
if(isset($_SESSION['logged_in'])){
  $user_id = $_SESSION['user_id'];
  $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
  $stmt->bind_param('i',$user_id);
  $stmt->execute();
  $orders = $stmt->get_result();
}

// upload new profile picture
if (isset($_POST['change_image']) && isset($_FILES['user_image'])) {
  $user_id = $_SESSION['user_id'];
  $image = $_FILES['user_image'];
  $target_dir = "assets/img/";
  $image_name = time() . "_" . basename($image["name"]);
  $target_file = $target_dir . $image_name;

  $check = getimagesize($image["tmp_name"]);
  if ($check !== false) {
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
      $stmt = $conn->prepare("UPDATE users SET user_image = ? WHERE user_id = ?");
      $stmt->bind_param("si", $image_name, $user_id);
      if ($stmt->execute()) {
        $_SESSION['user_image'] = $image_name;
        header('Location: account.php?message=Imaginea a fost actualizată');
        exit;
      }
    }
  } else {
    header('Location: account.php?error=Fișierul nu este o imagine validă');
    exit;
  }
}
?>

<?php include('layouts/header.php'); ?>
<style>
  #account-form input[type="password"],
  #account-form input[type="email"],
  #account-form input[type="text"] {
    width: 100% !important;
    max-width: 220px;
    padding: 12px;
    margin: 10px auto;
    display: block;
    box-sizing: border-box;
  }

  #account-form {
    width: 100% !important;
    max-width: 600px;
    margin: 0 auto;
    text-align: center;
  }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<!-- Account -->
<section class="my-5 py-5">
  <div class="row container mx-auto">
    <div class="text-center mt-3 pt-5 col-lg-6 col-md-12 col-sm-12">
      <p class="text-center" style="color:green"><?php if(isset($_GET['register_success'])){ echo $_GET['register_success']; }?></p>
      <p class="text-center" style="color:green"><?php if(isset($_GET['login_sucess'])){ echo $_GET['login_sucess']; }?></p>
      <h3 class="font-weight-bold"><i class="fas fa-user-circle"></i> Informații despre cont</h3>
      <hr class="mx-auto">

      <?php
      $user_image = isset($_SESSION['user_image']) ? $_SESSION['user_image'] : 'default.png';
      ?>
      <img src="assets/img/<?php echo $user_image; ?>" width="100" height="100" class="rounded-circle mb-3" alt="Profile Picture">

      <div class="account-info">
        <p><i class="fas fa-user"></i> Name: <span><?php echo $_SESSION['user_name']; ?></span></p>
        <p><i class="fas fa-envelope"></i> Email: <span><?php echo $_SESSION['user_email']; ?></span></p>
        <p><a href="#orders" id="order-btn"><i class="fas fa-box"></i> Your Orders</a></p>
        <p><a href="account.php?logout=1" id="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></p>
      </div>
    </div>

    <!-- Password Change Form -->
    <div class="col-lg-6 col-md-12 col-sm-12">
      <form id="account-form" method="POST" action="account.php">
        <p class="text-center" style="color:red"><?php if(isset($_GET['error'])){ echo $_GET['error']; }?></p>
        <p class="text-center" style="color:green"><?php if(isset($_GET['message'])){ echo $_GET['message']; }?></p>
        <h3><i class="fas fa-key"></i> Schimbă Parola</h3>
        <hr class="mx-auto">
        <div class="form-group">
          <label><i class="fas fa-lock"></i> Parola ta:</label>
          <input type="password" class="form-control w-75" name="currentPassword" placeholder="Introdu parola curentă" required/>
        </div>
        <div class="form-group">
          <label><i class="fas fa-unlock-alt"></i> Parola nouă:</label>
          <input type="password" class="form-control w-75" name="password" placeholder="Introdu parola nouă" required/>
        </div>
        <div class="form-group">
          <label><i class="fas fa-unlock"></i> Confirmă parola nouă:</label>
          <input type="password" class="form-control w-75" name="confirmPassword" placeholder="Confirmă parola nouă" required/>
        </div>
        <div class="form-group">
          <input type="submit" value="Schimbă parola" name="change_password" class="btn btn-primary" />
        </div>
      </form>
    </div>
  </div>
</section>

<!-- Profile Picture -->
<section class="container mb-5">
  <form method="POST" action="account.php" enctype="multipart/form-data">
    <h3><i class="fas fa-image"></i> Schimbă fotografia de profil:</h3>
    <div class="form-group">
      <input type="file" name="user_image" class="form-control w-75" accept="image/*" required>
    </div>
    <div class="form-group mt-2">
      <button type="submit" name="change_image" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
    </div>
  </form>
</section>

<!-- Orders -->
<section id="orders" class="orders container my-5 py-3">
  <div class="container mt-2">
    <h2 class="font-weight-bold text-center"><i class="fas fa-list"></i> Comenzile tale</h2>
    <hr class="mx-auto">
  </div>

  <table class="mt-5 pt-5 table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Order ID</th>
        <th>Cost</th>
        <th>Status</th>
        <th>Date</th>
        <th>Details</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $orders->fetch_assoc()) { ?>
      <tr>
        <td><span><?php echo $row['order_id']; ?></span></td>
        <td><span>$<?php echo $row['order_cost']; ?></span></td>
        <td><span><?php echo $row['order_status']; ?></span></td>
        <td><span><?php echo $row['order_date']; ?></span></td>
        <td>
          <form method="POST" action="order_details.php">
            <input type="hidden" name="order_status" value="<?php echo $row['order_status']; ?>" />
            <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>" />
            <button class="btn btn-primary btn-sm" name="order_details_btn" type="submit">
              <i class="fas fa-info-circle"></i> Details
            </button>
          </form>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <?php include('layouts/footer.php'); ?>
</body>
</html>
