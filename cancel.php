<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Plată anulată</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container text-center mt-5">
        <h1 class="text-danger">Plata a fost anulată</h1>
        <p>Nu am putut finaliza plata. Poți încerca din nou.</p>
        <a href="checkout.php" class="btn btn-warning mt-3">Înapoi la checkout</a>
    </div>
</body>
</html>
