<template>
	<div id="cfgshare-admin-settings">
		<NcSettingsSection
			:name="t('cfg_share_links', 'Default share label')"
			:description="
				t(
					'cfg_share_links',
					'Configure whether a default label should be set to custom links and what that label should be',
				)
			">
			<div>
				<h3>
					{{ t('cfg_share_links', 'Default label') }}:
					<span
						v-if="updating.key === SettingsKey.DefaultLabelMode"
						class="status-icon">
						<NcLoadingIcon
							v-if="updating.status === UpdateState.Updating"
							:name="t('cfg_share_links', 'Saving...')"
							:size="20" />
						<CheckIcon
							v-else-if="updating.status === UpdateState.Completed"
							:size="20" />
						<AlertIcon
							v-else-if="updating.status === UpdateState.Error"
							:size="20" />
					</span>
				</h3>
				<NcSelect
					v-model="labelMode"
					:options="labelOptions"
					track-by="id"
					label="label"
					:multiple="false"
					:allow-empty="false"
					:disabled="updating.status === UpdateState.Updating || loading"
					:placeholder="t('cfg_share_links', 'Select label type')"
					@option:selected="onLabelModeChange" />
			</div>
			<div v-if="labelMode.id === LabelMode.UserSpecified">
				<h3>
					{{ t('cfg_share_links', 'Custom label') }}:
					<span
						v-if="updating.key === SettingsKey.DefaultCustomLabel"
						class="status-icon">
						<NcLoadingIcon
							v-if="updating.status === UpdateState.Updating"
							:name="t('cfg_share_links', 'Saving...')"
							:size="20" />
						<CheckIcon
							v-else-if="updating.status === UpdateState.Completed"
							:size="20" />
						<AlertIcon
							v-else-if="updating.status === UpdateState.Error"
							:size="20" />
					</span>
				</h3>
				<NcSettingsInputText
					id="default-label"
					label=""
					:value.sync="customLabel"
					:disabled="
						updating.status === UpdateState.Updating ||
						loading ||
						labelMode.id !== LabelMode.UserSpecified
					"
					@submit="onLabelSubmit" />
			</div>
		</NcSettingsSection>
		<NcSettingsSection
			:name="t('cfg_share_links', 'Token settings')"
			:description="t('cfg_share_links', 'Configure requirements for tokens')">
			<div>
				<h3>
					{{ t('cfg_share_links', 'Minimal token length') }}:
					<span
						v-if="updating.key === SettingsKey.MinTokenLength"
						class="status-icon">
						<NcLoadingIcon
							v-if="updating.status === UpdateState.Updating"
							:name="t('cfg_share_links', 'Saving...')"
							:size="20" />
						<CheckIcon
							v-else-if="updating.status === UpdateState.Completed"
							:size="20" />
						<AlertIcon
							v-else-if="updating.status === UpdateState.Error"
							:size="20" />
					</span>
				</h3>
				<NcSettingsInputText
					id="min-len"
					label=""
					:value.sync="minLength"
					:disabled="updating.status === UpdateState.Updating || loading"
					@submit="onMinLengthSubmit" />
				<span v-if="isMinLenValid" class="form-error">
					{{ isMinLenValid }}
				</span>
			</div>
		</NcSettingsSection>
		<NcSettingsSection
			:name="t('cfg_share_links', 'Miscellaneous')"
			:description="t('cfg_share_links', 'Miscellaneous tweaks')">
			<div>
				<NcCheckboxRadioSwitch
					v-tooltip="{
						content: t(
							'cfg_share_links',
							'Keep this option off if you did not use versions lower than 1.2.0',
						),
						placement: 'top-start',
					}"
					:disabled="updating.status === UpdateState.Updating || loading"
					:loading="updating.status === UpdateState.Updating || loading"
					:checked.sync="deleteConflicts"
					type="switch"
					@update:checked="onDeleteConflictsChange">
					{{
						t(
							'cfg_share_links',
							'Delete shares of deleted files during token checks (when creating/updating share)',
						)
					}}
					<span
						v-if="
							updating.key === SettingsKey.DeleteRemovedShareConflicts
						"
						class="status-icon">
						<NcLoadingIcon
							v-if="updating.status === UpdateState.Updating"
							:name="t('cfg_share_links', 'Saving...')"
							:size="20" />
						<CheckIcon
							v-else-if="updating.status === UpdateState.Completed"
							:size="20" />
						<AlertIcon
							v-else-if="updating.status === UpdateState.Error"
							:size="20" />
					</span>
				</NcCheckboxRadioSwitch>
			</div>
		</NcSettingsSection>
	</div>
</template>

<script lang="ts">
import axios from '@nextcloud/axios'
import { showError } from '@nextcloud/dialogs'
import { t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import {
	NcCheckboxRadioSwitch,
	NcLoadingIcon,
	NcSelect,
	NcSettingsInputText,
	NcSettingsSection,
	Tooltip,
} from '@nextcloud/vue'
import AlertIcon from 'vue-material-design-icons/AlertCircle.vue'

import CheckIcon from 'vue-material-design-icons/Check.vue'
import { LabelMode } from '../enums/LabelMode.ts'
import { SettingsKey } from '../enums/SettingsKey.ts'
import { UpdateState } from '../enums/UpdateState.ts'

import SettingsMixin from '../mixins/SettingsMixin.ts'

import '@nextcloud/dialogs/style.css'

const labelOptions = [
	{ id: LabelMode.NoLabel, label: t('cfg_share_links', 'None') },
	{ id: LabelMode.SameAsToken, label: t('cfg_share_links', 'Same as token') },
	{ id: LabelMode.UserSpecified, label: t('cfg_share_links', 'Custom') },
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
				status: UpdateState.Idle,
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
		LabelMode() {
			return LabelMode
		},
		UpdateState() {
			return UpdateState
		},
		SettingsKey() {
			return SettingsKey
		},
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
		t,
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
			await this.saveSettings(SettingsKey.DefaultCustomLabel, this.customLabel)
		},
		async onMinLengthSubmit() {
			const minLength = this.minLength
			const minLenError = this.isMinLenValid

			if (minLenError) {
				showError(minLenError)
				return
			}

			await this.saveSettings(SettingsKey.MinTokenLength, minLength)
		},
		async onLabelModeChange(value) {
			if (value == null) {
				return
			}
			await this.saveSettings(
				SettingsKey.DefaultLabelMode,
				value.id.toString(),
			)
		},
		async onDeleteConflictsChange(value) {
			await this.saveSettings(
				SettingsKey.DeleteRemovedShareConflicts,
				value ? '1' : '0',
			)
		},
		async saveSettings(key, value) {
			const data = {
				key,
				value,
			}

			this.setUpdate(key, UpdateState.Updating)
			try {
				await axios.post(
					generateUrl('/apps/cfg_share_links/settings/save'),
					data,
				)
				this.setUpdate(key, UpdateState.Completed)
			} catch (e) {
				if (e.response.data && e.response.data.message) {
					showError(t('cfg_share_links', e.response.data.message))
				} else {
					showError(
						t('cfg_share_links', 'Error occurred while saving settings'),
					)
					console.error(e.response)
				}
				this.setUpdate(key, UpdateState.Error)
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
