<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Kingdom of Cards - Accueil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="intro-container">
        <div class="intro-content">
            <h1 class="intro-title">👑 Kingdom of Cards 👑</h1>
            <p>Un monde où seuls les meilleurs duellistes règnent...</p>
            <button class="start-button" onclick="startGame()">Appuyer pour continuer</button>
        </div>
    </div>

    <script>
        function startGame() {
            document.body.style.opacity = "0"; // Effet de fondu
            setTimeout(() => {
                window.location.href = "register.php";
            }, 500); // Redirection après 0.5s
        }
    </script>
</body>
</html>
