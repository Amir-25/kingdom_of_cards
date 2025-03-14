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
            <h1 class="intro-title"> Kingdom of Cards </h1>
            <p>Un monde où seuls les meilleurs duellistes règnent...</p>
            <button class="start-button" onclick="startGame()">Appuyer pour continuer</button>
        </div>
    </div>
        <audio id="audio-player" loop autoplay>
            <source src="assets/background2.mp3" type="audio/mpeg">
            Votre navigateur ne supporte pas l'audio.
        </audio>

        <div class="audio-container">
            <label for="volume">🎵 Volume :</label>
            <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const audio = document.getElementById("audio-player");
                const volumeSlider = document.getElementById("volume");

                // Play audio
                audio.volume = 0.5; // Volume par défaut
                audio.play().catch(error => console.log("Autoplay bloqué par le navigateur :", error));

                // Modifier le volume
                volumeSlider.addEventListener("input", function () {
                 audio.volume = this.value;
                });
            });
        </script>
    

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

