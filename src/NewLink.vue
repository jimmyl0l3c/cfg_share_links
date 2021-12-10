<template>
	<ul>
		<ListItem
			:title="'Custom token link'"
			:bold="false">
			<template #icon>
				<Avatar user="admin" display-name="Admin" />
			</template>
			<template #subtitle>
				Make a custom token link
			</template>
			<template #actions>
				<ActionInput icon="icon-edit">
					Specify custom token
				</ActionInput>
				<ActionButton icon="icon-add">
					Add
				</ActionButton>
			</template>
		</ListItem>
		<ListItemIcon
			:is-no-user="false"
			display-name="Share"
			icon="icon-public-white"
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
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'
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
		ActionInput,
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
			response: null,
			err: null,
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

		async createCustomLink() {
			this.updating = true
			const filePath = this.fileInfo.path.concat(this.fileInfo.name)
			const data = {
				path: filePath,
				shareType: 3,
				tokenCandidate: 'new_token',
			}
			// eslint-disable-next-line no-console
			console.log(data)
			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/new'), data)
				console.info(response)
				this.response = response
				this.err = null
				showSuccess(t('cfgsharelinks', 'New success'))
			} catch (e) {
				console.error(e)
				this.response = e.response
				this.err = e
				showError(t('cfgsharelinks', 'New error'))
			}
			this.updating = false
		},
	}
	,
}
</script>

<style scoped>
#subtitle {
	color: gray;
}
</style>
