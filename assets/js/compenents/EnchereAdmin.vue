<template>
  <div class="encheres-app">
    <h1>Gestion des Enchères</h1>
    <form @submit.prevent="submitForm">
  <input v-model="newEnchere.titre" placeholder="Titre de l'enchère" required />
  <input v-model="newEnchere.description" placeholder="Description" required />
  <input v-model="newEnchere.dateHeureDebut" type="datetime-local" placeholder="Date Heure Début" required />
  <input v-model="newEnchere.dateHeureFin" type="datetime-local" placeholder="Date Heure Fin" required />
  <input v-model="newEnchere.statut" placeholder="Statut" required />
  <input v-model.number="newEnchere.prixDebut" type="number" placeholder="Prix Début" required />
  <!-- Champ pour sélectionner un produit -->
  <select v-model="newEnchere.produitId" required>
    <option v-for="produit in produits" :value="produit.id" :key="produit.id">
      {{ produit.libelle }}
    </option>
  </select>
  <button type="submit">{{ isEditing ? 'Mettre à jour' : 'Ajouter' }}</button>
  <button type="button" v-if="isEditing" @click="cancelEdit">Annuler</button>
</form>
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
        <div>Supprimer</div>
        <div>Modifier</div>
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
        <button @click="deleteEnchere(enchere.id)">Supprimer</button>
        <button @click="editEnchere(enchere)">Modifier</button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';

export default {
  name: 'EnchereApp',
  setup() {
    const encheres = ref([]);
    const produits = ref([]);

    const newEnchere = ref({
      titre: '',
      description: '',
      dateHeureDebut: '',
      dateHeureFin: '',
      statut: '',
      prixDebut: 0,
    });
    const isEditing = ref(false);
    const editingEnchereId = ref(null);

    // Fetch all encheres
    const fetchEncheres = async () => {
      try {
        const response = await fetch('/api/encheres');
        if (!response.ok) throw new Error('Erreur lors du chargement des enchères');
        encheres.value = await response.json();
      } catch (error) {
        console.error(error.message);
      }
    };


// Charger les produits disponibles
const fetchProduits = async () => {
  try {
    const response = await fetch('/api/produits');
    if (!response.ok) throw new Error('Erreur lors du chargement des produits');
    produits.value = await response.json();
  } catch (error) {
    console.error(error.message);
  }
};

    // Add a new enchere
    const addEnchere = async () => {
      try {
        const response = await fetch('/api/enchere/add', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(newEnchere.value),
        });
        if (!response.ok) throw new Error("Erreur lors de l'ajout de l'enchère");
        await fetchEncheres();
        resetForm();
      } catch (error) {
        console.error(error.message);
      }
    };

    // Update an existing enchere
    const updateEnchere = async () => {
      try {
        const response = await fetch(`/api/enchere/update/${editingEnchereId.value}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(newEnchere.value),
        });
        if (!response.ok) throw new Error("Erreur lors de la mise à jour de l'enchère");
        await fetchEncheres();
        resetForm();
      } catch (error) {
        console.error(error.message);
      }
    };

    const deleteEnchere = async (id) => {
      try {
        const response = await fetch(`/api/enchere/delete/${id}`, { method: 'DELETE' });
        if (!response.ok) throw new Error("Erreur lors de la suppression de l'enchère");
        await fetchEncheres();
      } catch (error) {
        console.error(error.message);
      }
    };



    const editEnchere = (enchere) => {
  // Remplir le formulaire avec les données existantes
  newEnchere.value = { 
    titre: enchere.titre,
    description: enchere.description,
    dateHeureDebut: enchere.dateHeureDebut,
    dateHeureFin: enchere.dateHeureFin,
    statut: enchere.statut,
    prixDebut: enchere.prixDebut,
    produitId: enchere.produitId // Ici, on prend l'ID du produit et non pas le produit lui-même
  };
  isEditing.value = true;
  editingEnchereId.value = enchere.id;
};




    const resetForm = () => {
      isEditing.value = false;
      editingEnchereId.value = null;
      newEnchere.value = {
        titre: '',
        description: '',
        dateHeureDebut: '',
        dateHeureFin: '',
        statut: '',
        prixDebut: 0,
      };
    };

    const submitForm = async () => {
      if (isEditing.value) await updateEnchere();
      else await addEnchere();
    };
    onMounted(() => {
  fetchProduits();
  fetchEncheres();
});

    return {
      encheres,
      produits,
      newEnchere,
      isEditing,
      submitForm,
      deleteEnchere,
      editEnchere,
      resetForm,
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
</style>
