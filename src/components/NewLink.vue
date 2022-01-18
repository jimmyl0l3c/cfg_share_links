<template>
	<ul>
		<ListItem
			:title="t('cfgsharelinks', 'Custom public link')"
			:bold="false"
			:display-actions="true"
			class="l-hover show-actions">
			<template #icon>
				<Avatar
					:is-no-user="true"
					display-name="Share"
					icon-class="avatar-link-icon icon-public-white" />
			</template>
			<template #subtitle>
				<input
					v-model="tokenCandidate"
					:disabled="updating"
					class="token-input"
					:placeholder="t('cfgsharelinks', 'Enter custom token')"
					@focus="onFocus">
				<span v-if="isInputValid && focused" class="form-error"> {{ isInputValid }} </span>
			</template>
			<template #actions>
				<ActionButton icon="icon-add" @click="createCustomLink">
					{{ t('cfgsharelinks', 'Add') }}
				</ActionButton>
			</template>
		</ListItem>
	</ul>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import Avatar from '@nextcloud/vue/dist/Components/Avatar'
import ListItem from '@nextcloud/vue/dist/Components/ListItem'

import '@nextcloud/dialogs/styles/toast.scss'
import { showError } from '@nextcloud/dialogs'
import TokenValidation from '../mixins/TokenValidation'
import RequestMixin from '../mixins/RequestMixin'

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
				showError(t('cfgsharelinks', message != null && message.length > 1 ? message : t('cfgsharelinks', 'Invalid token')))
				this.updating = false
				return
			}

			await this.createLink(this.getFullPath, token)

			this.focused = false
			this.tokenCandidate = ''
			this.refreshSidebar(this.fileInfo)

			this.updating = false
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
	background-color: #c40c0c;
}
.l-hover ::v-deep a:hover {
	background-color: transparent;
}
.show-actions ::v-deep .list-item-content__actions {
	display: block !important;
}
</style>
