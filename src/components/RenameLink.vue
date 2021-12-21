<template>
	<ActionInput
		:value="tokenCandidate"
		type="text"
		icon="icon-public"
		@submit="onSubmit"
		@update:value="onTokenChange">
		{{ t('cfgsharelinks', 'Enter custom token') }}
	</ActionInput>
</template>

<script>
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
import '@nextcloud/dialogs/styles/toast.scss'
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
		getFullPath() {
			if (this.fileInfo) {
				if (this.fileInfo.path.endsWith('/')) {
					return this.fileInfo.path.concat(this.fileInfo.name)
				} else {
					return this.fileInfo.path.concat('/', this.fileInfo.name)
				}
			} else {
				return 'None'
			}
		},
		currentToken() {
			return this.share && this.share.token ? this.share.token : 'None'
		},
		shareId() {
			return this.share && this.share.id ? this.share.id : 'None'
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
		onTokenChange(token) {
			this.tokenCandidate = token
		},
		async onSubmit(_) {
			this.updating = true
			const token = this.tokenCandidate

			if (!this.isTokenValid(token)) {
				const message = this.isTokenValidString(token)
				showError(t('cfgsharelinks', message != null && message.length > 1 ? message : 'Invalid token'))
				this.updating = false
				return
			}

			const data = {
				id: this.shareId,
				path: this.getFullPath,
				currentToken: this.currentToken,
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
