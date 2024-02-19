	<?php
	require_once '/storage/ssd2/952/21893952/public_html/config/db.php';

	session_start();

	if (isset($_SESSION['user_id'])) {
		header("Location: index.php");
		exit;
	}

	$error = '';

	if ($_SERVER["REQUEST_METHOD"] == "POST") { 
	// Verifică metoda de solicitare pentru a asigura că datele sunt transmise prin metoda POST
	// Metoda POST este mai sigură pentru transferul datelor sensibile precum credențialele de autentificare
		$email = trim($_POST['email']);
		$password = trim($_POST['password']);

		if (empty($email) || empty($password)) {
			$error = 'Te rog completează toate câmpurile.';
		} else { // Folosim prepare și bindParam pentru a preveni injecțiile SQL
			$stmt = $pdo->prepare("SELECT id, parola, tip FROM utilizatori WHERE email = :email");
			$stmt->bindParam(":email", $email, PDO::PARAM_STR);
			$stmt->execute();

			if ($stmt->rowCount() == 1) {
				if ($row = $stmt->fetch()) {
					$id = $row['id'];
					$hashed_password = $row['parola'];
					$tip = $row['tip'];

					if (password_verify($password, $hashed_password)) { // Verifică dacă parola introdusă corespunde cu hash-ul parolei din DB
						$_SESSION['user_id'] = $id;
						$_SESSION['email'] = $email;
						$_SESSION['tip'] = $tip; // Stocăm tipul utilizatorului în sesiune (user sau admin)

						header("Location: index.php");
						exit;
					} else {
						$error = 'Parola introdusă nu este validă.';
					}
				}
			} else {
				$error = 'Nu există cont asociat cu acest email.';
			}
		}
	}
	?>


	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Autentificare</title>
		<!-- Bootstrap CSS -->
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
		<style>
			body { padding-top: 40px; padding-bottom: 40px; background-color: #f5f5f5; }
			.login-form { width: 100%; max-width: 330px; padding: 15px; margin: auto; }
			.login-form .checkbox { font-weight: 400; }
			.login-form .form-control { position: relative; box-sizing: border-box; height: auto; padding: 10px; font-size: 16px; }
			.login-form .form-control:focus { z-index: 2; }
			.login-form input[type="email"] { margin-bottom: -1px; border-bottom-right-radius: 0; border-bottom-left-radius: 0; }
			.login-form input[type="password"] { margin-bottom: 10px; border-top-left-radius: 0; border-top-right-radius: 0; }
		</style>
	</head>
	<body>
		<div class="login-form">
			<h2 class="text-center">Autentificare</h2>
			<p class="text-center">Pentru a accesa conținutul site-ului este necesar să aveți un cont.</p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-group">
					<label>Email</label>
					<input type="email" name="email" class="form-control" required autofocus>
				</div>    
				<div class="form-group">
					<label>Parola</label>
					<input type="password" name="password" class="form-control" required>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary btn-block">Login</button>
				</div>
				<?php if (!empty($error)): ?>
					<div class="alert alert-danger"><?php echo $error; ?></div>
				<?php endif; ?>
				<p class="text-center">Nu ai un cont? <a href="register.php">Înregistrează-te acum</a>.</p>
			</form>
		</div>    
	</body>
	</html>
