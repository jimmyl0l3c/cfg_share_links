<template>
	<div id="content" class="app-cfgsharelinks">
		<AppNavigation>
			<AppNavigationNew v-if="!loading"
				:text="'Test New'"
				:disabled="false"
				button-id="new-cfgsharelinks-button"
				button-class="icon-add"
				@click="testNew" />
			<AppNavigationNew v-if="!loading"
				:text="'Test Update'"
				:disabled="false"
				button-id="new-cfgsharelinks-button"
				button-class="icon-add"
				@click="testUpdate" />
		</AppNavigation>
		<AppContent>
			<div id="emptycontent">
				<div class="icon-public" />
				<h2>{{ 'Press any test button' }}</h2>
			</div>
		</AppContent>
	</div>
</template>

<script>
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'

import '@nextcloud/dialogs/styles/toast.scss'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	name: 'App',
	components: {
		AppContent,
		AppNavigation,
		AppNavigationNew,
	},
	data() {
		return {
			notes: [],
			currentNoteId: null,
			updating: false,
			loading: true,
			response: null,
			err: null,
		}
	},
	computed: {
		/**
		 * Return the currently selected note object
		 *
		 * @return {object | null}
		 */
		currentNote() {
			if (this.currentNoteId === null) {
				return null
			}
			return this.notes.find((note) => note.id === this.currentNoteId)
		},

		/**
		 * Returns true if a note is selected and its title is not empty
		 *
		 * @return {boolean}
		 */
		savePossible() {
			return this.currentNote && this.currentNote.title !== ''
		},
	},
	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		this.loading = false
	},

	methods: {
		filteredResponse(err) {
			let index
			if (err) {
				index = 'class="error error-wide"'
			} else {
				index = '</head>'
			}
			try {
				const start = this.response.data.indexOf(index)
				if (start <= 0) {
					return this.response.data
				}
				return this.response.data.slice(start + index.length + err)
			} catch (_) {
				return this.response
			}

		},
		async testNew() {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/new'), {
					path: '/Readme.md',
					shareType: 3,
					tokenCandidate: 'new_token',
				})
				console.info(response)
				this.response = response
				this.err = null
			} catch (e) {
				console.error(e)
				this.response = e.response
				this.err = e
				showError('Test new error')
			}
			this.updating = false
		},
		async testUpdate() {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/update'), {
					id: 'ocinternal:2',
					tokenCandidate: 'test_token',
				})
				console.info(response)
				this.response = response
				this.err = null
			} catch (e) {
				console.error(e)
				this.response = e.response
				this.err = e
				showError('Test update error')
			}
			this.updating = false
		},
	},
}
</script>
<style scoped>
	#app-content > div {
		width: 100%;
		height: 100%;
		padding: 20px;
		display: flex;
		flex-direction: column;
		flex-grow: 1;
	}

	input[type='text'] {
		width: 100%;
	}

	textarea {
		flex-grow: 1;
		width: 100%;
	}
</style>
