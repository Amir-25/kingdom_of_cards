document.addEventListener("DOMContentLoaded", () => {
    const confirmBox = document.getElementById("confirmation");
    const revealBox = document.getElementById("reveal");

    window.confirmPurchase = function () {
        confirmBox.style.display = "block";
    };

    window.cancelBuy = function () {
        confirmBox.style.display = "none";
    };

    window.confirmBuy = function () {
        confirmBox.style.display = "none";

        fetch("../api/buy.php", { method: "POST" })
            .then(r => r.text())
            .then(t => {
                console.log("Réponse brute de buy.php :", t);
                const data = JSON.parse(t);

                if (!data.success) return alert(data.message);

                document.getElementById("money").textContent = data.new_balance;
                revealBox.classList.add("shine-effect");

                // Tirage aléatoire
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

                const total = cards.reduce((sum, c) => sum + c.chance, 0);
                let rand = Math.random() * total;
                let selected;

                for (const c of cards) {
                    rand -= c.chance;
                    if (rand <= 0) {
                        selected = c;
                        break;
                    }
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

                // Affichage
                const cardDiv = revealBox.querySelector(".cards-reveal");
                cardDiv.innerHTML = `
                    <h2 class="rarity-label ${rarityClass}">${rarity}</h2>
                    <img src="../assets/Cartes/${selected.file}" class="card-reveal ${rarityClass}" alt="${selected.name}">
                    <h3 class="card-name ${rarityClass}">${selected.name}</h3>
                `;

                revealBox.className = `reveal-box ${rarityClass}`;
                revealBox.style.display = "block";
                revealBox.classList.remove("shine-effect");

                // Envoi de la carte au backend
                fetch("../api/save_card.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
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
        document.getElementById("reveal").style.display = "none";
    };
});










