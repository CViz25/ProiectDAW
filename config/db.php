<?php
// Setări pentru conexiunea la baza de date
$host = 'localhost'; // adresa serverului de baze de date
$dbname = 'id21893952_rezerva_bilete'; // numele bazei de date
$username = 'id21893952_admin'; // utilizatorul pentru baza de date
$password = 'Test1234!'; // parola pentru utilizatorul bazei de date

// Încercăm să stabilim conexiunea folosind extensia PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Setăm modul de raportare a erorilor la excepții pentru a le putea gestiona mai ușor
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Activăm modul implicit de preluare a datelor ca array asociativ
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Am comentat linia de mai jos după ce am verificat că este ok conexiunea
    // echo "Conexiunea la baza de date a fost stabilită cu succes.";
} catch (PDOException $e) {
    // În caz de eroare, oprește execuția și afișează mesajul de eroare
    die("Nu s-a putut conecta la baza de date: " . $e->getMessage());
}
?>
