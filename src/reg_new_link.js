import { translate as t, translatePlural as n } from '@nextcloud/l10n'
import { createApp } from 'vue'
import NewLink from './components/NewLink.vue'

console.debug('CfgShareLinks: NewLink init')

// Add new section
let sectionInstance = null
const props = null

window.addEventListener('DOMContentLoaded', function() {
	if (OCA.Sharing && OCA.Sharing.ShareTabSections) {
		OCA.Sharing.ShareTabSections.registerSection((el, fileInfo) => {
			console.debug(el)
			if (typeof fileInfo !== 'undefined' && typeof el !== 'undefined') {
				// if instance exists, just update props
				if (
					sectionInstance
					&& window.document.contains(sectionInstance.$el)
					&& props
				) {
					props.fileInfo = fileInfo
				} else {
					// create new instance
					if (sectionInstance) {
						// if sectionInstance.$el does not exist anymore (after changing folder for example)
						sectionInstance.unmount()
					}

					sectionInstance = createApp({ extends: NewLink }, {
						// props
						fileInfo,
					})

					sectionInstance.mixin({
						methods: {
							t,
							n,
						},
					})

					// props = Vue.observable({
					// 	...sectionInstance._props,
					// 	...{ fileInfo },
					// })
					// sectionInstance._props = props

					sectionInstance.mount(el[0])
				}
			}
		})
	}
})
