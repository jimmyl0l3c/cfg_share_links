<template>
	<ul v-if="canShare">
		<CustomListItem
			ref="newItem"
			:title="t('cfg_share_links', 'Custom public link')"
			:bold="false"
			:force-display-actions="true">
			<template #icon>
				<NcAvatar :is-no-user="true" icon-class="avatardiv">
					<template #icon>
						<LinkVariantIcon fill-color="white" :size="18" />
					</template>
				</NcAvatar>
			</template>
			<template #subtitle>
				<NcTextField
					:value.sync="tokenCandidate"
					:disabled="updating"
					:label="t('cfg_share_links', 'Enter custom token')"
					:helper-text="inputInvalidMessage"
					:error="isInputInvalid"
					:success="copied"
					@keyup.enter="createCustomLink" />
			</template>
			<template #actions>
				<NcActionText v-if="passwordPending" icon="icon-password">
					<template #icon>
						<LockIcon />
					</template>
					{{ passwordPrompt }}
				</NcActionText>
				<NcActionInput
					v-if="passwordPending"
					:disabled="updating"
					:value.sync="password"
					@submit="createCustomLink">
					{{ t('cfg_share_links', 'Enter a password') }}
				</NcActionInput>
				<NcActionButton @click="createCustomLink">
					<template #icon>
						<CheckIcon v-if="copied" />
						<PlusIcon v-else />
					</template>
					{{ copiedTooltip }}
				</NcActionButton>
			</template>
		</CustomListItem>
	</ul>
</template>

<script>
import { showError } from '@nextcloud/dialogs'
import { t } from '@nextcloud/l10n'

import { generateUrl } from '@nextcloud/router'
import '@nextcloud/dialogs/style.css'
import {
	NcActionButton,
	NcActionInput,
	NcActionText,
	NcAvatar,
	NcTextField,
} from '@nextcloud/vue'
import CheckIcon from 'vue-material-design-icons/Check.vue'

import LinkVariantIcon from 'vue-material-design-icons/LinkVariant.vue'
import LockIcon from 'vue-material-design-icons/Lock.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'
import RequestMixin from '../mixins/RequestMixin.ts'
import TokenValidation from '../mixins/TokenValidation.ts'

import CustomListItem from './CustomListItem.vue'

export default {
	name: 'NewLink',

	components: {
		NcActionText,
		NcActionInput,
		NcActionButton,
		NcAvatar,
		CustomListItem,
		NcTextField,
		LinkVariantIcon,
		LockIcon,
		PlusIcon,
		CheckIcon,
	},

	mixins: [TokenValidation, RequestMixin],

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
			tokenCandidate: '',
			copied: false,
			passwordPending: false,
			enforcePassword: false,
			password: '',
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
		isInputInvalid() {
			return (
				this.tokenCandidate != null
				&& this.tokenCandidate.length > 0
				&& !this.isTokenValid(this.tokenCandidate)
			)
		},
		inputInvalidMessage() {
			return this.isTokenValidString(this.tokenCandidate)
		},
		canShare() {
			return !!(this.fileInfo.permissions & OC.PERMISSION_SHARE)
		},
		copiedTooltip() {
			return this.copied
				? t('cfg_share_links', 'Link copied')
				: t('cfg_share_links', 'Create link')
			// return { content: message, show: this.copied, placement: 'bottom' }
		},
		passwordPrompt() {
			return this.enforcePassword
				? t('cfg_share_links', 'Password protection enforced')
				: t('cfg_share_links', 'Enter password (optional)')
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
		t,
		async createCustomLink() {
			this.updating = true
			const alwaysAskForPassword = OC.appConfig.core.enableLinkPasswordByDefault
			this.enforcePassword = OC.appConfig.core.enforcePasswordForPublicLink
			const token = this.tokenCandidate
			const password = this.password

			if (!this.isTokenValid(token)) {
				const message = this.isTokenValidString(token)
				showError(
					t(
						'cfg_share_links',
						message != null && message.length > 1
							? message
							: t('cfg_share_links', 'Invalid token'),
					),
				)
				this.updating = false
				return
			}

			if (this.enforcePassword || alwaysAskForPassword) {
				console.debug(
					`CfgShareLinks: EnforcePassword=${this.enforcePassword}, AlwaysAskForPassword=${alwaysAskForPassword}`,
				)

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
			this.password = ''

			if (response && response.ret === 0) {
				this.passwordPending = false

				let resultToken = token
				if (response.data && response.data.token) {
					resultToken = response.data.token
				}
				const shareLink
					= window.location.protocol
					+ '//'
					+ window.location.host
					+ generateUrl('/s/')
					+ resultToken

				if (!navigator.clipboard) {
					this.fallbackCopyTextToClipboard(shareLink)
				} else {
					await navigator.clipboard
						.writeText(shareLink)
						.then(() => {
							console.debug('CfgShareLinks: Link copied')
							// Notify that link was copied
							this.copied = true
							this.$refs.newItem.$refs.actions.$el.focus()
							// Reset tooltip after 4s
							setTimeout(() => {
								this.copied = false
							}, 4000)
						})
						.catch((reason) => {
							console.debug('CfgShareLinks: Could not copy')
							console.debug(reason)
						})
				}

				this.tokenCandidate = ''
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
.avatardiv {
	background-color: #c40c0c !important;
}
</style>
