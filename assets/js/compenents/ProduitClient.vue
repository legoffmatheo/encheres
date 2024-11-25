<template>
  <div class="produits-app"> 
    <h1>Tableau des Produits</h1>
    <div class="table-container">
      <div class="table-header">
        <div>ID</div>
        <div>Libellé</div>
        <div>Description</div>
        <div>Prix Plancher</div>
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

    onMounted(fetchProduits);

    return {
      produits,
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