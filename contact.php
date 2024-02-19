<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/storage/ssd2/952/21893952/public_html/config/PHPMailer/src/Exception.php';
require '/storage/ssd2/952/21893952/public_html/config/PHPMailer/src/PHPMailer.php';
require '/storage/ssd2/952/21893952/public_html/config/PHPMailer/src/SMTP.php';

require_once '/storage/ssd2/952/21893952/public_html/config/db.php';

session_start();

$feedbackMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['g-recaptcha-response'])) {
    $secretKey = "6Lf-XHcpAAAAALBNnWqgHYCWTn2E4Z6fm7LE45zo";
    $captchaResponse = $_POST['g-recaptcha-response'];
    $verifyURL = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captchaResponse";

    $response = file_get_contents($verifyURL);
    $responseData = json_decode($response);

    if ($responseData && $responseData->success) {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'DOMENIU.RO';  // Server SMTP -- MODIFICAT PENTRU GITHUB DIN MOTIVE DE PRIVACY
            $mail->SMTPAuth = true;
            $mail->Username = 'USER@DOMENIU.RO'; // Username SMTP -- -- MODIFICAT PENTRU GITHUB DIN MOTIVE DE PRIVACY
            $mail->Password = 'PAROLA USER';   // Parolă SMTP -- MODIFICAT PENTRU GITHUB DIN MOTIVE DE PRIVACY
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('USER@DOMENIU.RO', 'Mailer');
            $mail->addAddress('numeprenume@gmail.com', 'Nume Prenume'); // -- MODIFICAT PENTRU GITHUB DIN MOTIVE DE PRIVACY

            $mail->isHTML(true);
            $mail->Subject = 'Mesaj nou de la ' . $_POST['nume'];
            $mail->Body    = 'Nume: ' . $_POST['nume'] . '<br>Email: ' . $_POST['email'] . '<br>Telefon: ' . $_POST['telefon'] . '<br>Mesaj:<br>' . $_POST['mesaj'];

            $mail->send();
            $feedbackMessage = 'Mesajul a fost trimis cu succes. Vă mulțumim!';
        } catch (Exception $e) {
            $feedbackMessage = 'A apărut o eroare și mesajul nu a fost trimis. Mailer Error: ' . $mail->ErrorInfo;
        }
    } else {
        $feedbackMessage = 'Verificarea reCAPTCHA a eșuat. Vă rugăm să încercați din nou.';
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<div class="container">
    <a href="index.php" class="btn btn-info mb-3">Înapoi la pagina principală</a>
    <h2>Contact</h2>
    <?php if (!empty($feedbackMessage)): ?>
    <div class="alert alert-info"><?php echo $feedbackMessage; ?></div>
    <?php endif; ?>
    <form action="contact.php" method="post">
        <div class="form-group">
            <label for="nume">Nume:</label>
            <input type="text" class="form-control" id="nume" name="nume" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="telefon">Telefon:</label>
            <input type="text" class="form-control" id="telefon" name="telefon">
        </div>
        <div class="form-group">
            <label for="mesaj">Mesaj:</label>
            <textarea class="form-control" id="mesaj" name="mesaj" rows="5" required></textarea>
        </div>
        <div class="g-recaptcha" data-sitekey="6Lf-XHcpAAAAAP_4LgbPbmMWAobHfZMmNe43o7C1"></div>
        <button type="submit" class="btn btn-primary">Trimite</button>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

