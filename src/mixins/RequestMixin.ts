import '@nextcloud/dialogs/style.css'
import axios from '@nextcloud/axios'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import { ShareType } from '@nextcloud/sharing'
import type { CreateLinkRequest } from '../interfaces/requests/CreateLinkRequest'
import type { RenameByTokenRequest } from '../interfaces/requests/RenameByTokenRequest'
import type { ApiError } from '../interfaces/responses/ApiError'
import type { CustomShare } from '../interfaces/responses/CustomShare'
import type { ShareResponse } from '../interfaces/responses/ShareResponse'

export default {
	methods: {
		refreshSidebar(fileInfo: unknown): void {
			const shareTab = OCA.Files.Sidebar.state.tabs.find(
				(e) => e.id === 'sharing',
			)
			if (shareTab) {
				shareTab.update(fileInfo)
				console.debug('CfgShareLinks: Updated share tab')
			} else {
				console.debug('CfgShareLinks: No share tab to update')
			}
		},
		async createLink(
			path: string,
			token: string,
			password: string | null = '',
		): Promise<ShareResponse | ApiError> {
			const data: CreateLinkRequest = {
				path,
				shareType: ShareType.Link,
				tokenCandidate: token,
				password: password?.trim() ?? '',
			}

			try {
				const response: CustomShare = await axios.post(
					generateUrl('/apps/cfg_share_links/new'),
					data,
				)
				const returnValue: ShareResponse = { ret: 0, data: response.data }
				console.debug('CfgShareLinks: Custom public link created')
				showSuccess(t('cfg_share_links', 'Custom public link created'))
				return returnValue
			} catch (e) {
				const returnValue: ApiError = {
					ret: 1,
					message: e.response.data?.message,
				}
				if (e.response.data && e.response.data.message) {
					showError(t('cfg_share_links', e.response.data.message))
				} else {
					showError(
						t(
							'cfg_share_links',
							'Error occurred while creating public link',
						),
					)
				}
				console.error(
					'CfgShareLinks: Error occurred while creating public link',
				)
				console.error(e.response)
				return returnValue
			}
		},
		async renameLink(
			currentToken: string,
			tokenCandidate: string,
		): Promise<void> {
			const data: RenameByTokenRequest = {
				currentToken,
				tokenCandidate,
			}

			try {
				await axios.put(
					generateUrl('/apps/cfg_share_links/update-by-token'),
					data,
				)
				console.debug('CfgShareLinks: Public link renamed')
				showSuccess(t('cfg_share_links', 'Custom public link renamed'))
			} catch (e) {
				if (e.response.data && e.response.data.message) {
					showError(t('cfg_share_links', e.response.data.message))
				} else {
					showError(
						t(
							'cfg_share_links',
							'Error occurred while renaming public link',
						),
					)
					console.error('CfgShareLinks: Error while renaming public link')
					console.error(e.response)
				}
			}
		},
	},
}
