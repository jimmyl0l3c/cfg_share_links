<template>
	<div id="cfgshare-admin-settings">
		<NcSettingsSection :name="t('cfg_share_links', 'Default share label')"
			:description="
				t(
					'cfg_share_links',
					'Configure whether a default label should be set to custom links and what that label should be',
				)
			">
			<div>
				<h3>
					{{ t('cfg_share_links', 'Default label') }}:
					<span v-if="updating.key === 'default_label_mode'"
						class="status-icon">
						<NcLoadingIcon v-if="updating.status === 1"
							:name="t('cfg_share_links', 'Saving...')"
							:size="20" />
						<CheckIcon v-else-if="updating.status === 2" :size="20" />
						<AlertIcon v-else-if="updating.status === 3" :size="20" />
					</span>
				</h3>
				<NcSelect v-model="labelMode"
					:options="labelOptions"
					track-by="id"
					label="label"
					:multiple="false"
					:allow-empty="false"
					:disabled="updating.status === 1 || loading"
					:placeholder="t('cfg_share_links', 'Select label type')"
					@option:selected="onLabelModeChange" />
			</div>
			<div v-if="labelMode.id === 2">
				<h3>
					{{ t('cfg_share_links', 'Custom label') }}:
					<span v-if="updating.key === 'default_label'" class="status-icon">
						<NcLoadingIcon v-if="updating.status === 1"
							:name="t('cfg_share_links', 'Saving...')"
							:size="20" />
						<CheckIcon v-else-if="updating.status === 2" :size="20" />
						<AlertIcon v-else-if="updating.status === 3" :size="20" />
					</span>
				</h3>
				<NcSettingsInputText id="default-label"
					label=""
					:value.sync="customLabel"
					:disabled="updating.status === 1 || loading || labelMode.id !== 2"
					@submit="onLabelSubmit" />
			</div>
		</NcSettingsSection>
		<NcSettingsSection :name="t('cfg_share_links', 'Token settings')"
			:description="t('cfg_share_links', 'Configure requirements for tokens')">
			<div>
				<h3>
					{{ t('cfg_share_links', 'Minimal token length') }}:
					<span v-if="updating.key === 'min_token_length'" class="status-icon">
						<NcLoadingIcon v-if="updating.status === 1"
							:name="t('cfg_share_links', 'Saving...')"
							:size="20" />
						<CheckIcon v-else-if="updating.status === 2" :size="20" />
						<AlertIcon v-else-if="updating.status === 3" :size="20" />
					</span>
				</h3>
				<NcSettingsInputText id="min-len"
					label=""
					:value.sync="minLength"
					:disabled="updating.status === 1 || loading"
					@submit="onMinLengthSubmit" />
				<span v-if="isMinLenValid" class="form-error">
					{{ isMinLenValid }}
				</span>
			</div>
		</NcSettingsSection>
		<NcSettingsSection :name="t('cfg_share_links', 'Miscellaneous')"
			:description="t('cfg_share_links', 'Miscellaneous tweaks')">
			<div>
				<NcCheckboxRadioSwitch v-tooltip="{
						content: t(
							'cfg_share_links',
							'Keep this option off if you did not use versions lower than 1.2.0',
						),
						placement: 'top-start',
					}"
					:disabled="updating.status === 1 || loading"
					:loading="updating.status === 1 || loading"
					:checked.sync="deleteConflicts"
					type="switch"
					@update:checked="onDeleteConflictsChange">
					{{
						t(
							'cfg_share_links',
							'Delete shares of deleted files during token checks (when creating/updating share)',
						)
					}}
					<span v-if="updating.key === 'deleteRemovedShareConflicts'"
						class="status-icon">
						<NcLoadingIcon v-if="updating.status === 1"
							:name="t('cfg_share_links', 'Saving...')"
							:size="20" />
						<CheckIcon v-else-if="updating.status === 2" :size="20" />
						<AlertIcon v-else-if="updating.status === 3" :size="20" />
					</span>
				</NcCheckboxRadioSwitch>
			</div>
		</NcSettingsSection>
	</div>
</template>

<script>
import {
	NcCheckboxRadioSwitch,
	NcLoadingIcon,
	NcSelect,
	NcSettingsInputText,
	NcSettingsSection,
	Tooltip,
} from '@nextcloud/vue'

import CheckIcon from 'vue-material-design-icons/Check.vue'
import AlertIcon from 'vue-material-design-icons/AlertCircle.vue'

import SettingsMixin from '../mixins/SettingsMixin.js'

import '@nextcloud/dialogs/style.css'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

const labelOptions = [
	{ id: 0, label: t('cfg_share_links', 'None') },
	{ id: 1, label: t('cfg_share_links', 'Same as token') },
	{ id: 2, label: t('cfg_share_links', 'Custom') },
]

export default {
	name: 'AdminSettings',

	components: {
		NcSelect,
		NcSettingsSection,
		NcSettingsInputText,
		NcCheckboxRadioSwitch,
		NcLoadingIcon,
		CheckIcon,
		AlertIcon,
	},

	directives: {
		Tooltip,
	},

	mixins: [SettingsMixin],

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
			deleteConflicts: false,
		}
	},

	computed: {
		isMinLenValid() {
			const parsedMinLength = parseInt(this.minLength)

			if (isNaN(parsedMinLength)) {
				return t('cfg_share_links', 'Entered length is not a number')
			}

			if (parsedMinLength < 1) {
				return t('cfg_share_links', 'Minimum length must be at least 1')
			}

			return null
		},
	},

	async mounted() {
		this.loading = true

		this.customLabel = await this.getCustomLabel()
		this.minLength = await this.getMinTokenLength()
		this.labelMode = labelOptions[await this.getLabelMode()]
		this.deleteConflicts = await this.getDeleteRemovedShareConflicts()

		this.loading = false
	},

	methods: {
		setUpdate(key, status) {
			this.updating.status = status
			this.updating.key = key
		},
		async onLabelSubmit() {
			if (this.customLabel == null || this.customLabel.length === 0) {
				showError(t('cfg_share_links', 'Label cannot be empty'))
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
			if (value == null) {
				return
			}
			await this.saveSettings('default_label_mode', value.id.toString())
		},
		async onDeleteConflictsChange(value) {
			await this.saveSettings('deleteRemovedShareConflicts', value ? '1' : '0')
		},
		async saveSettings(key, value) {
			const data = {
				key,
				value,
			}

			this.setUpdate(key, 1)
			try {
				await axios.post(
					generateUrl('/apps/cfg_share_links/settings/save'),
					data,
				)
				this.setUpdate(key, 2)
			} catch (e) {
				if (e.response.data && e.response.data.message) {
					showError(t('cfg_share_links', e.response.data.message))
				} else {
					showError(
						t('cfg_share_links', 'Error occurred while saving settings'),
					)
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
