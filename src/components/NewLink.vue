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
				<ActionText v-if="passwordPending" icon="icon-password">
					{{ t('cfg_share_links', 'Password protection enforced') }}
				</ActionText>
				<ActionInput v-if="passwordPending"
					:disabled="updating"
					:value.sync="password"
					@submit="createCustomLink">
					{{ t('cfg_share_links', 'Enter a password') }}
				</ActionInput>
				<ActionButton :icon="buttonIcon" @click="createCustomLink">
					{{ copiedTooltip }}
				</ActionButton>
			</template>
		</ListItem>
	</ul>
</template>

<script>
import ActionText from '@nextcloud/vue/dist/Components/ActionText.js'
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput.js'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton.js'
import Avatar from '@nextcloud/vue/dist/Components/Avatar.js'
import ListItem from '@nextcloud/vue/dist/Components/ListItem.js'

import { generateUrl } from '@nextcloud/router'
import '@nextcloud/dialogs/styles/toast.scss'
import { showError } from '@nextcloud/dialogs'
import TokenValidation from '../mixins/TokenValidation.js'
import RequestMixin from '../mixins/RequestMixin.js'

export default {
	name: 'NewLink',

	components: {
		ActionText,
		ActionInput,
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
			requestPending: false,
			tokenCandidate: null,
			focused: false,
			copied: false,
			passwordPending: false,
			password: null,
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
		isMenuOpened() {
			return this.$refs.newItem.$refs.actions.opened
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
			const enforcePassword = OC.appConfig.core.enforcePasswordForPublicLink
			const token = this.tokenCandidate
			const password = this.password

			if (!this.isTokenValid(token)) {
				const message = this.isTokenValidString(token)
				showError(t('cfg_share_links', message != null && message.length > 1 ? message : t('cfg_share_links', 'Invalid token')))
				this.updating = false
				return
			}

			if (enforcePassword) {
				console.debug('CfgShareLinks: EnforcePassword enabled')

				if (!this.passwordPending || !this.isMenuOpened) {
					// Open menu with password prompt
					this.passwordPending = true
					this.$refs.newItem.$refs.actions.openMenu()

					this.updating = false
					return
				}
			}

			if (this.requestPending) return

			this.requestPending = true
			const response = await this.createLink(this.getFullPath, token, password)
			this.requestPending = false

			if (response && response.ret === 0) {
				this.passwordPending = false

				let resultToken = token
				if (response.data && response.data.token) {
					resultToken = response.data.token
				}
				const shareLink = window.location.protocol + '//' + window.location.host + generateUrl('/s/') + resultToken

				if (!navigator.clipboard) {
					this.fallbackCopyTextToClipboard(shareLink)
				} else {
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

				this.tokenCandidate = ''
				this.focused = false
				this.refreshSidebar(this.fileInfo)
			}

			this.updating = false
		},
		fallbackCopyTextToClipboard(text) {
			const textArea = document.createElement('textarea')
			textArea.value = text

			// Avoid scrolling to bottom
			textArea.style.top = '0'
			textArea.style.left = '0'
			textArea.style.position = 'fixed'

			document.body.appendChild(textArea)
			textArea.focus()
			textArea.select()

			try {
				const successful = document.execCommand('copy')
				const msg = successful ? 'successful' : 'unsuccessful'
				console.debug('CfgShareLinks_Fallback: Copying text command was ' + msg)

				// Notify that link was copied
				this.copied = true
				this.$refs.newItem.$refs.actions.$el.focus()
				// Reset tooltip after 4s
				setTimeout(() => {
					this.copied = false
				}, 4000)
			} catch (err) {
				console.error('CfgShareLinks_Fallback: Oops, unable to copy', err)
			}

			document.body.removeChild(textArea)
		},
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
