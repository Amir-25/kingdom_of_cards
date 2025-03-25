
document.addEventListener("DOMContentLoaded", function () {
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

        fetch("../api/buy.php", {
            method: "POST",
        })
        .then(response => response.text())
        .then(text => {
            console.log("Réponse brute de buy.php :", text);
            const data = JSON.parse(text);

            if (!data.success) {
                alert(data.message);
                return;
            }

            const moneySpan = document.getElementById("money");
            moneySpan.textContent = data.new_balance;

            revealBox.classList.add("shine-effect");

            setTimeout(() => {
                const cards = [
                    { name: "Gobelin Pyromane", file: "gobelin_pyromane.jpg", chance: 62 },
                    { name: "Serpent des Sables", file: "serpent_des_sables.jpg", chance: 62 },
                    { name: "Golem Mécanique", file: "golem_mecanique.jpg", chance: 62 },
                    { name: "Chimère Sanglante", file: "chimere_sanglante.jpg", chance: 20 },
                    { name: "Gardien Spectral", file: "gardien_spectral.jpg", chance: 20 },
                    { name: "Dragon du Néant", file: "dragon_du_neant.jpg", chance: 12 },
                    { name: "Chevalier de la Faille", file: "chevalier_de_la_faille.jpg", chance: 12 },
                    { name: "Roi des Profondeurs", file: "roi_des_profondeurs.jpg", chance: 5 },
                    { name: "Titan du Néant", file: "titan_du_neant.jpg", chance: 5 },
                    { name: "Seigneur du Chaos Abyssal", file: "seigneur_du_chaos_abyssal.jpg", chance: 1 }
                ];

                const totalWeight = cards.reduce((sum, c) => sum + c.chance, 0);
                let rand = Math.random() * totalWeight;
                let selected = null;
                for (const c of cards) {
                    rand -= c.chance;
                    if (rand <= 0) {
                        selected = c;
                        break;
                    }
                }

                const cardDiv = revealBox.querySelector(".cards-reveal");
                let rarity = "";
                let rarityClass = "";

                if (["Gobelin Pyromane", "Serpent des Sables", "Golem Mécanique"].includes(selected.name)) {
                    rarity = "Commun";
                    rarityClass = "rarity-common";
                } else if (["Chimère Sanglante", "Gardien Spectral"].includes(selected.name)) {
                    rarity = "Rare";
                    rarityClass = "rarity-rare";
                } else if (["Dragon du Néant", "Chevalier de la Faille"].includes(selected.name)) {
                    rarity = "Très Rare";
                    rarityClass = "rarity-trerare";
                } else if (["Roi des Profondeurs", "Titan du Néant"].includes(selected.name)) {
                    rarity = "Épique";
                    rarityClass = "rarity-epique";
                } else if (["Seigneur du Chaos Abyssal"].includes(selected.name)) {
                    rarity = "Légendaire";
                    rarityClass = "rarity-legendaire";
                }

                cardDiv.innerHTML = `
                    <h2 class="rarity-label ${rarityClass}">${rarity}</h2>
                    <img src="../assets/Cartes/${selected.file}" class="card-reveal ${rarityClass}" alt="${selected.name}">
                    <h3 class="card-name ${rarityClass}">${selected.name}</h3>
                `;

                revealBox.className = `reveal-box ${rarityClass}`;
                revealBox.style.display = "block";
                revealBox.classList.remove("shine-effect");

                // ✅ Sauvegarde de la carte tirée avec debug
                fetch("../api/save_card.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        carte_nom: selected.name
                    })
                })
                .then(r => r.text())
                .then(t => {
                    console.log("Réponse save_card.php :", t);
                    try {
                        const parsed = JSON.parse(t);
                        if (!parsed.success) alert("Erreur save_card: " + parsed.message);
                    } catch (e) {
                        alert("Erreur de parsing JSON dans save_card.php !");
                    }
                })
                .catch(e => console.error("Erreur save_card :", e));

            }, 1500);
        })
        .catch(error => {
            console.error("Erreur lors de l'achat :", error);
            alert("Une erreur est survenue.");
        });
    };

    window.closeReveal = function () {
        revealBox.style.display = "none";
    };
});







