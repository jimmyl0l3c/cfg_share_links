import Vue from 'vue'
import AdminSettings from './components/AdminSettings.vue'

console.debug('CfgShareLinks: SettingsAdmin init')

const View = Vue.extend(AdminSettings)
new View().$mount('#cfgshare-admin-settings')
