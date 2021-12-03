<template>
	<div id="content" class="app-cfgsharelinks">
		<AppNavigation>
			<AppNavigationNew v-if="!loading"
				:text="t('cfgsharelinks', 'Test New')"
				:disabled="false"
				button-id="new-cfgsharelinks-button"
				button-class="icon-add"
				@click="testNew" />
			<AppNavigationNew v-if="!loading"
				:text="t('cfgsharelinks', 'Test Update')"
				:disabled="false"
				button-id="new-cfgsharelinks-button"
				button-class="icon-add"
				@click="testUpdate" />
		</AppNavigation>
		<AppContent>
			<div v-if="currentNote">
				<input ref="title"
					v-model="currentNote.title"
					type="text"
					:disabled="updating">
				<textarea ref="content" v-model="currentNote.content" :disabled="updating" />
				<input type="button"
					class="primary"
					:value="t('cfgsharelinks', 'Save')"
					:disabled="updating || !savePossible"
					@click="saveNote">
			</div>
			<div v-else-if="response" style="height: 80%;display: flex;align-items: center;justify-content: center;">
				<div v-html="filteredResponse(err != null)">
				</div>
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{ t('cfgsharelinks', 'Press any test button') }}</h2>
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
import { showError, showSuccess } from '@nextcloud/dialogs'
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
			err: null
		}
	},
	computed: {
		/**
		 * Return the currently selected note object
		 * @returns {Object|null}
		 */
		currentNote() {
			if (this.currentNoteId === null) {
				return null
			}
			return this.notes.find((note) => note.id === this.currentNoteId)
		},

		/**
		 * Returns true if a note is selected and its title is not empty
		 * @returns {Boolean}
		 */
		savePossible() {
			return this.currentNote && this.currentNote.title !== ''
		},
	},
	/**
	 * Fetch list of notes when the component is loaded
	 */
	async mounted() {
		try {
			const response = await axios.get(generateUrl('/apps/cfgsharelinks/notes'))
			this.notes = response.data
		} catch (e) {
			console.error(e)
			showError(t('cfgsharelinks', 'Could not fetch notes'))
		}
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
		/**
		 * Create a new note and focus the note content field automatically
		 * @param {Object} note Note object
		 */
		openNote(note) {
			if (this.updating) {
				return
			}
			this.currentNoteId = note.id
			this.$nextTick(() => {
				this.$refs.content.focus()
			})
		},
		/**
		 * Action tiggered when clicking the save button
		 * create a new note or save
		 */
		saveNote() {
			if (this.currentNoteId === -1) {
				this.createNote(this.currentNote)
			} else {
				this.updateNote(this.currentNote)
			}
		},
		/**
		 * Create a new note and focus the note content field automatically
		 * The note is not yet saved, therefore an id of -1 is used until it
		 * has been persisted in the backend
		 */
		newNote() {
			if (this.currentNoteId !== -1) {
				this.currentNoteId = -1
				this.notes.push({
					id: -1,
					title: '',
					content: '',
				})
				this.$nextTick(() => {
					this.$refs.title.focus()
				})
			}
		},
		async testNew() {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/new'), {
					path: '/Readme.md',
					shareType: 3,
					tokenCandidate: 'test_token',
				})
				console.info(response)
				this.response = response
				this.err = null
			} catch (e) {
				console.error(e)
				this.response = e.response
				this.err = e
				showError(t('cfgsharelinks', 'Test new error'))
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
				showError(t('cfgsharelinks', 'Test update error'))
			}
			this.updating = false
		},
		/**
		 * Abort creating a new note
		 */
		cancelNewNote() {
			this.notes.splice(this.notes.findIndex((note) => note.id === -1), 1)
			this.currentNoteId = null
		},
		/**
		 * Create a new note by sending the information to the server
		 * @param {Object} note Note object
		 */
		async createNote(note) {
			this.updating = true
			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/notes'), note)
				const index = this.notes.findIndex((match) => match.id === this.currentNoteId)
				this.$set(this.notes, index, response.data)
				this.currentNoteId = response.data.id
			} catch (e) {
				console.error(e)
				showError(t('cfgsharelinks', 'Could not create the note'))
			}
			this.updating = false
		},
		/**
		 * Update an existing note on the server
		 * @param {Object} note Note object
		 */
		async updateNote(note) {
			this.updating = true
			try {
				await axios.put(generateUrl(`/apps/cfgsharelinks/notes/${note.id}`), note)
			} catch (e) {
				console.error(e)
				showError(t('cfgsharelinks', 'Could not update the note'))
			}
			this.updating = false
		},
		/**
		 * Delete a note, remove it from the frontend and show a hint
		 * @param {Object} note Note object
		 */
		async deleteNote(note) {
			try {
				await axios.delete(generateUrl(`/apps/cfgsharelinks/notes/${note.id}`))
				this.notes.splice(this.notes.indexOf(note), 1)
				if (this.currentNoteId === note.id) {
					this.currentNoteId = null
				}
				showSuccess(t('cfgsharelinks', 'Note deleted'))
			} catch (e) {
				console.error(e)
				showError(t('cfgsharelinks', 'Could not delete the note'))
			}
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
