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

document.getElementById("remove-card").addEventListener("click", async () => {
    if (selectedSlot && selectedSlot.firstChild) {
        const card = selectedSlot.querySelector(".card");
        const imgSrc = card.querySelector("img").getAttribute("src");
        const altText = card.querySelector("img").getAttribute("alt");

        // Vider le slot côté visuel
        selectedSlot.innerHTML = "";
        selectedSlot.classList.remove("selected-slot");
        document.getElementById("remove-card").disabled = true;

        // Ajouter côté interface
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

        // Mise à jour en base de données
        /*await fetch("../api/save_card.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                carte_nom: altText
            })
        });*/
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





