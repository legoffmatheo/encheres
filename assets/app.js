import { createApp } from 'vue';
import Bonjour from './js/compenents/Bonjour.vue';
import EnchereC from './js/compenents/EnchereClient.vue';
import EnchereA from './js/compenents/EnchereAdmin.vue';
import ProduitC from './js/compenents/ProduitClient.vue';
import ProduitA from './js/compenents/ProduitAdmin.vue';


createApp(Bonjour).mount('#app');
createApp(EnchereC).mount('#app01');
createApp(EnchereA).mount('#app11');
createApp(ProduitC).mount('#app02');
createApp(ProduitA).mount('#app12');


