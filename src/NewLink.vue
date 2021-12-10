<template>
	<ul>
		<ListItem
			:title="'Custom token link'"
			:bold="false">
			<template #icon>
				<Avatar :is-no-user="true" display-name="Share" icon-class="avatar-link-icon icon-public-white" />
			</template>
			<template #subtitle>
				<input v-model="tokenCandidate" class="token-input" placeholder="Enter custom token">
			</template>
			<template #actions>
				<ActionButton icon="icon-add" @click="createCustomLink">
					Add
				</ActionButton>
			</template>
		</ListItem>
		<ListItemIcon
			:is-no-user="false"
			display-name="Share"
			icon-class="avatar-link-icon icon-public-white"
			:title="getPath"
			:subtitle="getFullPath">
			<Actions>
				<ActionButton icon="icon-add" @click="debugFileInfo">
					Add
				</ActionButton>
			</Actions>
		</ListItemIcon>
	</ul>
</template>

<script>
import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import Avatar from '@nextcloud/vue/dist/Components/Avatar'
import ListItem from '@nextcloud/vue/dist/Components/ListItem'
import ListItemIcon from '@nextcloud/vue/dist/Components/ListItemIcon'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError, showSuccess } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	name: 'NewLink',

	components: {
		Actions,
		ActionButton,
		Avatar,
		ListItem,
		ListItemIcon,
	},

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
	},

	async mounted() {
		// could load something here if needed
		this.loading = false
	},

	methods: {
		debugFileInfo() {
			const data = {
				path: this.getFullPath,
				shareType: 3,
				tokenCandidate: 'new_token',
			}
			console.info(data)
		},

		isTokenValid(token) {
			if (!token || token.length <= 1) {
				return false
			}

			const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-+'

			for (const c of token) {
				if (!characters.includes(c)) {
					return false
				}
			}

			return true
		},

		async createCustomLink() {
			this.updating = true
			const token = this.tokenCandidate
			if (!this.isTokenValid(token)) {
				showError(t('cfgsharelinks', 'Invalid token'))
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
				showSuccess(t('cfgsharelinks', 'New success'))
			} catch (e) {
				console.error(e)
				showError(t('cfgsharelinks', 'New error'))
			}

			this.updating = false
		},
	},
}
</script>

<style lang="scss" scoped>
.token-input {
	width: 80%;
}
::v-deep .avatar-link-icon {
	background-color: #c40c0c;
}
</style>
