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
        <div>Option</div>  
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
        <button @click="editEncherir(enchere)">Enchérir</button>
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

    // Fonction pour récupérer les enchères
    const fetchEncheres = async () => {
      try {
        const response = await fetch('/api/encheres');
        if (!response.ok) {
          throw new Error('Erreur lors du chargement des enchères');
        }
        encheres.value = await response.json();
      } catch (error) {
        console.error('Erreur :', error.message);
      }
    };

    // Charger les enchères au montage du composant
    onMounted(() => {
      fetchEncheres();
    });

    return {
      encheres,
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
