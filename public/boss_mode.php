<?php
session_start();
require_once "../config.php";

$user_id = $_SESSION['user_id'];

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

// Deck du Boss
$stmt = $pdo->prepare("
SELECT id, nom, image_path, attaque, defense, rarete, effet, fusionnable 
FROM cartes WHERE id = ?
");
$stmt->execute([16]);
$opponent_deck = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Arena - Kingdom of Cards</title>
    <link rel="stylesheet" href="../Styles/boss_mode.css">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>

<div id="custom-alert" style="display:none;">
    <div class="alert-box">
        <p id="alert-message"></p>
        <button onclick="closeAlert()">OK</button>
    </div>
</div>

<div class="timer">Temps restant : <span id="timer">60</span>s</div>
<div class="timer-bar-container">
  <div class="timer-bar" id="timer-bar"></div>
</div>

<div class="pv" id="opponent-pv">Adversaire : <span id="opponent-life">10000</span> PV</div>
<div class="pv" id="user-pv">Toi : <span id="user-life">15000</span> PV</div>




<!-- Arène adversaire -->
<div class="slots" id="opponent-arena" style="margin-top:10px;">
    <div class="card-slot" style="background-image:url('<?= $opponent_deck[0]['image_path'] ?>');" data-index="0"></div>
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
    <source src="../assets/arene2.mp3" type="audio/mpeg">
</audio>

<audio id="boss-audio">
    <source src="../assets/boss.mp3" type="audio/mpeg">
</audio>

<audio id="effet-audio">
    <source src="../assets/effet.mp3" type="audio/mpeg">
</audio>


    <div class="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <script src="audio.js"></script>

<script>
const userDeck=<?= json_encode($user_deck); ?>;
const opponentDeck=<?= json_encode($opponent_deck); ?>;
let joueurActuel = 'user';
showAlert("Tu commences le match contre Pierre !");
let tourBoss = 0; 

let userPV = 15000, opponentPV = <?= $opponent_deck[0]['defense'] ?>;
document.getElementById('opponent-life').textContent = opponentPV;

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

function updateTimer(){
    if (timeLeft > 0) {
        timeLeft--;
        timerElement.textContent = timeLeft;

        const percent = (timeLeft / 60) * 100; 
        document.getElementById('timer-bar').style.width = percent + "%";

     
        if (timeLeft <= 10) {
            document.getElementById('timer-bar').style.background = 'linear-gradient(90deg,rgb(248, 26, 26), #cc0000)';
            document.getElementById('timer-bar').classList.add('flash-bar'); 
            document.getElementById('timer').classList.add('flash-text'); 
        } else {
            document.getElementById('timer-bar').style.background = 'linear-gradient(90deg, #00ffcc, #0099cc)';
            document.getElementById('timer-bar').classList.remove('flash-bar'); 
            document.getElementById('timer').classList.remove('flash-text'); 
        }

    } else {
        changeTurn();
    }
    updateLifeBars();
}

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
addDrop(document.querySelectorAll("#user-arena .card-slot"), document.querySelectorAll("#user-slots .card-slot"), 'user', userDeck);

document.getElementById('end-turn').onclick=changeTurn;

document.getElementById('give-up').onclick=()=>{
    clearInterval(timerInterval);
    let winner=joueurActuel==='user'?'Ton adversaire':'Toi';
    showAlert(winner+" a gagné par abandon !");
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




// Sélection cartes adverses
document.querySelectorAll("#opponent-arena .card-slot").forEach(slot => {
    slot.onclick = (e) => {
        const bgImage = slot.style.backgroundImage;
        if (!bgImage) return;

        const filename = bgImage.split('/').pop().replace(/["')]/g, '');
        const card = opponentDeck.find(c => c.image_path.includes(filename));
        if (!card) return;

        if (enAttaque && joueurActuel === 'user') {
    enAttaque = false;

    showAlert(`Tu attaques Pierre avec "${cardToAttackWith.nom}" !`);

    opponentPV -= cardToAttackWith.attaque;
    document.getElementById('opponent-life').textContent = Math.max(opponentPV, 0);

    if (cardToAttackWith.attaque < opponentDeck[0].defense) {
        showAlert(`Ta carte "${cardToAttackWith.nom}" a été détruite en attaquant Pierre.`);
        document.querySelectorAll("#user-arena .card-slot").forEach(s => {
            if (s.style.backgroundImage.includes(cardToAttackWith.image_path.split('/').pop())) {
                s.style.backgroundImage = '';
            }
        });
    } else {
        showAlert(`Ta carte "${cardToAttackWith.nom}" survit à l'attaque.`);
    }

    cardToAttackWith = null;
    canAttackThisTurn = false;
    document.querySelectorAll(".card-slot.selected").forEach(s => s.classList.remove('selected'));

    if (opponentPV <= 0) {
        showAlert("Bravo ! Tu as vaincu Pierre!");
    
    // Appel AJAX vers un fichier PHP pour ajouter l'argent
    fetch('../api/reward_boss_win.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({reward: 300000})
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showAlert("Tu as gagné 300000 💰 !");
        } else {
            console.warn("Erreur de reward :", data.error);
        }
        setTimeout(() => {
            window.location.href = "home.php";
        }, 1000);
    });
    
    return;
}

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
                    showAlert('Tu ne peux sélectionner que 2 cartes pour fusionner.');
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
        showAlert('Sélectionne exactement 2 cartes pour fusionner.');
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
            showAlert("Fusion réussie ! " + result.carte.nom + " apparait !");

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
                showAlert('Aucun emplacement libre dans l\'arène.');
            }

            canAttackThisTurn = false;

        } else {
            alert(result.error);
        }
    })
    .catch(e => console.error("Erreur fusion:", e));
};


let effetBossActif = false;

function changeTurn(){
    joueurActuel = joueurActuel === 'user' ? 'opponent' : 'user';
    showAlert("Tour de : " + (joueurActuel === 'user' ? 'Toi' : 'Pierre Belisle'));
    timeLeft = 60;
    timerElement.textContent = timeLeft;

    document.querySelectorAll(".card-slot.selected").forEach(slot => slot.classList.remove('selected'));
    selectedCards = [];
    cardToAttackWith = null;
    canAttackThisTurn = true;

    if (joueurActuel === 'opponent') {
        tourBoss++;

        if (tourBoss === 1) {
            showAlert("Pierre t'observe et n'attaque pas ce tour !");
            setTimeout(changeTurn, 2000);
            return;
        }

        // Effet spécial (3ᵉ tour du boss)
        if (tourBoss === 3) {
            document.getElementById('effet-audio').play().catch(e => console.warn("Audio effet non lancé :", e));
            showAlert("Pierre Belisle active son effet : ATK de tes cartes à 0 pendant 60 s durant ton prochain tour !");
            effetBossActif = true;
        }


        setTimeout(bossAttaque, 1000);
    } else if (effetBossActif) {
        userDeck.forEach(carte => carte.attaqueOriginale = carte.attaque);
        userDeck.forEach(carte => carte.attaque = 0);
        setTimeout(() => {
            userDeck.forEach(carte => carte.attaque = carte.attaqueOriginale);
            effetBossActif = false;
            showAlert("L'effet de Pierre est terminé. Tes cartes récupèrent leur ATK.");
        }, 60000); // Durée exacte d'un tour joueur (60 sec)
    }
}

function bossAttaque() {
    
    document.getElementById('boss-audio').play().catch(e => console.warn("Audio boss non lancé :", e));
    
    let cartesJoueur = Array.from(document.querySelectorAll("#user-arena .card-slot"))
        .filter(slot => slot.style.backgroundImage !== '');

    if(cartesJoueur.length > 0) {
        cartesJoueur.sort((a,b) => {
            const idxA = a.dataset.index;
            const idxB = b.dataset.index;
            return userDeck[idxA].defense - userDeck[idxB].defense;
        });
        let cartePlusFaible = cartesJoueur[0];
        const idx = cartePlusFaible.dataset.index;
        const carte = userDeck[idx];

        if(opponentDeck[0].attaque > carte.defense){
            showAlert(`Pierre a détruit ta carte "${carte.nom}" !`);
            cartePlusFaible.style.backgroundImage = '';
            userPV -= opponentDeck[0].attaque - carte.defense;
        } else {
            showAlert(`Ta carte "${carte.nom}" résiste à l'attaque !`);
        }

    } else {
        userPV -= opponentDeck[0].attaque;
        showAlert(`Pierre attaque directement tes PV (${opponentDeck[0].attaque} dégâts) !`);
    }

    document.getElementById('user-life').textContent = Math.max(userPV, 0);

    if (userPV <= 0) {
        showAlert("Pierre t'a vaincu !");
        setTimeout(() => { window.location.href = "home.php"; }, 1000);
    } else {
        setTimeout(changeTurn, 2000);
    }
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
                showAlert(`Ta carte "${card.nom}" a été détruite par l'adversaire !`);
                slot.style.backgroundImage = '';
                userPV -= (opponentCardToAttackWith.attaque - card.defense);
                document.getElementById('user-life').textContent = userPV;

            } else if (opponentCardToAttackWith.attaque < card.defense) {
                showAlert(`La carte adverse "${opponentCardToAttackWith.nom}" a été détruite en t'attaquant !`);
                document.querySelectorAll("#opponent-arena .card-slot").forEach(s => {
                    if (s.style.backgroundImage.includes(opponentCardToAttackWith.image_path.split('/').pop())) {
                        s.style.backgroundImage = '';
                    }
                });

            } else { // Égalité
                showAlert(`Les deux cartes "${card.nom}" et "${opponentCardToAttackWith.nom}" ont été détruites !`);
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
                showAlert("Tu as perdu, l'adversaire a gagné !");
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
                    showAlert('Tu ne peux sélectionner que 2 cartes pour fusionner.');
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
        showAlert("Sélectionne d'abord ta carte pour attaquer.");
        return;
    }

    let opponentArenaSlots = Array.from(document.querySelectorAll("#opponent-arena .card-slot"))
        .filter(slot => slot.style.backgroundImage !== '');

    if (opponentArenaSlots.length === 0) {
        showAlert("Aucun monstre adverse à attaquer !");
        return;
    }

    showAlert("Choisis le monstre adverse à attaquer.");

    enAttaque = true;
};

document.getElementById('attack-monster-btn').addEventListener('click', () => {
    if(joueurActuel !== 'opponent') return;

    if (!opponentCardToAttackWith) {
        showAlert("Sélectionne d'abord la carte adverse pour attaquer.");
        return;
    }

    let userArenaSlots = Array.from(document.querySelectorAll("#user-arena .card-slot"))
        .filter(slot => slot.style.backgroundImage !== '');

    if (userArenaSlots.length === 0) {
        showAlert("Aucun monstre à attaquer dans l'arène adverse !");
        return;
    }

    showAlert("Choisis le monstre à attaquer dans l'arène adverse.");

    enAttaqueOpponent = true;
});

document.getElementById('attack-pv-btn').onclick = () => {
    if (joueurActuel !== 'user') {
        showAlert("Ce n'est pas ton tour !");
        return;
    }

    if (!cardToAttackWith) {
        showAlert("Sélectionne d'abord ta carte pour attaquer directement les PV.");
        return;
    }

    let opponentArenaSlots = Array.from(document.querySelectorAll("#opponent-arena .card-slot"))
        .filter(slot => slot.style.backgroundImage !== '');

    if (opponentArenaSlots.length !== 0) {
        showAlert("Tu ne peux pas attaquer directement les PV tant que l'ennemi a des monstres dans son arène.");
        return;
    }

    opponentPV -= cardToAttackWith.attaque;
    document.getElementById('opponent-life').textContent = opponentPV;
    showAlert(`Tu as attaqué directement l'adversaire avec "${cardToAttackWith.nom}" et infligé ${cardToAttackWith.attaque} dégâts !`);

    cardToAttackWith = null;
    canAttackThisTurn = false;

    document.querySelectorAll(".card-slot.selected").forEach(s => s.classList.remove('selected'));

    if (opponentPV <= 0) {
        showAlert("Bravo ! Tu as gagné la partie !");
        setTimeout(() => { window.location.href = "home.php"; }, 1000);
    }
};

document.getElementById('attack-pv-btn').addEventListener('click', () => {
    if (joueurActuel !== 'opponent') return;

    if (!opponentCardToAttackWith) {
        showAlert("Sélectionne d'abord une carte pour attaquer directement les PV.");
        return;
    }

    let userArenaSlots = Array.from(document.querySelectorAll("#user-arena .card-slot"))
        .filter(slot => slot.style.backgroundImage !== '');

    if (userArenaSlots.length !== 0) {
        showAlert("L'adversaire ne peut pas attaquer directement tant que tu as des monstres dans ton arène !");
        return;
    }

    userPV -= opponentCardToAttackWith.attaque;
    document.getElementById('user-life').textContent = userPV;
    showAlert(`L'adversaire t'a directement attaqué avec "${opponentCardToAttackWith.nom}" et infligé ${opponentCardToAttackWith.attaque} dégâts !`);

    opponentCardToAttackWith = null;
    canAttackThisTurn = false;

    document.querySelectorAll(".card-slot.selected").forEach(s => s.classList.remove('selected'));

    if (userPV <= 0) {
        showAlert("Tu as perdu ! L'adversaire a gagné la partie !");
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

function showAlert(message) {
    document.getElementById('alert-message').textContent = message;
    document.getElementById('custom-alert').style.display = 'flex';
}

function closeAlert() {
    document.getElementById('custom-alert').style.display = 'none';
}

</script>

</body>
</html>