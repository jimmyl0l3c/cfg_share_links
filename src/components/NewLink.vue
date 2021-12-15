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
		<ListItemIcon
			:is-no-user="false"
			display-name="Share"
			icon-class="avatar-link-icon icon-public-white"
			:title="getPath"
			:subtitle="getFullPath">
			<Actions>
				<ActionButton icon="icon-add" @click="showModal">
					Add
				</ActionButton>
			</Actions>
		</ListItemIcon>
		<Modal v-if="modal" size="small" @close="closeModal">
			<div class="modal-content">
				<div>
					<b>Create custom link</b>
					<input v-model="tokenCandidate" class="token-input" placeholder="Enter custom token">
				</div>
				<button @click="closeModal">
					Add
				</button>
			</div>
		</Modal>
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
// import Button from '@nextcloud/vue/dist/Components/Button' TODO: use if available (not in 4.3.0)
import ListItem from '@nextcloud/vue/dist/Components/ListItem'
import ListItemIcon from '@nextcloud/vue/dist/Components/ListItemIcon'
import Modal from '@nextcloud/vue/dist/Components/Modal'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import TokenValidation from '../mixins/TokenValidation'

export default {
	name: 'NewLink',

	components: {
		Actions,
		ActionButton,
		ActionInput,
		Avatar,
		ListItem,
		ListItemIcon,
		Modal,
	},

	mixins: [
		TokenValidation,
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
		showModal() {
			this.modal = true
		},
		closeModal() {
			this.modal = false
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

			const data = {
				path: this.getFullPath,
				shareType: 3,
				tokenCandidate: token,
			}

			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/new'), data)
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
