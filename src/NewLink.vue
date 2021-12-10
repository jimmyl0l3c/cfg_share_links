<template>
	<ListItem
		:title="'Title of the element'"
		:bold="false"
		:details="'One hour ago'">
		<template #icon>
			<Avatar :size="44" user="admin" display-name="Jane Doe" />
		</template>
		<template #subtitle>
			In this slot you can put both text and other components such as icons
		</template>
		<template #actions>
			<ActionButton>
				Button one
			</ActionButton>
			<ActionButton>
				Button two
			</ActionButton>
		</template>
	</ListItem>
</template>

<script>
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import Avatar from '@nextcloud/vue/dist/Components/Avatar'
import ListItem from '@nextcloud/vue/dist/Components/ListItem'

export default {
	name: 'NewLink',

	components: {
		ActionButton,
		Avatar,
		ListItem,
	},

	props: {
		fileInfo: {
			type: Object,
			default: () => {},
			required: true,
		},
	},
}
</script>

<style scoped>
#subtitle {
	color: gray;
}
</style>
