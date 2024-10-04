/**
 * SPDX-FileCopyrightText: 2021 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

// import { getCurrentUser } from '@nextcloud/auth'
import { ShareType } from '@nextcloud/sharing'
import Vue from 'vue'

import RenameLink from './components/RenameLink.vue'

// Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA

interface ActionData {
	share: any // eslint-disable-line @typescript-eslint/no-explicit-any
	fileInfo: any // eslint-disable-line @typescript-eslint/no-explicit-any
	[key: string]: unknown
}

export class RenameShareAction {
	get id() {
		return 'rename-token'
	}

	get shareType() {
		return [ShareType.Link, ShareType.Email]
	}

	data({ share, fileInfo }: ActionData) {
		// Only works for files and existing shares
		//  || share.owner !== getCurrentUser()?.uid
		if (typeof share.token !== 'string' || fileInfo.type !== 'file') {
			return {}
		}

		return {
			text: '',
			is: RenameLink,
			fileInfo,
			share,
		}
	}

	get advanced() {
		return false
	}

	get handlers() {
		return {}
	}
}
