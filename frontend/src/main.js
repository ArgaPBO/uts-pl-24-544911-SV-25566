import { createApp } from 'vue'
import { RouterView } from 'vue-router'
import router from './router'

import 'bootstrap/dist/css/bootstrap.min.css'
import './style.css'
import App from './App.vue'

createApp(App)
  .use(router)
  .mount('#app')
