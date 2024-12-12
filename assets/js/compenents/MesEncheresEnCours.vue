<template>
  <div class="mes-encheres-en-cours">
    <h2>Mes enchères en cours</h2>
    <div v-if="encheres.length > 0" class="liste-encheres">
      <div class="carte-enchere" v-for="enchere in encheres" :key="enchere.id">
        <h3>{{ enchere.titre }}</h3>
        <p><strong>Prix le plus haut :</strong> {{ enchere.prixPlusHaut }} €</p>

        <!-- Champ pour entrer le montant de l'enchère -->
        <input
          type="number"
          v-model="enchere.montantEncheri"
          placeholder="Montant à enchérir"
        />
        <!-- Bouton pour soumettre l'enchère -->
        <button @click="encherir(enchere.id, enchere.montantEncheri)">
          Enchérir
        </button>
      </div>
    </div>
    <div v-else>
      <p>Aucune enchère en cours.</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      encheres: [], // Liste des enchères en cours
    };
  },
  methods: {
    // Récupérer les enchères en cours
    async chargerEncheresEnCours() {
      try {
        const response = await fetch("/api/encheres-en-cours-prix");
        if (!response.ok) throw new Error("Erreur lors du chargement des enchères");
        const data = await response.json();
        // Mettre à jour les enchères avec les nouvelles données
        this.encheres = data.map((enchere) => ({
          ...enchere,
          montantEncheri: 0, // Initialisation du montant à enchérir
        }));
      } catch (error) {
        console.error(error);
        alert("Impossible de charger les enchères en cours.");
      }
    },
    // Soumettre une enchère
    async encherir(enchereId, montant) {
      if (!montant || montant <= 0) {
        alert("Veuillez entrer un montant valide.");
        return;
      }

      try {
        const response = await fetch("/api/update-prix-encheri", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            participationId: enchereId,
            montant,
          }),
        });

        if (!response.ok) throw new Error("Erreur lors de l'enchère.");

        alert("Votre enchère a été soumise avec succès !");
        this.chargerEncheresEnCours(); // Recharger les enchères après soumission
      } catch (error) {
        console.error(error);
        alert("Impossible de soumettre l'enchère : " + error.message);
      }
    },
    // Mettre à jour les prix dynamiquement
    async mettreAJourPrix() {
      try {
        const response = await fetch("/api/encheres-en-cours-prix");
        if (!response.ok) throw new Error("Erreur lors de la mise à jour des prix");
        const data = await response.json();

        // Mettre à jour uniquement les prix dans les enchères locales
        data.forEach((enchereMiseAJour) => {
          const enchereLocale = this.encheres.find((e) => e.id === enchereMiseAJour.id);
          if (enchereLocale) {
            enchereLocale.prixPlusHaut = enchereMiseAJour.prixPlusHaut;
          }
        });
      } catch (error) {
        console.error("Erreur lors de la mise à jour des prix :", error);
      }
    },
  },
  mounted() {
    // Charger les enchères initiales
    this.chargerEncheresEnCours();

    // Mettre à jour les prix toutes les 5 secondes
    this.intervalId = setInterval(this.mettreAJourPrix, 5000);
  },
  beforeDestroy() {
    // Nettoyer l'intervalle pour éviter les fuites de mémoire
    clearInterval(this.intervalId);
  },
};
</script>

<style>
.mes-encheres-en-cours {
  max-width: 800px;
  margin: 20px auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: #f9f9f9;
}
.mes-encheres-en-cours h2 {
  text-align: center;
  margin-bottom: 20px;
}
.liste-encheres {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
}
.carte-enchere {
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 15px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  width: 300px;
  box-sizing: border-box;
}
.carte-enchere h3 {
  margin: 0 0 10px 0;
  font-size: 1.2em;
  color: #333;
}
.carte-enchere p {
  margin: 5px 0;
  font-size: 0.9em;
  color: #555;
}
</style>
