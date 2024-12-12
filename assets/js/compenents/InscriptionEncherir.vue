<template>
    <div class="inscription-encherir">
      <h2>Inscription à une enchère</h2>
      <div>
        <!-- Inclusion du composant des enchères en cours -->
        <encheres-en-cours @selectionner="choisirEnchere" />
      </div>
      <div v-if="enchereSelectionnee" class="formulaire-inscription">
        <h3>Vous avez sélectionné : {{ enchereSelectionnee.titre }}</h3>
        <form @submit.prevent="inscrireEncherir">
          <div class="form-group">
            <label for="budget">Budget maximum :</label>
            <input
              type="number"
              id="budget"
              v-model.number="budgetMaximum"
              placeholder="Entrez votre budget maximum"
              required
            />
          </div>
          <button type="submit">S'inscrire</button>
        </form>
      </div>
      <p v-if="message" :class="{ success: succes, error: !succes }">{{ message }}</p>
    </div>
  </template>
  
  <script>
  import { ref } from "vue";
  import EncheresEnCours from "./EnchereEnCours.vue"; 
  
  export default {
    components: {
      EncheresEnCours,
    },
    setup() {
      const enchereSelectionnee = ref(null);
      const budgetMaximum = ref(null);
      const message = ref(null);
      const succes = ref(false);
  
      // Méthode pour sélectionner une enchère
      const choisirEnchere = (enchere) => {
        enchereSelectionnee.value = enchere;
        message.value = null; // Réinitialiser le message
      };
  
      // Méthode pour inscrire l'utilisateur à l'enchère
      const inscrireEncherir = async () => {
        try {
          const reponse = await fetch("/api/encherir", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              idEnchere: enchereSelectionnee.value.id,
              budgetMaximum: budgetMaximum.value,
              prixEncheri: 0, // Toujours à 0 lors de l'inscription
            }),
          });
  
          if (reponse.ok) {
            const data = await reponse.json();
            message.value = data.resultat || "Inscription réussie.";
            succes.value = true;
          } else {
            const erreur = await reponse.json();
            message.value = erreur.message || "Erreur lors de l'inscription.";
            succes.value = false;
          }
        } catch (err) {
          message.value = "Erreur de connexion au serveur.";
          succes.value = false;
        }
      };
  
      return {
        enchereSelectionnee,
        budgetMaximum,
        message,
        succes,
        choisirEnchere,
        inscrireEncherir,
      };
    },
  };
  </script>
  
  <style>
  .inscription-encherir {
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: #f9f9f9;
  }
  
  .inscription-encherir h2,
  .inscription-encherir h3 {
    text-align: center;
    margin-bottom: 20px;
  }
  
  .formulaire-inscription {
    margin-top: 20px;
  }
  
  .form-group {
    margin-bottom: 15px;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }
  
  input {
    width: 100%;
    padding: 8px;
    box-sizing: border-box;
  }
  
  button {
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #45a049;
  }
  
  p.success {
    color: green;
    text-align: center;
  }
  
  p.error {
    color: red;
    text-align: center;
  }
  </style>
  