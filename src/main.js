// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './route.js'
import GAuth from 'vue-google-oauth2'
import {BootstrapVue,
        BIcon,
        BIconStar,
        BIconStarFill,
        BIconGoogle,
        BIconBoxArrowRight,
        BIconClipboardPlus,
        BIconClipboardCheck,
        BIconClipboardData,
        BIconExclamation,
        BIconCalendar,
        BIconChatText
        } from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue)
Vue.component('BIcon', BIcon)
Vue.component('BIconStar',BIconStar)
Vue.component('BIconStarFill',BIconStarFill)
Vue.component('BIconGoogle',BIconGoogle)
Vue.component('BIconBoxArrowRight',BIconBoxArrowRight)
Vue.component('BIconClipboardPlus',BIconClipboardPlus)
Vue.component('BIconClipboardCheck',BIconClipboardCheck)
Vue.component('BIconClipboardData',BIconClipboardData)
Vue.component('BIconExclamation',BIconExclamation)
Vue.component('BIconCalendar',BIconCalendar)
Vue.component('BIconChatText',BIconChatText)

Vue.config.productionTip = false
Vue.use(GAuth, {
  clientId: '240452811352-34mkkl305a9bo3ar2hi67j7b6kdstrj4.apps.googleusercontent.com', scope: 'email profile openid', prompt: 'select_account'
})

/* eslint-disable no-new */
new Vue({
  render: h => h(App),
  router
}).$mount('#app')