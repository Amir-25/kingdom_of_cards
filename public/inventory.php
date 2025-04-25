<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../public/connexion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inventaire - Kingdom of Cards</title>
    <link rel="stylesheet" href="../Styles/inventory.css">
    <link href="https://fonts.googleapis.com/css2?family=Pirata+One&display=swap" rel="stylesheet">
    
    <style>
    #popup-confirm {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 99999;
    }
    .popup-box {
        background: #111;
        border: 3px solid #00bfff;
        padding: 25px 40px;
        border-radius: 15px;
        text-align: center;
        color: white;
        font-family: 'Pirata One', cursive;
        font-size: 24px;
        box-shadow: 0 0 20px #00bfff;
        animation: fadeIn 0.3s ease-in-out;
    }
    .popup-buttons {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 25px;
    }
    .popup-buttons button {
        padding: 10px 20px;
        background: #00bfff;
        color: black;
        font-weight: bold;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Pirata One', cursive;
        font-size: 20px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
        .popup-hidden {
        display: none !important;
    }
    </style>


</head>
<body>
    <h1 class="titre-inventaire">Inventaire</h1>

    <!-- Slots pour le deck -->
    <div class="deck-section">
    <div class="deck-container">
    <img src="../assets/pierre.PNG" class="deck-background" alt="fond pierre">   
    <div class="deck-slots">
        <?php for ($i = 0; $i < 10; $i++): ?>
            <div class="slot"></div>
        <?php endfor; ?>
    </div>
    </div>
    <div id="boutons_inv">
        <div class="remove-button-container">
            <button id="remove-card" disabled>Retirer la carte</button>
        </div>
        <div class="save-button-container">
            <button id="save-deck">Sauvegarder mon deck</button>
        </div>
        <div class="back-button-container">
            <button id="back-button" class="back-button">Retour à l'accueil</button>
        </div>
    </div>
</div>


    <div class="card-list">
        <!-- Les cartes seront injectées ici par JS -->
    </div>

    <audio id="audio-player" loop autoplay>
        <source src="../assets/inventory.mp3" type="audio/mpeg">
        Votre navigateur ne supporte pas l'audio.
    </audio>

    <div class="audio-container">
        <label for="volume">🎵 Volume :</label>
        <input type="range" id="volume" class="volume-slider" min="0" max="1" step="0.1" value="0.5">
    </div>

    <div id="popup-confirm" class="popup-hidden">
    <div class="popup-box">
        <p>⚠️ Vous n'avez pas sauvegardé votre deck. Voulez-vous vraiment quitter ?</p>
        <div class="popup-buttons">
            <button id="popup-cancel">Annuler</button>
            <button id="popup-confirm-leave">Quitter</button>
        </div>
    </div>
</div>


    <script src="audio.js"></script>
    <script src="inventory.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    let deckModifie = false; 

    const cards = document.querySelectorAll(".card"); 
    const slots = document.querySelectorAll(".slot"); 

   
    cards.forEach(card => {
        card.draggable = true; 

        card.addEventListener("dragstart", function (e) {
            e.dataTransfer.setData("text/plain", card.outerHTML); 
        });
    });


    slots.forEach(slot => {
        slot.addEventListener("dragover", function (e) {
            e.preventDefault();
            slot.classList.add("highlight"); 
            autoScroll(e);
        });

     
        slot.addEventListener("dragleave", function () {
            slot.classList.remove("highlight");
        });

   
        slot.addEventListener("drop", function (e) {
        e.preventDefault();
        slot.classList.remove("highlight"); 

        if (slot.innerHTML.trim() === "") {
            const cardHTML = e.dataTransfer.getData("text/plain"); 
            const parser = new DOMParser();
            const cardElement = parser.parseFromString(cardHTML, "text/html").body.firstChild;

            cardElement.style.width = "120px";
            cardElement.style.height = "180px";
            cardElement.style.cursor = "default";

            slot.innerHTML = "";
            slot.appendChild(cardElement);

            document.querySelector(".card-list").querySelector(`[src="${cardElement.querySelector("img").src}"]`).parentElement.remove();

            deckModifie = true;
        }
    });
    });

    function autoScroll(e) {
        const scrollSpeed = 10; 
        const scrollZone = 100;

        if (e.clientY < scrollZone) {
            window.scrollBy(0, -scrollSpeed);
        } else if (window.innerHeight - e.clientY < scrollZone) {
            window.scrollBy(0, scrollSpeed);
        }
    }

    let selectedSlot = null;

document.querySelectorAll(".slot").forEach(slot => {
    slot.addEventListener("click", () => {
        // Retire la sélection des autres slots
        document.querySelectorAll(".slot").forEach(s => s.classList.remove("selected-slot"));

        // si ce slot contient une carte
        if (slot.firstChild) {
            selectedSlot = slot;
            slot.classList.add("selected-slot");
            document.getElementById("remove-card").disabled = false;
        } else {
            selectedSlot = null;
            document.getElementById("remove-card").disabled = true;
        }
    });
});

document.getElementById("remove-card").addEventListener("click", async () => {
    if (selectedSlot && selectedSlot.firstChild) {
        const card = selectedSlot.querySelector(".card");
        const imgSrc = card.querySelector("img").getAttribute("src");
        const altText = card.querySelector("img").getAttribute("alt");

        selectedSlot.innerHTML = "";
        selectedSlot.classList.remove("selected-slot");
        document.getElementById("remove-card").disabled = true;

        const existing = [...document.querySelectorAll(".card")].find(c => c.querySelector("img").alt === altText);
        if (existing) {
            let qte = parseInt(existing.dataset.quantity);
            qte++;
            existing.dataset.quantity = qte;
            existing.querySelector(".card-count span").textContent = "x" + qte;
        } else {
            const newCard = createCardElement(altText, imgSrc, 1);
            document.querySelector(".card-list").appendChild(newCard);
            setupDragAndDrop();
        }

        deckModifie = true;
    }
});


document.getElementById("save-deck").addEventListener("click", async () => {
    const slots = document.querySelectorAll(".slot");
    const deck = [];

    slots.forEach((slot, index) => {
        const img = slot.querySelector("img");
        if (img) {
            const src = img.getAttribute("src");
            deck.push({ src: src, position: index });
        }
    });

    const response = await fetch("../api/router.php/save_deck", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ deck: deck }),
        credentials: "same-origin"
    });

    const result = await response.json();
    alert(result.success || result.error);

    deckModifie = false;
});

// charger le deck automatiquement
fetch("../api/router.php/load_deck", {
    method: "GET",
    credentials: "same-origin"
})
.then(response => response.json())
.then(data => {
    if (data.deck) {
        const slots = document.querySelectorAll(".slot");

        data.deck.forEach((card, index) => {
            if (slots[index]) {
                const newCard = document.createElement("div");
                newCard.classList.add("card");
                newCard.innerHTML = `<img src="${card.image_path}" alt="${card.nom}">`;

                newCard.style.width = "120px";
                newCard.style.height = "180px";
                newCard.style.cursor = "default";

                slots[index].innerHTML = "";
                slots[index].appendChild(newCard);
            }
        });
    }
});

    document.getElementById("back-button").addEventListener("click", function (e) {
        if (deckModifie) {
            e.preventDefault();
            document.getElementById("popup-confirm").classList.remove("popup-hidden");
        } else {
            window.location.href = "home.php";
        }
    });

    document.getElementById("popup-cancel").addEventListener("click", function () {
        document.getElementById("popup-confirm").classList.add("popup-hidden");
    });

    document.getElementById("popup-confirm-leave").addEventListener("click", function () {
        window.location.href = "home.php";
    });
});
</script>
</body>
</html>





