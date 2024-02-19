<?php
require_once '/storage/ssd2/952/21893952/public_html/config/db.php';
require_once '/storage/ssd2/952/21893952/public_html/src/functions.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['tip'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$message = ''; // Pentru stocarea mesajelor de succes sau eroare

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titlu = trim($_POST['titlu']);
    $descriere = trim($_POST['descriere']);
    $data_evenimentului = trim($_POST['data_evenimentului']);
    $locatie = trim($_POST['locatie']);
    $pret = trim($_POST['pret']);
    $cantitate_disponibila = trim($_POST['cantitate_disponibila']);

    if (empty($titlu) || empty($data_evenimentului) || empty($locatie) || $pret <= 0 || $cantitate_disponibila <= 0) {
        $message = "<div class='alert alert-danger'>Toate câmpurile sunt obligatorii și trebuie să fie valide.</div>";
    } else {
        try {
            $sql = "INSERT INTO evenimente (titlu, descriere, data_evenimentului, locatie, pret, cantitate_disponibila) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$titlu, $descriere, $data_evenimentului, $locatie, $pret, $cantitate_disponibila])) {
                $message = "<div class='alert alert-success'>Eveniment adăugat cu succes.</div>";
            }
        } catch (Exception $e) {
            $message = "<div class='alert alert-danger'>A apărut o eroare la inserarea evenimentului: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administra Evenimente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 20px; }
        .container { max-width: 600px; }
        .form-control { margin-bottom: 10px; }
        .back-button { margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <a href="index.php" class="btn btn-secondary back-button">Înapoi la pagina principală</a>
    <h2>Adaugă un nou eveniment</h2>
    <?php echo $message; ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="titlu">Titlu</label>
            <input type="text" name="titlu" class="form-control" id="titlu" required>
        </div>
        <div class="form-group">
            <label for="descriere">Descriere</label>
            <textarea name="descriere" class="form-control" id="descriere" required></textarea>
        </div>
        <div class="form-group">
            <label for="data_evenimentului">Data Evenimentului</label>
            <input type="date" name="data_evenimentului" class="form-control" id="data_evenimentului" required>
        </div>
        <div class="form-group">
            <label for="locatie">Locație</label>
            <input type="text" name="locatie" class="form-control" id="locatie" required>
        </div>
        <div class="form-group">
            <label for="pret">Preț</label>
            <input type="number" name="pret" class="form-control" id="pret" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="cantitate_disponibila">Cantitate Disponibilă</label>
            <input type="number" name="cantitate_disponibila" class="form-control" id="cantitate_disponibila" required>
</div>
<button type="submit" class="btn btn-primary">Adaugă Eveniment</button>
</form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
