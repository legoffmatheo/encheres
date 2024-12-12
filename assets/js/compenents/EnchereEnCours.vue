<template>
  <div class="encheres-en-cours">
    <h2>Enchères en cours</h2>
    <div class="liste-encheres" v-if="encheres.length > 0">
      <div class="carte-enchere" v-for="enchere in encheres" :key="enchere.id">
        <h3>{{ enchere.titre }}</h3>
        <p><strong>Description :</strong> {{ enchere.description }}</p>
        <p><strong>Date de fin :</strong> {{ formaterDate(enchere.dateHeureFin) }}</p>
        <button @click="$emit('selectionner', enchere)">Sélectionner</button>
      </div>
    </div>
    <p v-else>Aucune enchère en cours pour le moment.</p>
  </div>
</template>

  
  <script>
  import { ref, onMounted } from "vue";
  
  export default {
    setup() {
      const encheres = ref([]);
      const erreur = ref(null);
  
      // Fonction pour récupérer les enchères
      const recupererEncheres = async () => {
        try {
          const reponse = await fetch("/api/encheres-en-cours");
          if (reponse.ok) {
            encheres.value = await reponse.json();
          } else {
            erreur.value = "Erreur lors du chargement des enchères.";
          }
        } catch (err) {
          erreur.value = "Erreur de connexion au serveur.";
        }
      };
  
      // Formater les dates
      const formaterDate = (chaineDate) => {
        const date = new Date(chaineDate);
        return date.toLocaleString();
      };
  
      // Récupérer les enchères lors du montage
      onMounted(() => {
        recupererEncheres();
      });
  
      return {
        encheres,
        erreur,
        formaterDate,
      };
    },
  };
  </script>
  
  <style>
  .encheres-en-cours {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #f9f9f9;
  }
  
  .encheres-en-cours h2 {
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
  
  p {
    text-align: center;
    font-style: italic;
    color: #888;
  }
  </style>
  