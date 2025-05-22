<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

$conn = new mysqli("localhost", "root", "", "licentaphp");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['subscribe'])) {
    $email = filter_var(trim($_POST['subscriber_email']), FILTER_VALIDATE_EMAIL);

    if ($email) {
        $stmt = $conn->prepare("SELECT id FROM subscribers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $insert = $conn->prepare("INSERT INTO subscribers (email) VALUES (?)");
            $insert->bind_param("s", $email);
            $insert->execute();

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'infocompanyemail2@gmail.com'; 
                $mail->Password = 'kfya epik efyv ncjq'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('infocompanyemail2@gmail.com', 'Charm');
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = 'Confirmare abonare - Charm';
                $mail->Body = "<h3>Mulțumim pentru abonare!</h3><p>Vei primi oferte speciale și noutăți de la Charm Shop.</p>";
                $mail->AltBody = "Multumim pentru abonare! Vei primi oferte speciale de la Charm Shop.";


                $mail->send();
                header("Location: ../index.php?subscribed=1");
            } catch (Exception $e) {
                header("Location: ../index.php?error=1");
            }
        } else {
            header("Location: ../index.php?exists=1");
        }
    } else {
        header("Location: ../index.php?invalid=1");
    }
}
