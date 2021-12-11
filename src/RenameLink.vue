<template>
	<ActionInput icon="icon-public" @submit="onSubmit">
		{{ currentToken }}
	</ActionInput>
</template>

<script>
import ActionInput from '@nextcloud/vue/dist/Components/ActionInput'

export default {
	id: 'rename-link',
	name: 'RenameLink',
	components: {
		ActionInput,
	},
	props: {
		share: {
			type: Object,
			default: () => {},
			required: true,
		},
		fileInfo: {
			type: Object,
			default: () => {},
			required: true,
		},
	},
	computed: {
		currentToken() {
			return this.share && this.share.token ? this.share.token : 'Enter token'
		},
	},
	methods: {
		onSubmit(e) {
			console.info(this.share)
		},
	},
}
</script>
