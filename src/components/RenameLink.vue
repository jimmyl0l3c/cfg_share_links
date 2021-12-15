<template>
	<ActionInput
		:value="tokenCandidate"
		icon="icon-public"
		@submit="onSubmit">
		{{ t('cfgsharelinks', 'Enter custom token') }}
	</ActionInput>
</template>

<script>
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import TokenValidation from '../mixins/TokenValidation'

export default {
	id: 'rename-link',
	name: 'RenameLink',
	components: {
		ActionInput,
	},

	mixins: [
		TokenValidation,
	],

	props: {
		share: {
			type: Object,
			default: () => {},
			required: true,
		},
		fileInfo: {
			type: Object,
			default: () => {},
			required: true,
		},
	},

	data() {
		return {
			updating: false,
			tokenCandidate: null,
		}
	},

	computed: {
		currentToken() {
			return this.share && this.share.token ? this.share.token : t('cfgsharelinks', 'Enter custom token')
		},
	},

	async mounted() {
		// TODO: load db to filter here
		if (this.share && this.share.token) {
			this.tokenCandidate = this.share.token
		}
		this.loading = false
	},

	methods: {
		async onSubmit(_) {
			this.updating = true
			const token = this.tokenCandidate
			if (!this.isTokenValid(token)) {
				const message = this.isTokenValidString(token)
				showError(t('cfgsharelinks', message != null && message.length > 1 ? message : 'Invalid token'))
				this.updating = false
				return
			}

			const data = { // TODO: change (send id, current token, new token, maybe also type and path)
				path: this.getFullPath,
				shareType: 3,
				tokenCandidate: token,
			}

			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/update'), data)
				console.info(response)
				showSuccess(t('cfgsharelinks', 'Custom public link created'))
			} catch (e) {
				if (e.response.data && e.response.data.message) {
					showError(t('cfgsharelinks', e.response.data.message))
				} else {
					showError(t('cfgsharelinks', 'Error occurred while creating public link'))
					console.error(e.response)
				}
			}

			this.updating = false
		},
	},
}
</script>
