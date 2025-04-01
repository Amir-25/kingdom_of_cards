document.addEventListener("DOMContentLoaded", () => {
    const confirmBox = document.getElementById("confirmation");
    const revealBox = document.getElementById("reveal");
    const moneySpan = document.getElementById("money");

    // Récupération de l'ID utilisateur
    const userId = localStorage.getItem("user_id");

    if (!userId) {
        alert("Utilisateur non connecté (ID manquant)");
        return;
    }

    //Récupère le solde à l’ouverture
    fetch("../api/get_money.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ user_id: userId })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                moneySpan.textContent = data.money;
            } else {
                alert("Erreur : " + data.message);
            }
        });

    window.confirmPurchase = function () {
        confirmBox.style.display = "block";
    };

    window.cancelBuy = function () {
        confirmBox.style.display = "none";
    };

    window.confirmBuy = function () {
        confirmBox.style.display = "none";

        fetch("../api/buy.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ user_id: userId })
        })
            .then(r => r.text())
            .then(t => {
                console.log("Réponse brute de buy.php :", t);
                const data = JSON.parse(t);

                if (!data.success) return alert(data.message);

                moneySpan.textContent = data.new_balance;
                revealBox.classList.add("shine-effect");

                // Tirage aléatoire local (synchronisé avec la réponse du serveur)
                const cards = [
                    { id: 1, name: "Gobelin Pyromane", file: "gobelin_pyromane.jpg", chance: 62 },
                    { id: 2, name: "Serpent des Sables", file: "serpent_des_sables.jpg", chance: 62 },
                    { id: 3, name: "Golem Mécanique", file: "golem_mecanique.jpg", chance: 62 },
                    { id: 4, name: "Chimère Sanglante", file: "chimere_sanglante.jpg", chance: 20 },
                    { id: 5, name: "Gardien Spectral", file: "gardien_spectral.jpg", chance: 20 },
                    { id: 6, name: "Dragon du Néant", file: "dragon_du_neant.jpg", chance: 12 },
                    { id: 7, name: "Chevalier de la Faille", file: "chevalier_de_la_faille.jpg", chance: 12 },
                    { id: 8, name: "Roi des Profondeurs", file: "roi_des_profondeurs.jpg", chance: 5 },
                    { id: 9, name: "Titan du Néant", file: "titan_du_neant.jpg", chance: 5 },
                    { id: 10, name: "Seigneur du Chaos Abyssal", file: "seigneur_du_chaos_abyssal.jpg", chance: 1 }
                ];

                const selected = cards.find(c => c.id === data.card_id);

                if (!selected) {
                    alert("Carte non trouvée localement !");
                    return;
                }

                // Déterminer la rareté
                const rarityMap = {
                    Commun: [1, 2, 3],
                    Rare: [4, 5],
                    "Très Rare": [6, 7],
                    Épique: [8, 9],
                    Légendaire: [10]
                };

                let rarity = "";
                let rarityClass = "";

                for (const [label, ids] of Object.entries(rarityMap)) {
                    if (ids.includes(selected.id)) {
                        rarity = label;
                        rarityClass = "rarity-" + label.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase().replace(/\s+/g, "");
                        break;
                    }
                }

                // Affichage visuel
                const cardDiv = revealBox.querySelector(".cards-reveal");
                cardDiv.innerHTML = `
                    <h2 class="rarity-label ${rarityClass}">${rarity}</h2>
                    <img src="../assets/Cartes/${selected.file}" class="card-reveal ${rarityClass}" alt="${selected.name}">
                    <h3 class="card-name ${rarityClass}">${selected.name}</h3>
                `;

                revealBox.className = `reveal-box ${rarityClass}`;
                revealBox.style.display = "block";
                revealBox.classList.remove("shine-effect");

                // Enregistrement dans la BD
                fetch("../api/save_card.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        user_id: userId,
                        carte_id: selected.id,
                        carte_nom: selected.name
                    })
                })
                    .then(r => r.text())
                    .then(t => {
                        console.log("save_card response:", t);
                        try {
                            const parsed = JSON.parse(t);
                            if (!parsed.success) alert("Erreur: " + parsed.message);
                        } catch (e) {
                            alert("Erreur de parsing JSON dans save_card.php !");
                        }
                    })
                    .catch(e => console.error("Erreur save_card:", e));
            })
            .catch(e => {
                console.error("Erreur d'achat:", e);
                alert("Une erreur s'est produite.");
            });
    };

    window.closeReveal = function () {
        revealBox.style.display = "none";
    };
});
