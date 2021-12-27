import Vue from 'vue'
import { translate as t, translatePlural as n } from '@nextcloud/l10n'
import AdminSettings from './components/AdminSettings'

Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA

Vue.mixin({
	methods: {
		t,
		n,
	},
})

const View = Vue.extend(AdminSettings)
new View().$mount('#cfgshare-admin-settings')
