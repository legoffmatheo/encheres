<template>
  <div class="produits-app">
    <h1>Gestion des Produits</h1>
    <form @submit.prevent="submitForm">
      <input v-model="newProduit.libelle" placeholder="Libellé du produit" required />
      <input v-model="newProduit.description" placeholder="Description" required />
      <input v-model.number="newProduit.prixPlancher" type="number" placeholder="Prix Plancher" required />
      <button type="submit">{{ isEditing ? 'Mettre à jour' : 'Ajouter' }}</button>
      <button type="button" v-if="isEditing" @click="cancelEdit">Annuler</button>
    </form>
    <h1>Tableau des Produits</h1>
    <div class="table-container">
      <div class="table-header">
        <div>ID</div>
        <div>Libellé</div>
        <div>Description</div>
        <div>Prix Plancher</div>
        <div>Options</div>
      </div>
      <div 
        class="table-row" 
        v-for="produit in produits" 
        :key="produit.id"
      >
        <div>{{ produit.id }}</div>
        <div>{{ produit.libelle }}</div>
        <div>{{ produit.description }}</div>
        <div>{{ produit.prixPlancher }}</div>
        <button @click="deleteProduit(produit.id)">Supprimer</button>
        <button @click="editProduit(produit)">Modifier</button>
      </div>
    </div>
  </div>
</template>
<script>
import { ref, onMounted } from 'vue';

export default {
  name: 'ProduitApp',
  setup() {
    const produits = ref([]);
    const newProduit = ref({
      libelle: '',
      description: '',
      prixPlancher: 0,
    });
    const isEditing = ref(false);
    const editingProduitId = ref(null);

    const fetchProduits = async () => {
      try {
        const response = await fetch('/api/produits');
        if (!response.ok) throw new Error('Erreur lors du chargement des produits');
        produits.value = await response.json();
      } catch (error) {
        console.error(error.message);
      }
    };

    const addProduit = async () => {
      try {
        const response = await fetch('/api/produit/add', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(newProduit.value),
        });
        if (!response.ok) throw new Error("Erreur lors de l'ajout du produit");
        await fetchProduits();
        resetForm();
      } catch (error) {
        console.error(error.message);
      }
    };

    const updateProduit = async () => {
      try {
        const response = await fetch(`/api/produit/update/${editingProduitId.value}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(newProduit.value),
        });
        if (!response.ok) throw new Error("Erreur lors de la mise à jour du produit");
        await fetchProduits();
        resetForm();
      } catch (error) {
        console.error(error.message);
      }
    };

    const deleteProduit = async (id) => {
      try {
        const response = await fetch(`/api/produit/delete/${id}`, { method: 'DELETE' });
        if (!response.ok) throw new Error("Erreur lors de la suppression du produit");
        await fetchProduits();
      } catch (error) {
        console.error(error.message);
      }
    };

    const editProduit = (produit) => {
      newProduit.value = { ...produit };
      isEditing.value = true;
      editingProduitId.value = produit.id;
    };

    const resetForm = () => {
      isEditing.value = false;
      editingProduitId.value = null;
      newProduit.value = {
        libelle: '',
        description: '',
        prixPlancher: 0,
      };
    };

    const submitForm = async () => {
      if (isEditing.value) await updateProduit();
      else await addProduit();
    };

    onMounted(fetchProduits);

    return {
      produits,
      newProduit,
      isEditing,
      submitForm,
      deleteProduit,
      editProduit,
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