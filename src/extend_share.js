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
import NewLink from './NewLink'
import RenameLink from './RenameLink'
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

console.debug('CfgShareLinks init')

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
				update: (e) => {
					console.info(e)
					// console.info(this.$data.text)
					console.info('clicked')
				},
			},
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
