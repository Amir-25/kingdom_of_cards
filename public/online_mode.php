<?php
session_start();
require_once "../config.php";

$matchId = $_GET['match'] ?? '';

$user_id = $_SESSION['user_id'];

$client_id = 0;



// Deck utilisateur
$stmt = $pdo->prepare("
    SELECT c.id, c.nom, c.image_path, c.attaque, c.defense, c.rarete, c.effet, c.fusionnable
    FROM deck d
    JOIN cartes c ON d.card_id = c.id
    WHERE d.user_id = ?
    ORDER BY d.position ASC
");
$stmt->execute([$user_id]);
$user_deck = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Arena - Kingdom of Cards</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: url('../assets/arene.jpg') no-repeat center center fixed;
            background-size: cover; 
            height: 100vh;
            width: 100vw;
            font-family: 'Press Start 2P', cursive;
            color: #fff;
        }

        .slots{display:flex;justify-content:center;gap:10px;}
        .card-slot {
            width:80px;height:120px;border:2px solid #fff;border-radius:8px;
            background:rgba(0,0,0,0.6);background-size:cover;background-position:center;
        }
        .action-buttons {
            position:absolute;top:50%;right:20px;display:flex;flex-direction:column;gap:10px;
        }
        .action-buttons button {
    background: linear-gradient(145deg, #121212, #1c1c1c);
    border: 2px solid #00ffcc;
    border-radius: 10px;
    padding: 12px 18px;
    font-family: 'Press Start 2P', cursive;
    font-size: 10px;
    color: #00ffcc;
    text-shadow: 0 0 3px #00ffcc;
    box-shadow: 0 0 8px #00ffcc66;
    transition: all 0.2s ease;
    cursor: pointer;
}

.action-buttons button:hover {
    background: #00ffcc;
    color: #000;
    text-shadow: none;
    box-shadow: 0 0 15px #00ffccaa;
    transform: scale(1.05);
}

.action-buttons button:disabled {
    background: #333;
    border-color: #555;
    color: #777;
    box-shadow: none;
    cursor: not-allowed;
}

        .timer {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            padding: 15px 25px;
            border-radius: 10px;
            border: 3px solid #fff;
            font-size: 18px;
            text-align: center;
            z-index: 9999;
        }

        .pv {
            position:absolute;background:rgba(0,0,0,0.8);border:2px solid #00ff22;
            padding:8px 15px;border-radius:8px;color:#00ff22;text-shadow:0 0 5px #00ff22;font-size:16px;
        }
        #user-pv{bottom:300px;left:10px;}
        #opponent-pv{top:80px;left:10px;}
        
        .card-info{
            font-family:'Press Start 2P', cursive;
            animation:fade-in 0.2s;
        }

        @keyframes fade-in{
            from{opacity:0;transform:translateY(-10px);}
            to{opacity:1;transform:translateY(0);}
        }

        .selected {
            border: 3px solid #00ffff !important;
            box-shadow: 0 0 10px #00ffff;
        }

        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }



    </style>
</head>
<body>

<div class="timer">Temps restant : <span id="timer">60</span>s</div>

<div class="pv" id="opponent-pv">Adversaire : <span id="opponent-life">9000</span> PV</div>
<div class="pv" id="user-pv">Toi : <span id="user-life">9000</span> PV</div>

<!-- Slots adversaire -->
<div class="slots" id="opponent-slots" style="margin-top:10px;">
    <?php for ($i=0; $i<10; $i++): ?><div class="card-slot"></div><?php endfor; ?>
</div>

<!-- Arène adversaire -->
<div class="slots" id="opponent-arena" style="margin-top:10px;">
    <?php for ($i=0; $i<4; $i++): ?><div class="card-slot"></div><?php endfor; ?>
</div>

<!-- Arène joueur -->
<div class="slots" id="user-arena" style="position:absolute;bottom:160px;width:100%;">
    <?php for ($i=0; $i<4; $i++): ?><div class="card-slot"></div><?php endfor; ?>
</div>

<!-- Slots joueur -->
<div class="slots" id="user-slots" style="position:absolute;bottom:20px;width:100%;">
    <?php for ($i=0; $i<10; $i++): ?><div class="card-slot"></div><?php endfor; ?>
</div>

<div class="action-buttons">
    <button id="end-turn">Finir Tour</button>
    <button id="give-up">Abandonner</button>
    <button id="fusion-btn">Fusionner</button>
    <button id="attack-monster-btn">Attaquer Monstre</button>
    <button id="attack-pv-btn">Attaquer PV</button>
</div>


<audio id="audio-player" loop autoplay>
        <source src="../assets/solo_mode.mp3" type="audio/mpeg">
    </audio>

    <div class="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script src="audio.js"></script>

<script>
const userDeck=<?= json_encode($user_deck); ?>;

const opponentDeck= {}


let joueurActuel='opponent';


alert((joueurActuel==='user'?'Toi':'Ton adversaire')+" commence le match !");
console.log(userDeck);
console.log(<?= json_encode($user_id); ?>);

let userPV=9000,opponentPV=9000;

let timeLeft=60,timerElement=document.getElementById('timer');
timerElement.textContent=timeLeft;
let timerInterval=setInterval(updateTimer,1000);

let selectedCards = [];

let opponentSelectedCards = [];

let cardToAttackWith = null;

let canAttackThisTurn = true;

let opponentCardToAttackWith = null;

let enAttaque = false;

let enAttaqueOpponent = false;

let socket = null;

let arenaCards = {};

let connId = 0;

document.addEventListener("DOMContentLoaded", function(event) { 

    const host = window.location.hostname; 

    socket = new WebSocket('ws://192.168.0.181:8080');  


    socket.onopen = function () {
    console.log('Connecté au match'); 

};


// Gérer la réception des messages du serveur WebSocket
socket.onmessage = function (event) {
    try {
        const data = JSON.parse(event.data);
        console.log('Message reçu du serveur :', data);
        if(!data.connId){
            let value = data[Object.keys(data).find(key => key != connId)];

            
            let key = Object.keys(data).find(key => key != connId);

            arenaCards[key] = value;

            console.log(value);
            afficher(value,document.querySelectorAll("#opponent-arena .card-slot"));

            addRightClick(document.querySelectorAll("#opponent-arena .card-slot"), value, true);

            changeTurn();
            console.log(connId);

        }
        else if(connId===0){
            if(joueurActuel == 'opponent' && data.message == 'En attente d’un adversaire…'){
                changeTurn();
                console.log('aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa');
            }
            connId = data.connId;
            arenaCards[data.connId] = [];
        }

    } catch (e) {
        console.error('Erreur de traitement du message WebSocket:', e);
    }
};

// Gérer la fermeture de la connexion par le serveur ou en cas d’erreur
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
};

});
 








function updateTimer(){
    if(timeLeft>0){timeLeft--;timerElement.textContent=timeLeft;}
    else{changeTurn();}
}

// function changeTurn(){
//     joueurActuel=joueurActuel==='user'?'opponent':'user';
//     //alert("Tour de : "+(joueurActuel==='user'?'Toi':'Ton adversaire'));

//     console.log('rah derna change turn');
//     socket.send(JSON.stringify(userDeck));

//     timeLeft=60;timerElement.textContent=timeLeft;
// }

function afficher(deck,slots){
    slots.forEach((s,i)=>{if(deck[i])s.style.backgroundImage=`url(${deck[i].image_path})`;});
}

afficher(userDeck,document.querySelectorAll("#user-slots .card-slot"));
afficher(opponentDeck,document.querySelectorAll("#opponent-slots .card-slot"));

// Drag & Drop
function addDrag(slots,role){
    slots.forEach(slot=>{
        slot.draggable=true;
        slot.ondragstart=e=>{
            if(joueurActuel!==role||!slot.style.backgroundImage)e.preventDefault();
            else{e.dataTransfer.setData('text',slot.style.backgroundImage);slot.classList.add('dragging');}
        };
        slot.ondragend=()=>slot.classList.remove('dragging');
    });
}

function addDrop(arena, deck, role, deckData){
    arena.forEach(slot=>{
        slot.ondragover = e => e.preventDefault();
        slot.ondrop = e => {
            if (!slot.style.backgroundImage){
                let img = e.dataTransfer.getData('text');
                slot.style.backgroundImage = img;
                
                let filename = img.split('/').pop().replace(/["')]/g, '');
                let card = deckData.find(c => c.image_path.includes(filename));

                
                arenaCards[connId].push(card);
                
                if (card) {
                    slot.dataset.index = deckData.indexOf(card);
                }

                deck.forEach(s => {
                    if (s.style.backgroundImage === img) s.style.backgroundImage = '';
                });
            
                if (joueurActuel === role){
                    canAttackThisTurn = false;
                }            
            }
        };
    });


}



addDrag(document.querySelectorAll("#user-slots .card-slot"),'user');
addDrag(document.querySelectorAll("#opponent-slots .card-slot"),'opponent');
// Appel exact (à remplacer par le nouveau appel clair avec le deckData) :
addDrop(document.querySelectorAll("#user-arena .card-slot"), document.querySelectorAll("#user-slots .card-slot"), 'user', userDeck);
addDrop(document.querySelectorAll("#opponent-arena .card-slot"), document.querySelectorAll("#opponent-slots .card-slot"), 'opponent', opponentDeck);

document.getElementById('end-turn').onclick=()=>{

    sendCards();

    changeTurn();

};   

document.getElementById('give-up').onclick=()=>{
    clearInterval(timerInterval);
    let winner=joueurActuel==='user'?'Ton adversaire':'Toi';
    alert(winner+" a gagné par abandon !");
    setTimeout(()=>{window.location.href="home.php";},1000);
};

function afficher(deck,slots){
    slots.forEach((s,i)=>{
        if(deck[i]){
            s.style.backgroundImage=`url(${deck[i].image_path})`;
            s.dataset.index=i;
        }
    });
}

afficher(userDeck,document.querySelectorAll("#user-slots .card-slot"));
afficher(opponentDeck,document.querySelectorAll("#opponent-slots .card-slot"));

// Gestion affichage infos cartes au clic droit
function showCardInfo(cardData, slot, isOpponent = false){
    let existing = slot.querySelector('.card-info');
    if(existing){ existing.remove(); return; }

    const info = document.createElement('div');
    info.className = 'card-info';
    info.style = `position:absolute;${isOpponent ? 'top:100%;' : 'bottom:100%;'}left:0;width:200px;background:#000;color:#fff;padding:10px;border:2px solid #ffd700;border-radius:8px;z-index:999;text-align:left;font-size:10px;`;

    info.innerHTML = `
        <strong>${cardData.nom}</strong><br>
        ATK : ${cardData.attaque}<br>
        DEF : ${cardData.defense}<br>
        Rareté : ${cardData.rarete}<br>
        Effet : ${cardData.effet || 'Aucun'}<br>
        Fusionnable : ${cardData.fusionnable==1?'Oui':'Non'}
    `;
    slot.style.position = 'relative';
    slot.appendChild(info);
}

// Clic droit sur cartes
function addRightClick(slots, deckData, isOpponent = false){
    slots.forEach(slot=>{
        slot.oncontextmenu = e => {
            e.preventDefault();
            const idx = slot.dataset.index;
            if (idx !== undefined) {
                showCardInfo(deckData[idx], slot, isOpponent);
            }
        };
    });
}

// Pour le joueur (infos en haut)
addRightClick(document.querySelectorAll("#user-slots .card-slot"), userDeck, false);
addRightClick(document.querySelectorAll("#user-arena .card-slot"), userDeck, false);

// Pour l'adversaire (infos en bas)
addRightClick(document.querySelectorAll("#opponent-slots .card-slot"), opponentDeck, true);
addRightClick(document.querySelectorAll("#opponent-arena .card-slot"), opponentDeck, true);

// Gestion sélection cartes dans l'arène

/* function toggleSelect(slot, idCarte) {
    if (slot.classList.contains('selected')) {
        slot.classList.remove('selected');
        selectedCards = selectedCards.filter(id => id !== idCarte);
    } else {
        if (selectedCards.length < 2) {
            slot.classList.add('selected');
            selectedCards.push(idCarte);
        } else {
            alert('Tu ne peux sélectionner que 2 cartes pour fusionner.');
        }
    }
}

// Applique sélection aux cartes d'arène du joueur
document.querySelectorAll("#user-arena .card-slot").forEach(slot => {
    slot.onclick = () => {
        const bgImage = slot.style.backgroundImage;
        if (!bgImage) return;

        const filename = bgImage.split('/').pop().replace(/["')]/g, '');
        const card = userDeck.find(c => c.image_path.includes(filename));
        if (card) toggleSelect(slot, card.id);
    };
}); */


// Sélection cartes adverses
document.querySelectorAll("#opponent-arena .card-slot").forEach(slot => {
    slot.onclick = (e) => {
        const bgImage = slot.style.backgroundImage;
        if (!bgImage) return;

        const filename = bgImage.split('/').pop().replace(/["')]/g, '');
        console.log(filename);
        let value = arenaCards[Object.keys(arenaCards).find(key => key != connId)];
        let targetKey = Object.keys(arenaCards).find(key => key != connId);
        console.log(value);
        const card = value.find(c => c.image_path.includes(filename));
        if (!card) return;

        console.log(card);

        if (enAttaque && joueurActuel === 'user') {
            // Logique d'attaque
            enAttaque = false;

            if (cardToAttackWith.attaque > card.defense) {
                alert(`Tu as détruit "${card.nom}" adverse !`);
                arenaCards[targetKey] = arenaCards[targetKey].filter(c => !c.image_path.includes(filename));
                console.log(arenaCards);
                slot.style.backgroundImage = '';
                opponentPV -= (cardToAttackWith.attaque - card.defense);
                document.getElementById('opponent-life').textContent = opponentPV;

            } else if (cardToAttackWith.attaque < card.defense) {
                alert(`Ta carte "${cardToAttackWith.nom}" a été détruite.`);
                // Supprimer la carte du joueur
                document.querySelectorAll("#user-arena .card-slot").forEach(s => {
                    if (s.style.backgroundImage.includes(cardToAttackWith.image_path.split('/').pop())) {
                        s.style.backgroundImage = '';
                    }
                });

            } else { // Égalité
                alert(`Les deux cartes "${cardToAttackWith.nom}" et "${card.nom}" ont été détruites !`);
                slot.style.backgroundImage = '';
                document.querySelectorAll("#user-arena .card-slot").forEach(s => {
                    if (s.style.backgroundImage.includes(cardToAttackWith.image_path.split('/').pop())) {
                        s.style.backgroundImage = '';
                    }
                });
            }

            cardToAttackWith = null;
            canAttackThisTurn = false;

            document.querySelectorAll(".card-slot.selected").forEach(s => s.classList.remove('selected'));

            // Vérification de la fin de partie
            if (opponentPV <= 0) {
                alert("Bravo ! Tu as gagné la partie !");
                setTimeout(() => { window.location.href = "home.php"; }, 1000);
            }

            return;
        }

        if (joueurActuel !== 'opponent') return;

        // Sélection adverse normale (attaque et fusion)
        if (e.ctrlKey || e.shiftKey) { // Fusion adversaire
            if (slot.classList.contains('selected')) {
                slot.classList.remove('selected');
                opponentSelectedCards = opponentSelectedCards.filter(id => id !== card.id);
            } else {
                if (opponentSelectedCards.length < 2) {
                    slot.classList.add('selected');
                    opponentSelectedCards.push(card.id);
                } else {
                    alert('Tu ne peux sélectionner que 2 cartes pour fusionner.');
                }
            }
        } else { // Attaque adversaire
            document.querySelectorAll("#opponent-arena .card-slot").forEach(s => {
                if (s !== slot) s.classList.remove('selected');
            });

            if (opponentCardToAttackWith === card) {
                opponentCardToAttackWith = null;
                slot.classList.remove('selected');
            } else {
                opponentCardToAttackWith = card;
                slot.classList.add('selected');
            }

            opponentSelectedCards = [];
        }
    };
});

document.getElementById('fusion-btn').onclick = () => {
    let cartes = joueurActuel === 'user' ? selectedCards : opponentSelectedCards;
    let arenaId = joueurActuel === 'user' ? "#user-arena" : "#opponent-arena";
    let deckActuel = joueurActuel === 'user' ? userDeck : opponentDeck;

    if (cartes.length !== 2) {
        alert('Sélectionne exactement 2 cartes pour fusionner.');
        return;
    }

    fetch('../api/check_fusion.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({carte1: cartes[0], carte2: cartes[1]})
    })
    .then(response => response.json())
    .then(result => {
        if (result.fusion) {
            alert("Fusion réussie ! " + result.carte.nom + " apparait !");

            // Retirer cartes fusionnées et enlever sélection
            document.querySelectorAll(arenaId + " .card-slot.selected").forEach(slot => {
                slot.style.backgroundImage = '';
                slot.classList.remove('selected');
                delete slot.dataset.index;
            });

            if(joueurActuel === 'user') selectedCards = [];
            else opponentSelectedCards = [];

            // Chemin image
            let filename = result.carte.image_path.split('/').pop();
            let cheminFinal = `../assets/Cartes/${filename}`;

            // Ajouter carte fusionnée dans premier slot libre
            let emptySlot = Array.from(document.querySelectorAll(arenaId + " .card-slot"))
                .find(slot => !slot.style.backgroundImage || slot.style.backgroundImage === '');

            if (emptySlot) {
                emptySlot.style.backgroundImage = `url('${cheminFinal}')`;
                emptySlot.style.backgroundSize = 'cover';
                emptySlot.style.backgroundPosition = 'center';
                emptySlot.style.backgroundRepeat = 'no-repeat';

                emptySlot.dataset.index = deckActuel.length;
                deckActuel.push(result.carte);
            } else {
                alert('Aucun emplacement libre dans l\'arène.');
            }

            canAttackThisTurn = false;

        } else {
            alert(result.error);
        }
    })
    .catch(e => console.error("Erreur fusion:", e));
};

function sendCards() {
    socket.send(JSON.stringify(arenaCards));
}

function changeTurn(){
    joueurActuel = joueurActuel === 'user' ? 'opponent' : 'user';
    console.log('Jai fait change tuuuuuuuuuuuuuurn');
    // socket.send(JSON.stringify(arenaCards));

    alert("Tour de : " + (joueurActuel === 'user' ? 'Toi' : 'Ton adversaire'));
    timeLeft = 60;
    timerElement.textContent = timeLeft;

    // Désélectionner toutes les cartes automatiquement
    document.querySelectorAll(".card-slot.selected").forEach(slot => slot.classList.remove('selected'));

    // Réinitialisation complète des sélections
    selectedCards = [];
    opponentSelectedCards = [];
    cardToAttackWith = null;
    opponentCardToAttackWith = null;
    canAttackThisTurn = true;
}



document.querySelectorAll("#user-arena .card-slot").forEach(slot => {
    slot.onclick = (e) => {
        const bgImage = slot.style.backgroundImage;
        if (!bgImage) return;

        const filename = bgImage.split('/').pop().replace(/["')]/g, '');
        const card = userDeck.find(c => c.image_path.includes(filename));
        if (!card) return;

        if (enAttaqueOpponent && joueurActuel === 'opponent') {
            enAttaqueOpponent = false;

            if (opponentCardToAttackWith.attaque > card.defense) {
                alert(`Ta carte "${card.nom}" a été détruite par l'adversaire !`);
                slot.style.backgroundImage = '';
                userPV -= (opponentCardToAttackWith.attaque - card.defense);
                document.getElementById('user-life').textContent = userPV;

            } else if (opponentCardToAttackWith.attaque < card.defense) {
                alert(`La carte adverse "${opponentCardToAttackWith.nom}" a été détruite en t'attaquant !`);
                document.querySelectorAll("#opponent-arena .card-slot").forEach(s => {
                    if (s.style.backgroundImage.includes(opponentCardToAttackWith.image_path.split('/').pop())) {
                        s.style.backgroundImage = '';
                    }
                });

            } else { // Égalité
                alert(`Les deux cartes "${card.nom}" et "${opponentCardToAttackWith.nom}" ont été détruites !`);
                slot.style.backgroundImage = '';
                document.querySelectorAll("#opponent-arena .card-slot").forEach(s => {
                    if (s.style.backgroundImage.includes(opponentCardToAttackWith.image_path.split('/').pop())) {
                        s.style.backgroundImage = '';
                    }
                });
            }

            opponentCardToAttackWith = null;
            canAttackThisTurn = false;

            document.querySelectorAll(".card-slot.selected").forEach(s => s.classList.remove('selected'));

            if (userPV <= 0) {
                alert("Tu as perdu, l'adversaire a gagné !");
                setTimeout(() => { window.location.href = "home.php"; }, 1000);
            }

            return;
        }

        if (joueurActuel !== 'user') return;

        if (e.ctrlKey || e.shiftKey) { // Fusion joueur
            if (slot.classList.contains('selected')) {
                slot.classList.remove('selected');
                selectedCards = selectedCards.filter(id => id !== card.id);
            } else {
                if (selectedCards.length < 2) {
                    slot.classList.add('selected');
                    selectedCards.push(card.id);
                } else {
                    alert('Tu ne peux sélectionner que 2 cartes pour fusionner.');
                }
            }
        } else { // Attaque joueur
            document.querySelectorAll("#user-arena .card-slot").forEach(s => {
                if (s !== slot) s.classList.remove('selected');
            });

            if (cardToAttackWith === card) {
                cardToAttackWith = null;
                slot.classList.remove('selected');
            } else {
                cardToAttackWith = card;
                slot.classList.add('selected');
            }

            selectedCards = [];
        }
    };
});


document.getElementById('attack-monster-btn').onclick = () => {
    if (!cardToAttackWith) {
        alert("Sélectionne d'abord ta carte pour attaquer.");
        return;
    }

    let opponentArenaSlots = Array.from(document.querySelectorAll("#opponent-arena .card-slot"))
        .filter(slot => slot.style.backgroundImage !== '');

    if (opponentArenaSlots.length === 0) {
        alert("Aucun monstre adverse à attaquer !");
        return;
    }

    alert("Choisis le monstre adverse à attaquer.");

    enAttaque = true;
};

document.getElementById('attack-monster-btn').addEventListener('click', () => {
    if(joueurActuel !== 'opponent') return;

    if (!opponentCardToAttackWith) {
        alert("Sélectionne d'abord la carte adverse pour attaquer.");
        return;
    }

    let userArenaSlots = Array.from(document.querySelectorAll("#user-arena .card-slot"))
        .filter(slot => slot.style.backgroundImage !== '');

    if (userArenaSlots.length === 0) {
        alert("Aucun monstre à attaquer dans l'arène adverse !");
        return;
    }

    alert("Choisis le monstre à attaquer dans l'arène adverse.");

    enAttaqueOpponent = true;
});

document.getElementById('attack-pv-btn').onclick = () => {
    if (joueurActuel !== 'user') {
        alert("Ce n'est pas ton tour !");
        return;
    }

    if (!cardToAttackWith) {
        alert("Sélectionne d'abord ta carte pour attaquer directement les PV.");
        return;
    }

    let opponentArenaSlots = Array.from(document.querySelectorAll("#opponent-arena .card-slot"))
        .filter(slot => slot.style.backgroundImage !== '');

    if (opponentArenaSlots.length !== 0) {
        alert("Tu ne peux pas attaquer directement les PV tant que l'ennemi a des monstres dans son arène.");
        return;
    }

    opponentPV -= cardToAttackWith.attaque;
    document.getElementById('opponent-life').textContent = opponentPV;
    alert(`Tu as attaqué directement l'adversaire avec "${cardToAttackWith.nom}" et infligé ${cardToAttackWith.attaque} dégâts !`);

    cardToAttackWith = null;
    canAttackThisTurn = false;

    document.querySelectorAll(".card-slot.selected").forEach(s => s.classList.remove('selected'));

    if (opponentPV <= 0) {
        alert("Bravo ! Tu as gagné la partie !");
        setTimeout(() => { window.location.href = "home.php"; }, 1000);
    }
};

document.getElementById('attack-pv-btn').addEventListener('click', () => {
    if (joueurActuel !== 'opponent') return;

    if (!opponentCardToAttackWith) {
        alert("Sélectionne d'abord une carte pour attaquer directement les PV.");
        return;
    }

    let userArenaSlots = Array.from(document.querySelectorAll("#user-arena .card-slot"))
        .filter(slot => slot.style.backgroundImage !== '');

    if (userArenaSlots.length !== 0) {
        alert("L'adversaire ne peut pas attaquer directement tant que tu as des monstres dans ton arène !");
        return;
    }

    userPV -= opponentCardToAttackWith.attaque;
    document.getElementById('user-life').textContent = userPV;
    alert(`L'adversaire t'a directement attaqué avec "${opponentCardToAttackWith.nom}" et infligé ${opponentCardToAttackWith.attaque} dégâts !`);

    opponentCardToAttackWith = null;
    canAttackThisTurn = false;

    document.querySelectorAll(".card-slot.selected").forEach(s => s.classList.remove('selected'));

    if (userPV <= 0) {
        alert("Tu as perdu ! L'adversaire a gagné la partie !");
        setTimeout(() => { window.location.href = "home.php"; }, 1000);
    }
});

setInterval(() => {
    if(joueurActuel === 'user'){
        document.getElementById('attack-monster-btn').disabled = !(cardToAttackWith && canAttackThisTurn);
        document.getElementById('attack-pv-btn').disabled = !(cardToAttackWith && canAttackThisTurn);
    } else {
        document.getElementById('attack-monster-btn').disabled = !(opponentCardToAttackWith && canAttackThisTurn);
        document.getElementById('attack-pv-btn').disabled = !(opponentCardToAttackWith && canAttackThisTurn);
    }
}, 500);

</script>

</body>
</html>