import '@nextcloud/dialogs/styles/toast.scss'
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
		async createLink(path, token) {
			const data = {
				path,
				shareType: 3,
				tokenCandidate: token,
			}

			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/new'), data)
				console.debug('CfgShareLinks: Custom public link created')
				console.debug(response)
				showSuccess(t('cfgsharelinks', 'Custom public link created'))
			} catch (e) {
				if (e.response.data && e.response.data.message) {
					showError(t('cfgsharelinks', e.response.data.message))
				} else {
					showError(t('cfgsharelinks', 'Error occurred while creating public link'))
				}
				console.error('CfgShareLinks: Error occurred while creating public link')
				console.error(e.response)
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
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/update'), data)
				console.debug('CfgShareLinks: Public link renamed')
				console.debug(response)
				showSuccess(t('cfgsharelinks', 'Custom public link renamed'))
			} catch (e) {
				if (e.response.data && e.response.data.message) {
					showError(t('cfgsharelinks', e.response.data.message))
				} else {
					showError(t('cfgsharelinks', 'Error occurred while renaming public link'))
					console.error('CfgShareLinks: Error while renaming public link')
					console.error(e.response)
				}
			}
		},
	},
}
