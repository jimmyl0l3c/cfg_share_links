<template>
	<ul>
		<ListItemIcon
			:is-no-user="false"
			display-name="Share"
			icon-class="avatar-link-icon icon-public-white"
			:title="t('cfgsharelinks', 'Custom public link')"
			:subtitle="t('cfgsharelinks', 'Create link with custom share token')">
			<Actions :force-menu="true">
				<ActionInput icon="icon-edit">
					Enter token
				</ActionInput>
			</Actions>
		</ListItemIcon>
		<ListItem
			:title="t('cfgsharelinks', 'Custom public link')"
			:bold="false">
			<template #icon>
				<Avatar :is-no-user="true"
					display-name="Share"
					icon-class="avatar-link-icon icon-public-white" />
			</template>
			<template #subtitle>
				<input v-model="tokenCandidate"
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
import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
import Avatar from '@nextcloud/vue/dist/Components/Avatar'
import ListItem from '@nextcloud/vue/dist/Components/ListItem'
import ListItemIcon from '@nextcloud/vue/dist/Components/ListItemIcon'

import '@nextcloud/dialogs/styles/toast.scss'
import { showError } from '@nextcloud/dialogs'
import TokenValidation from '../mixins/TokenValidation'
import RequestMixin from '../mixins/RequestMixin'

export default {
	name: 'NewLink',

	components: {
		Actions,
		ActionButton,
		ActionInput,
		Avatar,
		ListItem,
		ListItemIcon,
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
			modal: false,
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
		getPath() {
			return this.fileInfo ? '/'.concat(this.fileInfo.name) : 'None'
		},
		isInputValid() {
			return this.isTokenValidString(this.tokenCandidate)
		},
	},

	async mounted() {
		// could load something here if needed
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
				showError(t('cfgsharelinks', message != null && message.length > 1 ? message : 'Invalid token'))
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
.modal-content {
	margin: 50px;
	text-align: center;
}
.token-input {
	width: 80%;
}
::v-deep .avatar-link-icon {
	background-color: #c40c0c;
}
</style>
