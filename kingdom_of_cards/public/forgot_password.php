<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h2>Mot de passe oublié</h2>
        <form action="send_reset_link.php" method="POST">
            <label>Votre adresse email :</label>
            <input type="email" name="email" required>
            <button type="submit">Envoyer le lien de réinitialisation</button>
        </form>
        <p><a href="login.php">Retour à la connexion</a></p>
    </div>
</body>
</html>
