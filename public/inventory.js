document.addEventListener("DOMContentLoaded", function () {
    fetch("../api/inventory.php")
        .then(response => response.json())
        .then(cartes => {
            const container = document.querySelector(".card-list");
            container.innerHTML = "";

            cartes.forEach(carte => {
                const cardDiv = createCardElement(carte.name, carte.image, carte.quantity);
                container.appendChild(cardDiv);
            });

            // Drag & Drop pour les slots
            setupDragAndDrop();
        })
        .catch(error => {
            console.error("Erreur lors du chargement des cartes :", error);
        });
});

// Auto-scroll pendant le drag
document.addEventListener("dragover", function (e) {
    const scrollMargin = 100;  // distance à partir du haut/bas pour déclencher le scroll
    const scrollSpeed = 10;    // vitesse du scroll

    const y = e.clientY;

    if (y < scrollMargin) {
        // Vers le haut
        window.scrollBy(0, -scrollSpeed);
    } else if (y > window.innerHeight - scrollMargin) {
        // Vers le bas
        window.scrollBy(0, scrollSpeed);
    }
});


// Crée une carte HTML
function createCardElement(name, image, quantity) {
    const cardDiv = document.createElement("div");
    cardDiv.classList.add("card");
    cardDiv.setAttribute("draggable", "true");
    cardDiv.dataset.name = name;
    cardDiv.dataset.image = image;
    cardDiv.dataset.quantity = quantity;

    const img = document.createElement("img");
    img.src = image;
    img.alt = name;

    const badge = document.createElement("div");
    badge.classList.add("card-count");
    const span = document.createElement("span");
    span.textContent = "x" + quantity;
    badge.appendChild(span);

    cardDiv.appendChild(img);
    cardDiv.appendChild(badge);

    return cardDiv;
}

function setupDragAndDrop() {
    const slots = document.querySelectorAll(".slot");

    document.querySelectorAll(".card").forEach(card => {
        card.addEventListener("dragstart", e => {
            e.dataTransfer.setData("text/plain", JSON.stringify({
                name: card.dataset.name,
                image: card.dataset.image
            }));
            card.classList.add("dragging");
        });

        card.addEventListener("dragend", e => {
            card.classList.remove("dragging");
        });
    });

    slots.forEach(slot => {
        slot.addEventListener("dragover", e => {
            e.preventDefault();
            slot.classList.add("highlight");
        });

        slot.addEventListener("dragleave", () => {
            slot.classList.remove("highlight");
        });
        slot.addEventListener("drop", e => {
            e.preventDefault();
            slot.classList.remove("highlight");
        
            const data = JSON.parse(e.dataTransfer.getData("text/plain"));
            if (slot.hasChildNodes()) return;
        
            // Cherche la carte dans l'inventaire
            const card = [...document.querySelectorAll(".card")].find(
                c => c.dataset.name === data.name && !c.classList.contains("in-slot")
            );
            if (!card) return;
        
            // Crée une copie propre pour le slot
            const cloned = card.cloneNode(true);
            cloned.classList.add("in-slot");
            cloned.classList.add("card"); // Assure la taille
            cloned.setAttribute("draggable", "false");
            cloned.querySelector(".card-count").remove(); // supprime le badge
            slot.appendChild(cloned);
        
            // Diminue la quantité dans l'inventaire
            let qte = parseInt(card.dataset.quantity);
            qte--;
            if (qte <= 0) {
                card.remove();
            } else {
                card.dataset.quantity = qte;
                card.querySelector(".card-count span").textContent = "x" + qte;
            }
        });
    
    });
}






