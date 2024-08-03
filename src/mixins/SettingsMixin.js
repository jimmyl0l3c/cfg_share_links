import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'

export default {
	data() {
		return {
			settings: null,
		}
	},

	methods: {
		async fetchSettings() {
			try {
				const response = await axios.get(
					generateUrl('/apps/cfg_share_links/settings'),
				)
				if (response.data) {
					this.settings = response.data
				}
			} catch (e) {
				console.error(e.response)
			}
		},
		async getSettings() {
			if (this.settings) {
				return this.settings
			} else {
				await this.fetchSettings()
				return this.settings
			}
		},
		async getLabelMode() {
			const settings = await this.getSettings()
			return settings
				&& settings.defaultLabelMode
				&& settings.defaultLabelMode >= 0
				&& settings.defaultLabelMode <= 2
				? settings.defaultLabelMode
				: 0
		},
		async getCustomLabel() {
			const settings = await this.getSettings()
			return settings && settings.defaultLabel
				? settings.defaultLabel
				: 'Custom link'
		},
		async getMinTokenLength() {
			const settings = await this.getSettings()
			return settings && settings.minTokenLength
				? settings.minTokenLength.toString()
				: '3'
		},
		async getMinTokenLengthInt() {
			const length = parseInt(await this.getMinTokenLength())
			return isNaN(length) ? 3 : length
		},
		async getDeleteRemovedShareConflicts() {
			const settings = await this.getSettings()
			return !!(settings && settings.deleteRemovedShareConflicts)
		},
	},
}
