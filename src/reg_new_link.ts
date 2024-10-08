import Vue from 'vue'
import NewLink from './components/NewLink.vue'

// Vue.prototype.OC = window.OC
Vue.prototype.OCA = window.OCA

console.info('CfgShareLinks: NewLink init')

// Add new section
let sectionInstance = null
let props = null
const View = Vue.extend(NewLink)

window.addEventListener('DOMContentLoaded', function () {
	if (OCA.Sharing && OCA.Sharing.ShareTabSections) {
		OCA.Sharing.ShareTabSections.registerSection((el, fileInfo) => {
			if (typeof fileInfo !== 'undefined' && typeof el !== 'undefined') {
				// if instance exists, just update props
				if (
					sectionInstance &&
					window.document.contains(sectionInstance.$el) &&
					props
				) {
					props.fileInfo = fileInfo
				} else {
					// create new instance
					if (sectionInstance) {
						// if sectionInstance.$el doesn't exist anymore (after changing folder for example)
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
