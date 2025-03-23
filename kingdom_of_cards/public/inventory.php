<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Kingdom of Cards</title>
    <link rel="stylesheet" href="../styles.css">
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
});
</script>


</html>
