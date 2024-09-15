import { translate as t, translatePlural as n } from '@nextcloud/l10n'
import { createApp } from 'vue'
import AdminSettings from './components/AdminSettings.vue'

console.debug('CfgShareLinks: SettingsAdmin init')

const View = createApp(AdminSettings)

View.mixin({
	methods: {
		t,
		n,
	},
})

View.mount('#cfgshare-admin-settings')
