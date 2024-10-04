import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { LabelMode } from '../enums/LabelMode'
import type { AppSettings } from '../interfaces/responses/AppSettings'

export default {
	data() {
		return {
			settings: null,
		}
	},

	methods: {
		async fetchSettings(): Promise<void> {
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
		async getSettings(): Promise<AppSettings | null> {
			if (this.settings) {
				return this.settings
			} else {
				await this.fetchSettings()
				return this.settings
			}
		},
		async getLabelMode(): Promise<number> {
			const settings = await this.getSettings()
			return settings
				&& settings.defaultLabelMode
				&& settings.defaultLabelMode >= 0
				&& settings.defaultLabelMode <= 2
				? settings.defaultLabelMode
				: LabelMode.NoLabel
		},
		async getCustomLabel(): Promise<string> {
			const settings = await this.getSettings()
			return settings && settings.defaultLabel
				? settings.defaultLabel
				: 'Custom link'
		},
		async getMinTokenLength(): Promise<string> {
			const settings = await this.getSettings()
			return settings && settings.minTokenLength
				? settings.minTokenLength.toString()
				: '3'
		},
		async getMinTokenLengthInt(): Promise<number> {
			const length = parseInt(await this.getMinTokenLength())
			return isNaN(length) ? 3 : length
		},
		async getDeleteRemovedShareConflicts(): Promise<boolean> {
			const settings = await this.getSettings()
			return !!(settings && settings.deleteRemovedShareConflicts)
		},
	},
}
