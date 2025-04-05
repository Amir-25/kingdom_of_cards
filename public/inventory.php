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
    <link rel="stylesheet" href="../Styles/styles.css">

    <!-- ce bout doit etre placer dans le css une fois qu'il est bien organisé-->

    <style>
    
    .deck-section {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 50px;
    margin-top: 40px;
}


#boutons_inv {
    position: absolute;      /* ou relative si tu veux qu’il se déplace dans un bloc parent */
    top: 230px;              /* ajuste verticalement */
    left: 1250px;              /* ajuste horizontalement */

    display: flex;
    flex-direction: column;
    gap: 15px;
}



    button {
        width: 200px; /* Définit une largeur fixe pour tous les boutons */
        padding: 8px 12px;
        font-size: 14px;
        cursor: pointer;
        background-color: rgba(255, 215, 0, 0.6);
        color: white;
        border: 2px solid gold;
        border-radius: 5px;
        transition: all 0.3s ease;
        box-shadow: none;
    }

    #remove-card:disabled {
        background-color: #ccc;
        color: #666;
        border: 2px solid #aaa;
        cursor: not-allowed;
    }

    #remove-card:hover:not(:disabled), #save-deck:hover, .back-button:hover {
        background-color: gold;
        color: black;
        box-shadow: 0 0 10px gold;
    }

    .selected-slot {
        border: 3px solid gold;
        border-radius: 8px;
        transform: scale(1.05);
        transition: all 0.3s ease;
        box-shadow: 0 0 20px gold;
    }
</style>


</head>
<body>

    <!-- Slots pour le deck -->
    <div class="deck-section">
    <div class="deck-slots">
        <?php for ($i = 0; $i < 10; $i++): ?>
            <div class="slot"></div>
        <?php endfor; ?>
    </div>

    <div id="boutons_inv">
        <div class="remove-button-container">
            <button id="remove-card" disabled>Retirer la carte</button>
        </div>
        <div class="save-button-container">
            <button id="save-deck">Sauvegarder mon deck</button>
        </div>
        <div class="back-button-container">
            <button onclick="window.location.href='home.php'" class="back-button">Retour à l'accueil</button>
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

    <script src="audio.js"></script>
    <script src="inventory.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
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

            if (slot.innerHTML.trim()==="") {
                const cardHTML = e.dataTransfer.getData("text/plain"); 
                const parser = new DOMParser();
                const cardElement = parser.parseFromString(cardHTML, "text/html").body.firstChild;

                cardElement.style.width = "120px";
                cardElement.style.height = "180px";
                cardElement.style.cursor = "default";

              
                slot.innerHTML = "";
                slot.appendChild(cardElement);

               
                document.querySelector(".card-list").querySelector(`[src="${cardElement.querySelector("img").src}"]`).parentElement.remove();
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

document.getElementById("remove-card").addEventListener("click", () => {
    if (selectedSlot && selectedSlot.firstChild) {
        const card = selectedSlot.querySelector(".card");
        const imgSrc = card.querySelector("img").getAttribute("src");
        const altText = card.querySelector("img").getAttribute("alt");

        // creeer une nouvelle carte dans la liste
        const newCard = document.createElement("div");
        newCard.classList.add("card");
        newCard.innerHTML = `<img src="${imgSrc}" alt="${altText}">`;
        document.querySelector(".card-list").appendChild(newCard);

        newCard.draggable = true;
        newCard.addEventListener("dragstart", function (e) {
            e.dataTransfer.setData("text/plain", newCard.outerHTML);
        });

        // vider le slot
        selectedSlot.innerHTML = "";
        selectedSlot.classList.remove("selected-slot");
        document.getElementById("remove-card").disabled = true;
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

});
</script>
</body>
</html>





