<template>
	<NcActionInput
		:value.sync="tokenCandidate"
		:disabled="updating"
		type="text"
		@submit="onSubmit">
		<template #icon>
			<LinkVariantIcon size="20" />
		</template>
		{{ t('cfg_share_links', 'Enter custom token') }}
	</NcActionInput>
</template>

<script>
import { showError } from '@nextcloud/dialogs'
import { t } from '@nextcloud/l10n'
import { NcActionInput } from '@nextcloud/vue'
import LinkVariantIcon from 'vue-material-design-icons/LinkVariant.vue'

import '@nextcloud/dialogs/style.css'
import RequestMixin from '../mixins/RequestMixin.js'
import TokenValidation from '../mixins/TokenValidation.js'

export default {
	id: 'rename-link',
	name: 'RenameLink',
	components: {
		NcActionInput,
		LinkVariantIcon,
	},

	mixins: [TokenValidation, RequestMixin],

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
		t,
		async onSubmit(_) {
			this.updating = true
			const token = this.tokenCandidate

			if (!this.isTokenValid(token)) {
				const message = this.isTokenValidString(token)
				showError(
					t(
						'cfg_share_links',
						message != null && message.length > 1 ? message : 'Invalid token',
					),
				)
				this.updating = false
				return
			}

			await this.renameLink(this.currentToken, token)

			this.refreshSidebar(this.fileInfo)

			this.updating = false
		},
	},
}
</script>
