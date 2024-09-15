import { createAppConfig } from '@nextcloud/vite-config'

export default createAppConfig({
	main: 'src/main.js',
	regNewLink: 'src/reg_new_link.js',
	regRenameLink: 'src/reg_rename_link.js',
	settings: 'src/settings.js',
})
