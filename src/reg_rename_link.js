// import { createApp } from 'vue'
import RenameLink from './components/RenameLink.vue'

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
