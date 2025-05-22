<?php

session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'server/connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = htmlspecialchars(trim($_POST["name"]));
    $email   = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST["message"]));

    // 1. Salvează mesajul în baza de date
    $stmt = $conn->prepare("INSERT INTO messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    $stmt->execute();
    $stmt->close();

    // 2. Trimite emailul
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'infocompanyemail2@gmail.com';     
        $mail->Password   = 'kfya epik efyv ncjq';               
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('infocompanyemail2@gmail.com', 'Formular Contact');
        $mail->addAddress('infocompanyemail2@gmail.com');
        $mail->addReplyTo($email, $name);

        $mail->isHTML(false);
        $mail->Subject = 'Mesaj nou de pe site';
        $mail->Body    = "Nume: $name\nEmail: $email\n\nMesaj:\n$message";

        $mail->send();
        echo "<script>alert('Mesajul a fost trimis și salvat cu succes!'); window.location.href='contact.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Eroare trimitere email: {$mail->ErrorInfo}'); window.history.back();</script>";
    }
} else {
    header("Location: contact.php");
    exit;
}
?>
