export default {
	methods: {
		isTokenValidString(token) {
			switch (this.tokenValidityCheck(token)) {
			case 1:
				return t('cfgsharelinks', 'Token is not long enough')
			case 2:
				return t('cfgsharelinks', 'Token contains invalid characters')
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
			if (!token || token.length <= 1) {
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
