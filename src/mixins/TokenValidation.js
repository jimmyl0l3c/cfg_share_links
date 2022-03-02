import SettingsMixin from './SettingsMixin'

export default {
	mixins: [
		SettingsMixin,
	],

	data() {
		return {
			minLength: 3,
		}
	},

	methods: {
		async fetchTokenConfig() {
			this.minLength = await this.getMinTokenLengthInt()
		},
		isTokenValidString(token) {
			switch (this.tokenValidityCheck(token)) {
			case 1:
				return t('cfg_share_links', 'Token is not long enough')
			case 2:
				return t('cfg_share_links', 'Token contains invalid characters')
			case 3:
				return ''
			case 0:
				return ''
			default:
				return ''
			}
		},
		isTokenValid(token) {
			return this.tokenValidityCheck(token) === 0
		},
		tokenValidityCheck(token) {
			if (!token || token.length === 0) {
				return 3
			}

			if (token.length > 0 && token.length < this.minLength) {
				return 1
			}

			const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-+'

			for (const c of token) {
				if (!characters.includes(c)) {
					return 2
				}
			}

			return 0
		},
	},
}
