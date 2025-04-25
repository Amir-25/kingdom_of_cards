<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Kingdom of Cards - Accueil</title>
    <link rel="stylesheet" href="../Styles/home.css">
</head>
<body>
    <div class="home-container"  id="home-container">
        <video id="versusVideo" class="versus-video" src="../assets/versus.mp4" preload="auto"></video>
        <h1 class="home-title" id="home-title">👑 Kingdom of Cards 👑</h1>

        <div class="menu" id="menu">
            <div class="grid-buttons">
                <button class="menu-button" id="playOnline"><img src="../assets/iconeEnLigne.png" class="icon" alt="En ligne"></button>
                <button class="menu-button" onclick="startVersus()"><img src="../assets/iconeSolo.png" class="icon" alt="Solo"></button>
                <button class="menu-button" onclick="location.href='inventory.php'"><img src="../assets/iconeInventaire.png" class="icon" alt="Inventaire"></button>
                <button class="menu-button" onclick="location.href='shop.php'"><img src="../assets/iconeMagasin.png" class="icon" alt="Magasin"></button>
            </div>
            <button class="menu-button boss-button" onclick="location.href='boss_mode.php'"><img src="../assets/bossIcone.png" class="iconBoss" alt="Magasin"></button>
            <!-- On retire le onclick pour la déconnexion et on ajoute un id -->
            <button class="menu-button logout-button" id="logout-btn"><img src="../assets/iconeDeconnecter.png" class="iconDeconnecter" alt="Deconnecter"></button>

        </div>
    </div>

    <div id="statusMsg"></div>

    <audio id="audio-player" loop autoplay>
        <source src="../assets/home.mp3" type="audio/mpeg">
        Votre navigateur ne supporte pas l'audio.
    </audio>

    <div class="audio-container" id="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script>
        document.getElementById("logout-btn").addEventListener("click", function() {
            fetch("../api/router.php/logout", {
                credentials: "same-origin"
            })
            .then(response => response.json())
            .then(data => {
                alert(data.success);
                window.location.href = "login.php";
            });
        });
    </script>
    <script src="audio.js"></script>


    <script>
        function startVersus() {
        const text = document.getElementById('home-title');
        const video = document.getElementById('versusVideo');
        const container = document.getElementById('home-container');
        const menu = document.getElementById('menu');
        const audio = document.getElementById('audio-container');


        // Masquer les elements et jouer la vidéo
        text.style.display = 'none';
        menu.style.display = 'none';
        video.style.display = 'block';
        audio.style.display = 'none';
        video.play();
        container.style.background = 'none';

        setTimeout(() => {
            document.body.classList.add('fade-out');
    
           setTimeout(() => {
              window.location.href = "solo_mode.php";
            }, 1000); 
        }, 4000); 
    }
    </script>

<script>
document.getElementById('playOnline').addEventListener('click', function () {
    // 2.a Établir la connexion WebSocket vers le serveur Ratchet
    // Construire l'URL WebSocket : on utilise le même host que la page, avec le port du serveur WS
    const host = window.location.hostname; 
    
    //const socket = new WebSocket('ws://' + host + ':8080');  // utilisez 'wss://' si SSL/TLS

    const socket = new WebSocket('ws://172.20.10.2:9000'); //Remplace 192.168.1.42 par l’IP réelle que tu as 

    // (Optionnel) Indiquer à l'utilisateur que la recherche de match a commencé
    const statusDiv = document.getElementById('statusMsg');
    if (statusDiv) {
        statusDiv.textContent = 'Connexion au serveur de jeu...';
    }

    // 2.b Gérer l'ouverture de la connexion
    socket.onopen = function () {
        console.log('Connecté au serveur de matchmaking.');
        if (statusDiv) {
            statusDiv.textContent = 'En attente d’un autre joueur...';
        }
        // A l'ouverture, notre serveur nous place automatiquement en file d'attente.
        // (Pas besoin d'envoyer un message "join", sauf si on souhaitait transmettre des infos supplémentaires.)
    };

    // Gérer la réception des messages du serveur WebSocket
    socket.onmessage = function (event) {
        try {
            const data = JSON.parse(event.data);
            console.log('Message reçu du serveur :', data);
            if (data.action === 'status') {
                // Mise à jour de statut (attente, etc.)
                if (statusDiv) {
                    statusDiv.textContent = data.message || '';
                }
            } else if (data.action === 'matchFound') {
                // 2.c Match trouvé : rediriger vers la page de combat avec l'ID de match
                const matchId = data.matchId;
                if (statusDiv) {
                    statusDiv.textContent = 'Match trouvé ! Redirection en cours...';
                }
                // Rediriger vers battle.php en passant l'identifiant de match dans l'URL
                window.location.href = 'online_mode.php?match=' + encodeURIComponent(matchId);
            }
        } catch (e) {
            console.error('Erreur de traitement du message WebSocket:', e);
        }
    };

    // Gérer la fermeture de la connexion (par le serveur ou en cas d’erreur)
    socket.onclose = function () {
        console.log('Connexion au serveur de matchmaking fermée.');
        // Si la fermeture survient avant qu'un match ne soit trouvé, on peut informer l'utilisateur
        if (statusDiv && !statusDiv.textContent.includes('Match trouvé')) {
            statusDiv.textContent = 'La connexion au serveur de jeu a été fermée. Veuillez réessayer.';
        }
    };

    // Gérer les erreurs de la connexion WebSocket
    socket.onerror = function (error) {
        console.error('WebSocket error:', error);
        // On peut afficher un message d'erreur à l'utilisateur
    };
});
</script>


</body>
</html>
