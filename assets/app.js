import { createApp } from 'vue';
import Base from './js/compenents/Accueil.vue';
import AVenir from './js/compenents/EncheresAVenir.vue';
import EnCours from './js/compenents/EnchereEnCours.vue';
import MesEnCours from './js/compenents/MesEncheresEnCours.vue';
import Terminees from './js/compenents/EncherresTerminees.vue';
import InscriptionEncherir from './js/compenents/InscriptionEncherir.vue';

createApp(Base).mount('#app');
createApp(AVenir).mount('#eav');
createApp(EnCours).mount('#ec');
createApp(MesEnCours).mount('#mec');
createApp(Terminees).mount('#ter');
createApp(InscriptionEncherir).mount('#ie');




