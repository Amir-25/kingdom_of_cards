<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Kingdom of Cards</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <div class="container">
        <h2>Entrez dans l'Arène</h2>
        <form id="login-form" method="post">
            <label>Nom d'utilisateur :</label>
            <input type="text" name="username" required>

            <label>Mot de passe :</label>
            <input type="password" name="password" required>

            <button type="submit">Se connecter</button>
        </form>
        <p>Pas encore de compte ? <a href="register.php">Rejoins le Royaume</a></p>
        <p><a href="forgot_password.php">Mot de passe oublié ?</a></p>

    </div>

    <audio id="audio-player" loop autoplay>
        <source src="../assets/login.mp3" type="audio/mpeg">
    </audio>

    <div class="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script src="audio.js"></script>

    <script>
        document.getElementById("login-form").addEventListener("submit", async function (event) {
            event.preventDefault();

            const formData = {
                username: document.querySelector("input[name='username']").value,
                password: document.querySelector("input[name='password']").value
            };

            const response = await fetch("../api/router.php/login", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(formData),
                credentials: "same-origin"
            });

            const text = await response.text();
            console.log("Réponse serveur:", text);
            try {
                const result = JSON.parse(text);
                if (result.success) {
                    window.location.href = "home.php";
                } else {
                    alert(result.error || "Erreur inconnue");
                }
            } catch (e) {
                alert("Erreur de réponse du serveur.");
            }
        });
    </script>

</body>
</html>
