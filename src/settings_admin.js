import { translate as t, translatePlural as n } from '@nextcloud/l10n'
import Vue from 'vue'
import AdminSettings from './components/AdminSettings.vue'

// Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA

Vue.mixin({
	methods: {
		t,
		n,
	},
})

console.debug('CfgShareLinks: SettingsAdmin init')

const View = Vue.extend(AdminSettings)
new View().$mount('#cfgshare-admin-settings')
