// Bootstrap
require('./bootstrap');
import 'bootstrap-css-only/css/bootstrap.min.css';
import 'mdbvue/lib/css/mdb.min.css';
import '../css/app.css';

window.Vue = require('vue');

// Vue, mainapp + router
import Vue from 'vue';
import App from './views/App';
import router from './router';
import store from './store';

// Mixins
import alertMixin from './mixins/alertMixin';

// Waves
import Waves from 'vue-waves-effect';
import 'vue-waves-effect/dist/vueWavesEffect.css';
Vue.use(Waves);

// Mixins
Vue.mixin(alertMixin);

/*-----------------------------------------------------------

  Components

-----------------------------------------------------------*/
/*-----------------------------------------------------------
  Navigation
-----------------------------------------------------------*/
// Navbar
Vue.component('navbar', require('./components/Navbar.vue').default);

/*-----------------------------------------------------------
  Forms
-----------------------------------------------------------*/
// Input
Vue.component('v-input', require('./components/forms/Input.vue').default);

/*-----------------------------------------------------------

  Vue instance

  -----------------------------------------------------------*/
new Vue({
  el: '#app',
  components: { App },
  router,
  store
}).$mount('#app')
