<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Kingdom of Cards</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h2>Rejoignez le Royaume des Cartes</h2>
        <form id="register-form" method="post">
            <label>Nom d'utilisateur :</label>
            <input type="text" name="username" required>
            
            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <label>Confirmer le mot de passe :</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">S'inscrire</button>
        </form>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <p>Déjà un compte ? <a href="login.php">Connecte-toi ici</a></p>
    </div>
    <audio id="audio-player" loop autoplay>
        <source src="../assets/background.mp3" type="audio/mpeg">
        Votre navigateur ne supporte pas l'audio.
    </audio>

    <div class="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script>
        document.getElementById("register-form").addEventListener("submit", async function (event) {
            event.preventDefault();

            const formData = {
                username: document.querySelector("input[name='username']").value,
                password: document.querySelector("input[name='password']").value,
                confirm_password: document.querySelector("input[name='confirm_password']").value
            };

            const response = await fetch("../api/router.php/register", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData),
                credentials: "same-origin"
            });

            const result = await response.json();
            alert(result.success || result.error);

            if (result.success) {
                window.location.href = "login.php";
            }
        });
    </script>
    <script src="audio.js"></script>
</body>
</html>
