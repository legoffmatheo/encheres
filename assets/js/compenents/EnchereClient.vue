<template>
  <div class="encheres-app">
    <h1>Tableau des Enchères</h1>
    <div class="table-container">
      <div class="table-header">
        <div>ID</div>
        <div>Titre</div>
        <div>Description</div>
        <div>Date Heure Début</div>
        <div>Date Heure Fin</div>
        <div>Statut</div>
        <div>Prix Début</div>
        <div>Produit</div>
        <div>Enchérir</div>
      </div>
      <div 
        class="table-row" 
        v-for="enchere in encheres" 
        :key="enchere.id"
      >
        <div>{{ enchere.id }}</div>
        <div>{{ enchere.titre }}</div>
        <div>{{ enchere.description }}</div>
        <div>{{ enchere.dateHeureDebut }}</div>
        <div>{{ enchere.dateHeureFin }}</div>
        <div>{{ enchere.statut }}</div>
        <div>{{ enchere.prixDebut }}</div>
        <div>{{ enchere.produit }}</div>
        <button @click="showParticipationForm(enchere)">Enchérir</button>
      </div>
    </div>

    <!-- Formulaire de participation -->
    <div v-if="selectedEnchere" class="participation-form">
      <h2>Ajouter une Participation à l'Enchère "{{ selectedEnchere.titre }}"</h2>
      <form @submit.prevent="submitParticipation">
        <div>
          <label for="prixEncheri">Prix Enchéri :</label>
          <input
            type="number"
            id="prixEncheri"
            v-model="participation.prixEncheri"
            required
            :min="selectedEnchere.prixMax || selectedEnchere.prixDebut"
          />
        </div>
        <div>
          <label for="budgetMaximum">Budget Maximum :</label>
          <input
            type="number"
            id="budgetMaximum"
            v-model="participation.budgetMaximum"
            required
            min="0"
          />
        </div>
        <button type="submit" :disabled="participation.prixEncheri > participation.budgetMaximum">Soumettre</button>
        <button type="button" @click="cancelParticipation">Annuler</button>
      </form>
    </div>
  </div>
</template>


<script>
import { ref, onMounted } from 'vue';

export default {
  name: 'EnchereApp',
  setup() {
    const encheres = ref([]); // Liste des enchères
    const selectedEnchere = ref(null); // Enchère sélectionnée
    const participation = ref({
      prixEncheri: '',
      budgetMaximum: '',
    });

    // Fonction pour afficher le formulaire d'enchère
    const showParticipationForm = (enchere) => {
      selectedEnchere.value = enchere;

      // Récupérer le prix max pour l'enchère sélectionnée
      fetch(`/api/encheresa/${enchere.id}`)
        .then((res) => res.json())
        .then((data) => {
          selectedEnchere.value.prixMax = data.prixMax || enchere.prixDebut;
        })
        .catch((err) => console.error('Erreur lors de la récupération du prix max:', err));
    };

    const submitParticipation = async () => {
  try {
    // Affichez les données envoyées pour voir ce qui est envoyé
    console.log('Données envoyées :', {
      prixEncheri: participation.value.prixEncheri,
      budgetMaximum: participation.value.budgetMaximum,
      enchereId: selectedEnchere.value.id,
    });

    const response = await fetch('/api/participation/add', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        prixEncheri: participation.value.prixEncheri,
        budgetMaximum: participation.value.budgetMaximum,
        enchereId: selectedEnchere.value.id,
      }),
    });

    // Vérification du statut de la réponse
    if (!response.ok) {
      const errorDetails = await response.json();
      console.error('Détails de l\'erreur API :', errorDetails); // Afficher les détails de l'erreur
      alert(`Erreur: ${errorDetails.error || 'Erreur inconnue'}`);
      return;
    }

    alert('Participation ajoutée avec succès');
    cancelParticipation();
    fetchEncheres(); // Recharger les enchères
  } catch (error) {
    // Affichez l'erreur complète dans la console
    console.error('Erreur lors de l’ajout de la participation :', error); // L'erreur complète du catch
    alert('Erreur lors de l’ajout de la participation');
  }
};




    // Fonction pour annuler la participation
    const cancelParticipation = () => {
      selectedEnchere.value = null;
      participation.value = { prixEncheri: '', budgetMaximum: '' };
    };

    // Fonction pour charger les enchères
    const fetchEncheres = async () => {
      try {
        const response = await fetch('/api/encheresc'); // Récupère les enchères
        if (!response.ok) throw new Error('Erreur lors du chargement des enchères');
        encheres.value = await response.json();
      } catch (error) {
        console.error('Erreur lors du chargement des enchères:', error.message);
      }
    };

    // Charger les enchères à l'initialisation
    onMounted(() => {
      fetchEncheres();
    });

    return {
      encheres,
      selectedEnchere,
      participation,
      showParticipationForm,
      submitParticipation,
      cancelParticipation,
    };
  },
};
</script>

<style scoped>
.encheres-app {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

h1 {
  text-align: center;
  margin-bottom: 20px;
}

.table-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.table-header,
.table-row {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border: 1px solid #ccc;
  background-color: #f9f9f9;
}

.table-header {
  font-weight: bold;
  background-color: #e9e9e9;
}

.table-row:nth-child(even) {
  background-color: #f4f4f4;
}

.participation-form {
  margin-top: 20px;
  padding: 20px;
  border: 1px solid #ccc;
  background-color: #fdfdfd;
}

.participation-form form div {
  margin-bottom: 10px;
}

.participation-form form button {
  margin-right: 10px;
}
</style>
