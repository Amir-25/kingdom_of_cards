<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Kingdom of Cards - Accueil</title>
    <link rel="stylesheet" href="../Styles/index.css">
</head>
<body>
    <div class="intro-container" id="introContainer" onclick="startGame()">
        <video id="introVideo" class="background-video" src="../assets/castle.mp4" preload="auto"></video>
        <img src="../assets/logoIndex.png" alt="Logo Kingdom of Cards" id="intro-logo">
        <div class="intro-text" id="introText">Appuyez pour entrer dans le royaume ...</div>
    </div>

    <script>
        function startGame() {
        const text = document.getElementById('introText');
        const video = document.getElementById('introVideo');
        const container = document.getElementById('introContainer');
        const logo = document.getElementById('intro-logo');

        // Masquer le logo
        logo.style.display = 'none';

        // Masquer le texte et afficher la vidéo
        text.style.display = 'none';
        video.style.display = 'block';
        video.play();
        container.style.background = 'none';

        setTimeout(() => {
            document.body.classList.add('fade-out');
    
           setTimeout(() => {
              window.location.href = "register.php";
            }, 1000); 
        }, 5000); 
    }

    </script>

</body>
</html>
