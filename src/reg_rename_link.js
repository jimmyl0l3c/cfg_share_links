import { translate as t, translatePlural as n } from '@nextcloud/l10n'
import Vue from 'vue'
import RenameLink from './components/RenameLink.vue'

// Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA

Vue.mixin({
	methods: {
		t,
		n,
	},
})

console.debug('CfgShareLinks: RenameLink init')

// Add rename input
window.addEventListener('DOMContentLoaded', () => {
	if (OCA.Sharing && OCA.Sharing.ExternalShareActions) {
		OCA.Sharing.ExternalShareActions.registerAction({
			id: 'rename-token',
			data: (action) => {
				return {
					text: '',
					share: action.share,
					fileInfo: action.fileInfo,
					is: RenameLink,
				}
			},
			shareType: [OC.Share.SHARE_TYPE_LINK, OC.Share.SHARE_TYPE_EMAIL],
			handlers: {
				update: (_) => {},
			},
		})
	}
})
