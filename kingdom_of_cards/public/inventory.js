document.addEventListener("DOMContentLoaded", async function () {
    const cardList = document.querySelector(".card-list");
    const deckSlots = document.querySelectorAll(".slot");

    // Charger les cartes du joueur
    const response = await fetch("/api/inventory.php");
    const cards = await response.json();

    // Vérifier si on a bien reçu les cartes
    if (!Array.isArray(cards) || cards.length === 0) {
        console.log("Aucune carte trouvée pour cet utilisateur.");
        return;
    }

    // Afficher les cartes en bas
    cards.forEach(card => {
        const cardElement = document.createElement("div");
        cardElement.classList.add("card");
        cardElement.setAttribute("draggable", "true");
        cardElement.dataset.id = card.id;
        cardElement.innerHTML = `<img src="${card.image}" alt="${card.name}">`;
        cardList.appendChild(cardElement);

        // Ajouter l'événement de drag
        cardElement.addEventListener("dragstart", function (e) {
            e.dataTransfer.setData("cardId", card.id);
        });
    });

    // Permettre le drop des cartes dans les slots du deck
    deckSlots.forEach(slot => {
        slot.addEventListener("dragover", e => e.preventDefault());

        slot.addEventListener("drop", function (e) {
            e.preventDefault();
            const cardId = e.dataTransfer.getData("cardId");

            // Vérifier si la case est vide
            if (!this.dataset.cardId) {
                const selectedCard = cards.find(c => c.id == cardId);
                this.innerHTML = `<img src="${selectedCard.image}" alt="Card">`;
                this.dataset.cardId = cardId;
            }
        });
    });

    // Sauvegarde du deck
    document.getElementById("save-deck").addEventListener("click", async function () {
        const deck = Array.from(deckSlots).map(slot => slot.dataset.cardId).filter(id => id);

        if (deck.length !== 10) {
            alert("Le deck doit contenir exactement 10 cartes.");
            return;
        }

        const response = await fetch("/api/inventory.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ deck })
        });

        const result = await response.json();
        alert(result.success || result.error);
    });
});
