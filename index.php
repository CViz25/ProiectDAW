<?php
require_once '/storage/ssd2/952/21893952/public_html/config/db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$esteAdmin = isset($_SESSION['tip']) && $_SESSION['tip'] === 'admin';

try {
    $sql = "SELECT * FROM evenimente";
    $stmt = $pdo->query($sql);
    $evenimente = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "A apărut o eroare la preluarea evenimentelor: " . $e->getMessage();
    $evenimente = [];
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['rezervaBilet'])) {
    $id_eveniment = $_POST['id_eveniment'];
    $id_utilizator = $_SESSION['user_id'];
    $cantitate = 1;

    $pdo->beginTransaction();

    try {
        // Actualizează cantitatea disponibilă de bilete după ce se face o rezervare
        $sql = "UPDATE evenimente SET cantitate_disponibila = cantitate_disponibila - ? WHERE id = ? AND cantitate_disponibila >= ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$cantitate, $id_eveniment, $cantitate])) {
            // Verifică dacă actualizarea a avut loc
            if ($stmt->rowCount() > 0) {
                // Inserează rezervarea în baza de date
                $sql = "INSERT INTO rezervari (id_utilizator, id_eveniment, cantitate) VALUES (?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_utilizator, $id_eveniment, $cantitate]);

                $pdo->commit();
                header("Location: index.php");
                exit;
            } else {
                $error = "Numărul de bilete disponibile este insuficient.";
                $pdo->rollBack();
            }
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "A apărut o eroare la adăugarea rezervării: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acasă - Rezervare Bilete Online</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding-top: 20px; }
        footer { padding-top: 20px; border-top: 1px solid #eeeeee; margin-top: 20px; }
        .event-details { display: none; }
    </style>
<!-- Matomo - Pentru analytics vizitatori-->
<script>
  var _paq = window._paq = window._paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="https://rezervabilete.matomo.cloud/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '1']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.async=true; g.src='https://cdn.matomo.cloud/rezervabilete.matomo.cloud/matomo.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Matomo Code -->
</head>
<body>
<div class="container">
    <header class="mb-4">
        <h1 class="text-center">Bine ai venit la sistemul de rezervare bilete online!</h1>
    </header>

    <nav class="navbar navbar-expand-lg navbar-light bg-light rounded">
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Acasă</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Deconectare</a>
                </li>
				<li class="nav-item">
                <a class="nav-link" href="contact.php">Contact</a>
				</li>
                <?php if ($esteAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link" href="administrare_evenimente.php">Adaugă Eveniment (Admin)</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <main role="main" class="container">
        <div class="jumbotron">
            <h1 class="display-4">Sistemul de rezervare bilete</h1>
            <p>Regăsești mai jos lista cu evenimentele disponibile.</p>
            <p>Pentru a vedea detalii legate de un eveniment, te rog să faci click pe titlul evenimentului.</p>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php foreach ($evenimente as $eveniment): ?>
                <div class="event" id="event<?= $eveniment['id']; ?>">
                    <h4 style="cursor:pointer;" onclick="toggleDetails(<?= $eveniment['id']; ?>)"><?= htmlspecialchars($eveniment['titlu']); ?></h4>
                    <div class="event-details">
                        <p><?= htmlspecialchars($eveniment['descriere']); ?></p>
                        <p>Data evenimentului: <?= htmlspecialchars($eveniment['data_evenimentului']); ?></p>
                        <p>Locație: <?= htmlspecialchars($eveniment['locatie']); ?></p>
                        <p>Preț: <?= htmlspecialchars($eveniment['pret']); ?> lei</p>
                        <p>Cantitate disponibilă: <?= htmlspecialchars($eveniment['cantitate_disponibila']); ?></p>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <input type="hidden" name="id_eveniment" value="<?= $eveniment['id']; ?>">
                            <input type="hidden" name="rezervaBilet" value="true">
                            <button type="submit" class="btn btn-primary">Rezervare bilet</button>
                        </form>
                    </div>
                </div>
                <script>
                    function toggleDetails(eventId) {
                        var details = document.getElementById("event" + eventId).getElementsByClassName("event-details")[0];
                        details.style.display = details.style.display === "none" ? "block" : "none";
                    }
                </script>
            <?php endforeach; ?>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; Proiect DAW 2024 - Rezervare Bilete Online - VIZITIU Stefan-Catalin</p>
    </footer>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
