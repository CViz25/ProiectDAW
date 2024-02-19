<?php
require_once '/storage/ssd2/952/21893952/public_html/config/db.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$nume = $email = $password = $confirm_password = "";
$nume_err = $email_err = $password_err = $confirm_password_err = $captcha_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificare nume gol
    if (empty(trim($_POST["nume"]))) {
        $nume_err = "Te rog introdu numele.";
    } else {
        $nume = trim($_POST["nume"]);
    }

    // Verificare email gol
    if (empty(trim($_POST["email"]))) {
        $email_err = "Te rog introdu un email.";
    } else {
        // Pregătirea unei declarații select
        $sql = "SELECT id FROM utilizatori WHERE email = :email";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $param_email = trim($_POST["email"]);
			// Verifică dacă email-ul este unic (nu există in DB)
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $email_err = "Acest email este deja folosit.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Ceva nu a funcționat corect. Te rog încearcă mai târziu.";
            }
            unset($stmt);
        }
    }

    // Verificare parolă
    if (empty(trim($_POST["password"]))) { // Parolă goală
        $password_err = "Te rog introdu o parolă.";
    } elseif (strlen(trim($_POST["password"])) < 6) { // Parolă sub 6 caractere
        $password_err = "Parola trebuie să aibă cel puțin 6 caractere.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Verificare confirmare parolă = parolă
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Te rog confirmă parola.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Parolele nu se potrivesc.";
        }
    }

    // Validarea reCAPTCHA
    $captchaResponse = $_POST['g-recaptcha-response'];
    if (empty($captchaResponse)) {
        $captcha_err = "Confirmă că nu ești un robot.";
    } else {
        $secretKey = "6Lf-XHcpAAAAALBNnWqgHYCWTn2E4Z6fm7LE45zo";
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $captchaResponse);
        $responseKeys = json_decode($response, true);
        if (!$responseKeys["success"]) {
            $captcha_err = "Verificarea CAPTCHA a eșuat.";
        }
    }

    // Verificăm dacă nu există erori înainte de a introduce în baza de date
    if (empty($nume_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($captcha_err)) {
        $sql = "INSERT INTO utilizatori (nume, email, parola, tip) VALUES (:nume, :email, :parola, 'user')";

        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(":nume", $param_nume, PDO::PARAM_STR);
            $stmt->bindParam(":email", $param_email, PDO::PARAM_STR);
            $stmt->bindParam(":parola", $param_password, PDO::PARAM_STR);

            $param_nume = $nume;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Crează o parolă hash

            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                echo "Oops! Ceva nu a funcționat corect. Te rog încearcă mai târziu.";
        }
        unset($stmt);
    }
}
unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Înregistrare</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { background-color: #f7f7f7; }
        .container { max-width: 400px; margin-top: 50px; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05); }
        .form-control { border-radius: 20px; }
        .btn-primary { border-radius: 20px; padding: 10px 20px; width: 100%; }
        .form-group span { color: #d9534f; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Înregistrare</h2>
        <p>Te rog completează formularul pentru a crea un cont.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Nume</label>
                <input type="text" name="nume" class="form-control" value="<?php echo $nume; ?>">
                <span class="help-block"><?php echo $nume_err; ?></span>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Parolă</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirmă Parola</label>
                <input type="password" name="confirm_password" class="form-control">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <div class="g-recaptcha" data-sitekey="6Lf-XHcpAAAAAP_4LgbPbmMWAobHfZMmNe43o7C1"></div> <!-- Înlocuiți CHEIA_TUA_PUBLICĂ cu cheia publică reală -->
                <span class="help-block"><?php echo $captcha_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Înregistrează-te">
            </div>
        </form>
    </div>    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
