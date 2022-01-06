<template>
	<div id="cfgshare-admin-settings">
		<SettingsSection
			:title="t('cfgsharelinks', 'Default share label')"
			:description="t('cfgsharelinks', 'Configure whether a default label should be set to custom links and what that label should be')">
			<div>
				<h3>
					{{ t('cfgsharelinks', 'Default label') }}:
					<span
						v-if="updating.key === 'default_label_mode'"
						:class="'status-icon '.concat(updatingIcon)" />
				</h3>
				<Multiselect
					v-model="labelMode"
					:options="labelOptions"
					track-by="id"
					label="label"
					:multiple="false"
					:allow-empty="false"
					:disabled="updating.status === 1 || loading"
					:placeholder="t('cfgsharelinks', 'Select label mode')"
					@update:value="onLabelModeChange" />
			</div>
			<div>
				<h3>
					{{ t('cfgsharelinks', 'Custom label') }}:
					<span
						v-if="updating.key === 'default_label'"
						:class="'status-icon '.concat(updatingIcon)" />
				</h3>
				<SettingsInputText
					id="default-label"
					label=""
					:value="customLabel"
					:disabled="updating.status === 1 || loading || labelMode.id !== 2"
					@update:value="onCustomLabelChange"
					@submit="onLabelSubmit" />
			</div>
		</SettingsSection>
		<SettingsSection
			:title="t('cfgsharelinks', 'Token settings')"
			:description="t('cfgsharelinks', 'Configure requirements for tokens')">
			<div>
				<h3>
					{{ t('cfgsharelinks', 'Minimal token length') }}:
					<span
						v-if="updating.key === 'min_token_length'"
						:class="'status-icon '.concat(updatingIcon)" />
				</h3>
				<SettingsInputText
					id="min-len"
					label=""
					:value="minLength"
					:disabled="updating.status === 1 || loading"
					@update:value="onMinLengthChange"
					@submit="onMinLengthSubmit" />
				<span v-if="isMinLenValid" class="form-error"> {{ isMinLenValid }} </span>
			</div>
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
import '@nextcloud/dialogs/styles/toast.scss'
import { showError } from '@nextcloud/dialogs'

const labelOptions = [
	{ id: 0, label: t('cfgsharelinks', 'None') },
	{ id: 1, label: t('cfgsharelinks', 'Same as token') },
	{ id: 2, label: t('cfgsharelinks', 'Custom') },
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
			updating: {
				status: 0,
				key: null,
			},
			loading: true,
			labelMode: labelOptions[0],
			labelOptions,
			customLabel: 'Custom link',
			minLength: '3',
		}
	},

	computed: {
		isMinLenValid() {
			const minLength = this.minLength

			if (isNaN(minLength)) {
				return t('cfgsharelinks', 'Entered length is not a number')
			}

			if (parseInt(minLength) < 1) {
				return t('cfgsharelinks', 'Minimum length must be at least 1')
			}

			return null
		},
		updatingIcon() {
			switch (this.updating.status) {
			case 1:
				return 'icon-loading-small'
			case 2:
				return 'icon-checkmark'
			case 3:
				return 'icon-error'
			default:
				return ''
			}
		},
	},

	async mounted() {
		this.loading = true

		this.customLabel = await this.getCustomLabel()
		this.minLength = await this.getMinTokenLength()
		this.labelMode = labelOptions[await this.getLabelMode()]

		this.loading = false
	},

	methods: {
		setUpdate(key, status) {
			this.updating.status = status
			this.updating.key = key
		},
		async onLabelSubmit() {
			if (this.customLabel == null || this.customLabel.length === 0) {
				showError(t('cfgsharelinks', 'Label can\'t be empty'))
				return
			}
			// validity check?
			await this.saveSettings('default_label', this.customLabel)
		},
		async onMinLengthSubmit() {
			const minLength = this.minLength
			const minLenError = this.isMinLenValid

			if (minLenError) {
				showError(minLenError)
				return
			}

			await this.saveSettings('min_token_length', minLength)
		},
		async onLabelModeChange(value) {
			if (!value) {
				return
			}
			await this.saveSettings('default_label_mode', value.id.toString())
		},
		onCustomLabelChange(value) {
			this.customLabel = value
		},
		onMinLengthChange(value) {
			this.minLength = value
			// could do validity check here instead of computed
		},
		async saveSettings(key, value) {
			const data = {
				key,
				value,
			}

			this.setUpdate(key, 1)
			try {
				const response = await axios.post(generateUrl('/apps/cfgsharelinks/settings/save'), data)
				console.info(response)
				this.setUpdate(key, 2)
			} catch (e) {
				if (e.response.data && e.response.data.message) {
					showError(t('cfgsharelinks', e.response.data.message))
				} else {
					showError(t('cfgsharelinks', 'Error occurred while saving settings'))
					console.error(e.response)
				}
				this.setUpdate(key, 3)
			}
		},
	},
}
</script>

<style lang="scss" scoped>
.form-error {
	color: #c40c0c;
	display: block;
}
.status-icon {
	display: inline-block;
	margin-left: 6px;
}
</style>
