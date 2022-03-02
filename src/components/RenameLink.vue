<template>
	<ActionInput :value="tokenCandidate"
		type="text"
		icon="icon-public"
		@submit="onSubmit"
		@update:value="onTokenChange">
		{{ t('cfg_share_links', 'Enter custom token') }}
	</ActionInput>
</template>

<script>
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
import '@nextcloud/dialogs/styles/toast.scss'
import { showError } from '@nextcloud/dialogs'
import TokenValidation from '../mixins/TokenValidation'
import RequestMixin from '../mixins/RequestMixin'

export default {
	id: 'rename-link',
	name: 'RenameLink',
	components: {
		ActionInput,
	},

	mixins: [
		TokenValidation,
		RequestMixin,
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
		await this.fetchTokenConfig()
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
				showError(t('cfg_share_links', message != null && message.length > 1 ? message : 'Invalid token'))
				this.updating = false
				return
			}

			await this.renameLink(this.shareId, this.getFullPath, this.currentToken, token)

			this.refreshSidebar(this.fileInfo)

			this.updating = false
		},
	},
}
</script>
