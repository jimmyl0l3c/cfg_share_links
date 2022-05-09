<template>
	<ul v-if="canShare">
		<ListItem ref="newItem"
			:title="t('cfg_share_links', 'Custom public link')"
			:bold="false"
			:force-display-actions="true"
			class="l-hover">
			<template #icon>
				<Avatar :is-no-user="true"
					display-name="Share"
					icon-class="avatar-link-icon icon-public-white" />
			</template>
			<template #subtitle>
				<input v-model="tokenCandidate"
					:disabled="updating"
					class="token-input"
					:placeholder="t('cfg_share_links', 'Enter custom token')"
					@focus="onFocus"
					@keyup.enter="createCustomLink">
				<span v-if="isInputValid && focused" class="form-error"> {{ isInputValid }} </span>
			</template>
			<template #actions>
				<ActionButton :icon="buttonIcon" @click="createCustomLink">
					{{ copiedTooltip }}
				</ActionButton>
			</template>
		</ListItem>
	</ul>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import Avatar from '@nextcloud/vue/dist/Components/Avatar'
import ListItem from '@nextcloud/vue/dist/Components/ListItem'

import { generateUrl } from '@nextcloud/router'
import '@nextcloud/dialogs/styles/toast.scss'
import { showError } from '@nextcloud/dialogs'
import TokenValidation from '../mixins/TokenValidation.js'
import RequestMixin from '../mixins/RequestMixin.js'

export default {
	name: 'NewLink',

	components: {
		ActionButton,
		Avatar,
		ListItem,
	},

	mixins: [
		TokenValidation,
		RequestMixin,
	],

	props: {
		fileInfo: {
			type: Object,
			default: () => {},
			required: true,
		},
	},

	data() {
		return {
			updating: false,
			loading: true,
			tokenCandidate: null,
			focused: false,
			copied: false,
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
		isInputValid() {
			return this.isTokenValidString(this.tokenCandidate)
		},
		canShare() {
			return !!(this.fileInfo.permissions & OC.PERMISSION_SHARE)
		},
		buttonIcon() {
			return this.copied ? 'icon-checkmark' : 'icon-add'
		},
		copiedTooltip() {
			return this.copied ? t('cfg_share_links', 'Link copied') : t('cfg_share_links', 'Create link')
			// return { content: message, show: this.copied, placement: 'bottom' }
		},
	},

	async mounted() {
		// could load something here if needed
		await this.fetchTokenConfig()
		this.loading = false
	},

	methods: {
		onFocus() {
			this.focused = true
		},
		async createCustomLink() {
			this.updating = true
			const token = this.tokenCandidate
			if (!this.isTokenValid(token)) {
				const message = this.isTokenValidString(token)
				showError(t('cfg_share_links', message != null && message.length > 1 ? message : t('cfg_share_links', 'Invalid token')))
				this.updating = false
				return
			}

			const response = await this.createLink(this.getFullPath, token)

			if (response && response.ret === 0) {
				let resultToken = token
				if (response.data && response.data.token) {
					resultToken = response.data.token
				}
				const shareLink = window.location.protocol + '//' + window.location.host + generateUrl('/s/') + resultToken

				await navigator.clipboard.writeText(shareLink).then(() => {
					console.debug('CfgShareLinks: Link copied')
					// Notify that link was copied
					this.copied = true
					this.$refs.newItem.$refs.actions.$el.focus()
					// Reset tooltip after 4s
					setTimeout(() => {
						this.copied = false
					}, 4000)
				}).catch(reason => {
					console.debug('CfgShareLinks: Could not copy')
					console.debug(reason)
				})
			}

			this.focused = false
			this.tokenCandidate = ''
			this.refreshSidebar(this.fileInfo)

			this.updating = false
		},
		/*
		  async testRename() {
		 	this.updating = true
		 	 await this.renameLink('21', '/Reshare.md', 'reshare-test1', 'reshare-bad')
		 	 this.updating = false
		  },
		 async revertRename() {
		 	this.updating = true
		 	await this.renameLink('21', '/Reshare.md', 'reshare-bad', 'reshare-test1')
		 	this.updating = false
		 },
		*/
	},
}
</script>

<style lang="scss" scoped>
.form-error {
	color: #c40c0c;
	display: block;
}
.token-input {
	width: 80%;
}
::v-deep .avatar-link-icon {
	background-color: #c40c0c !important;
}
.l-hover ::v-deep a:hover {
	background-color: transparent;
}
</style>
