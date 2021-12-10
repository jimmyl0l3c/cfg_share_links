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
import { translate as t, translatePlural as n } from '@nextcloud/l10n'

// eslint-disable-next-line
// __webpack_public_path__ = generateFilePath(appName, '', 'js/')

Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA

Vue.mixin({
	methods: {
		t,
		n,
	},
})

// Add rename button
window.addEventListener('DOMContentLoaded', () => {
	console.info('CFGSHARE LOADED')
	if (OCA.Sharing && OCA.Sharing.ExternalLinkActions) { // TODO: use ExternalSHareActions instead
		console.info('CFGSHARE IF')
		OCA.Sharing.ExternalLinkActions.registerAction({
			url: link => `https://share.diasporafoundation.org/?url=${link}`,
			name: 'Test action',
			icon: 'app',
		})
	}
})

// Add new section
let sectionInstance = null
let props = null
const View = Vue.extend(NewLink)

window.addEventListener('DOMContentLoaded', function() {
	if (OCA.Sharing && OCA.Sharing.ShareTabSections) {
		OCA.Sharing.ShareTabSections.registerSection(
			(el, fileInfo) => {
				if (typeof fileInfo !== 'undefined' && typeof el !== 'undefined') {
					// if instance exists, just update props
					if (sectionInstance && window.document.contains(sectionInstance.$el) && props) {
						props.fileInfo = fileInfo
					} else { // create new instance
						if (sectionInstance) {
							// if sectionInstance.$el doesnt exist anymore (after changing folder for example)
							sectionInstance.$destroy()
						}

						sectionInstance = new View({
							props: { fileInfo },
						})

						props = Vue.observable({
						 ...sectionInstance._props,
						 ...{ fileInfo },
						})
						sectionInstance._props = props

						sectionInstance.$mount(el[0])
					}
				}
			})
	}
})
