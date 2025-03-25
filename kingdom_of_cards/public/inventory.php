<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Kingdom of Cards</title>
    <link rel="stylesheet" href="../styles.css">

    <!-- ce bout doit etre placer dans le css une fois qu'il est bien organisé-->

    <style>
    .remove-button-container, .save-button-container, .back-button-container {
        text-align: right;
        margin: 20px;
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
    <div class="deck-slots">
         <div class="slot"></div>
         <div class="slot"></div>
         <div class="slot"></div>
         <div class="slot"></div>
         <div class="slot"></div>
         <div class="slot"></div>
         <div class="slot"></div>
         <div class="slot"></div>
         <div class="slot"></div>
         <div class="slot"></div>
    </div>

    <div class="remove-button-container">
        <button id="remove-card" disabled>Retirer la carte</button>
    </div>

    <div class="save-button-container">
        <button id="save-deck">Sauvegarder mon deck</button>
    </div>

    <div class="back-button-container">
        <button onclick="window.location.href='home.php'" class="back-button">Retour à l'accueil</button>
    </div>


    <div class="card-list">
        <div class="card">
            <img src="../assets/CARTES/behemoth_des_abysses.jpg" alt="Béhémoth des Abysses">
        </div>
        <div class="card">
            <img src="../assets/CARTES/chaos_celeste.jpg" alt="Chaos Céleste">
        </div>
        <div class="card">
            <img src="../assets/CARTES/chevalier_de_la_faille.jpg" alt="Chevalier de la Faille">
        </div>
        <div class="card">
            <img src="../assets/CARTES/chimere_sanglante.jpg" alt="Chimère Sanglante">
        </div>
        <div class="card">
            <img src="../assets/CARTES/dragon_du_neant.jpg" alt="Dragon du Néant">
        </div>
        <div class="card">
            <img src="../assets/CARTES/dragon_eclipse_infernale.jpg" alt="Dragon Éclipse Infernale">
        </div>
        <div class="card">
            <img src="../assets/CARTES/gardien_spectral.jpg" alt="Gardien Spectral">
        </div>
        <div class="card">
            <img src="../assets/CARTES/gobelin_pyromane.jpg" alt="Gobelin Pyromane">
        </div>
        <div class="card">
            <img src="../assets/CARTES/golem_apocalypse.jpg" alt="Golem Apocalyptique">
        </div>
        <div class="card">
            <img src="../assets/CARTES/golem_mecanique.jpg" alt="Golem Mécanique">
        </div>
        <div class="card">
            <img src="../assets/CARTES/roi_des_profondeurs.jpg" alt="Roi des Profondeurs">
        </div>
        <div class="card">
            <img src="../assets/CARTES/roi_destruction_totale.jpg" alt="Roi de la Destruction Totale">
        </div>
        <div class="card">
            <img src="../assets/CARTES/seigneur_du_chaos_abyssal.jpg" alt="Seigneur du Chaos Abyssal">
        </div>
        <div class="card">
            <img src="../assets/CARTES/serpent_des_sables.jpg" alt="Serpent des Sables">
        </div>
        <div class="card">
            <img src="../assets/CARTES/titan_du_neant.jpg" alt="Titan du Néant">
        </div>
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

</body>

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
                newCard.innerHTML = `<img src="../${card.image}" alt="${card.name}">`;

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


</html>
