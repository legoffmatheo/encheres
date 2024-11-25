<template>
  <div class="conteur">
    <h1>{{mot}}</h1>
    <button @click="afficher">Afficher</button>
  </div>
</template>

<script>
import { ref } from 'vue';

export default {
  name: 'Bonjour',
  setup() {

  const mot = ref ('');

  const afficher =(()=>{
    mot.value = "Bonjour";
  })
  return{
    mot,
    afficher,
  }

  }
  };
</script>

<style scoped>

</style>
