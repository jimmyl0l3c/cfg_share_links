import '@nextcloud/dialogs/style.css'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export default {
	methods: {
		refreshSidebar(fileInfo) {
			const shareTab = OCA.Files.Sidebar.state.tabs.find(e => e.id === 'sharing')
			if (shareTab) {
				shareTab.update(fileInfo)
				console.debug('CfgShareLinks: Updated share tab')
			} else {
				console.debug('CfgShareLinks: No share tab to update')
			}
		},
		async createLink(path, token, password = '') {
			const data = {
				path,
				shareType: 3,
				tokenCandidate: token,
				password,
			}

			try {
				const response = await axios.post(generateUrl('/apps/cfg_share_links/new'), data)
				const returnValue = { ret: 0, data: response.data }
				console.debug('CfgShareLinks: Custom public link created')
				showSuccess(t('cfg_share_links', 'Custom public link created'))
				return returnValue
			} catch (e) {
				const returnValue = { ret: 1, data: e.response.data }
				if (e.response.data && e.response.data.message) {
					showError(t('cfg_share_links', e.response.data.message))
				} else {
					showError(t('cfg_share_links', 'Error occurred while creating public link'))
				}
				console.error('CfgShareLinks: Error occurred while creating public link')
				console.error(e.response)
				return returnValue
			}
		},
		async renameLink(id, path, currentToken, tokenCandidate) {
			const data = {
				id,
				path,
				currentToken,
				tokenCandidate,
			}

			try {
				await axios.post(generateUrl('/apps/cfg_share_links/update'), data)
				console.debug('CfgShareLinks: Public link renamed')
				showSuccess(t('cfg_share_links', 'Custom public link renamed'))
			} catch (e) {
				if (e.response.data && e.response.data.message) {
					showError(t('cfg_share_links', e.response.data.message))
				} else {
					showError(t('cfg_share_links', 'Error occurred while renaming public link'))
					console.error('CfgShareLinks: Error while renaming public link')
					console.error(e.response)
				}
			}
		},
	},
}
