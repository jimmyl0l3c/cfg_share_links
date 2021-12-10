/**
 * @copyright Copyright (c) 2018 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
// import { generateFilePath } from '@nextcloud/router'

import Vue from 'vue'
// import App from './App'
// import RenameLink from './RenameLink'
import NewLink from './NewLink'
// import { translate as t, translatePlural as n } from '@nextcloud/l10n'

// eslint-disable-next-line
// __webpack_public_path__ = generateFilePath(appName, '', 'js/')

Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA

// Vue.mixin({
// methods: {
// t,
// n,
// },
// })

// const View = Vue.extend(RenameLink)
// let TabInstance = null

// Add rename button
window.addEventListener('DOMContentLoaded', () => {
	// eslint-disable-next-line no-console
	console.log('CFGSHARE LOADED')
	if (OCA.Sharing && OCA.Sharing.ExternalLinkActions) { // TODO: use ExternalSHareActions instead
		// eslint-disable-next-line no-console
		console.log('CFGSHARE IF')
		OCA.Sharing.ExternalLinkActions.registerAction({
			url: link => `https://share.diasporafoundation.org/?url=${link}`,
			name: 'Test action',
			icon: 'app',
		})
	}
})

// Add new section
let sectionInstance = null
const View = Vue.extend(NewLink)
window.addEventListener('DOMContentLoaded', function() {
	if (OCA.Sharing && OCA.Sharing.ShareTabSections) {
		OCA.Sharing.ShareTabSections.registerSection(
			async (el, fileInfo) => {
				if (sectionInstance) {
					sectionInstance.$destroy()
				}

				sectionInstance = new View({
					props: { fileInfo },
				})

				if (typeof el !== 'undefined') {
					sectionInstance.$mount(el[0])
				}
			})
	}
})
