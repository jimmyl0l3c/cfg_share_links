<template>
	<div id="cfgshare-admin-settings">
		<SettingsSection
			:title="t('cfgsharelinks', 'Default share label')"
			:description="t('cfgsharelinks', 'Define default label for custom links')">
			<div>
				<h3>Default label:</h3>
				<Multiselect
					v-model="labelMode"
					:options="labelOptions"
					track-by="id"
					label="label"
					@update:value="onLabelModeChange" />
			</div>
			<div>
				<h3>Custom label:</h3>
				<SettingsInputText
					id="default-label"
					label=""
					:value="customLabel"
					@update:value="onCustomLabelChange"
					@submit="onLabelSubmit" />
			</div>
		</SettingsSection>
		<SettingsSection
			:title="t('cfgsharelinks', 'Token settings')"
			:description="t('cfgsharelinks', 'Token settings/requirements')">
			<div>
				<h3>Minimal token length:</h3>
				<SettingsInputText
					id="min-len"
					label=""
					:value="minLength"
					@update:value="onMinLengthChange"
					@submit="onMinLengthSubmit" />
			</div>
			<!-- TODO: add more options (available characters) -->
		</SettingsSection>
	</div>
</template>

<script>
import Multiselect from '@nextcloud/vue/dist/Components/Multiselect'
import SettingsSection from '@nextcloud/vue/dist/Components/SettingsSection'
import SettingsInputText from '@nextcloud/vue/dist/Components/SettingsInputText'
import SettingsMixin from '../mixins/SettingsMixin'
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

const labelOptions = [
	{ id: 1, label: t('cfgsharelinks', 'None') },
	{ id: 2, label: t('cfgsharelinks', 'Same as token') },
	{ id: 3, label: t('cfgsharelinks', 'Custom') },
]

export default {
	name: 'AdminSettings',

	components: {
		Multiselect,
		SettingsSection,
		SettingsInputText,
	},

	mixins: [
		SettingsMixin,
	],

	data() {
		return {
			updating: false,
			loading: true,
			labelMode: labelOptions[0],
			labelOptions,
			customLabel: 'Custom link',
			minLength: '3',
		}
	},

	async mounted() {
		this.loading = true
		this.customLabel = await this.getCustomLabel()
		this.minLength = await this.getMinTokenLength()
		this.labelMode = labelOptions[await this.getLabelMode()]
		this.loading = false
	},

	methods: { // TODO: add general visual feedback (saved confirmation, errors, ...)
		async onLabelSubmit() {
			this.loading = true
			// TODO: validity check
			await this.saveSettings('default_label', this.customLabel)
			this.loading = false
		},
		async onMinLengthSubmit() {
			this.loading = true
			const minLength = this.minLength

			if (isNaN(minLength)) {
				showError(t('cfgsharelinks', 'Entered length is not a number'))
				return
			}

			if (parseInt(minLength) < 1) {
				showError(t('cfgsharelinks', 'Minimum length must be at least 1'))
				return
			}

			await this.saveSettings('min_token_length', minLength)
			this.loading = false
		},
		async onLabelModeChange(value) {
			this.loading = true
			await this.saveSettings('default_label_mode', (value.id - 1).toString())
			this.loading = false
		},
		onCustomLabelChange(value) {
			this.customLabel = value
			// validity check?
		},
		onMinLengthChange(value) {
			this.minLength = value
			if (isNaN(value)) {
				// TODO: show error
			} else if (parseInt(value) < 1) {
				// TODO: show error
			}
		},
		async saveSettings(key, value) {
			const data = {
				key,
				value,
			}

			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/settings/save'), data)
				console.info(response)
			} catch (e) {
				console.error(e.response)
			}
		},
	},
}
</script>
