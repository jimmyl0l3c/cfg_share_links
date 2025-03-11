import { RenameShareAction } from './rename-share-action.ts'

console.info('CfgShareLinks: RenameLink init')

// Add rename input
window.addEventListener('DOMContentLoaded', () => {
	if (OCA.Sharing && OCA.Sharing.ExternalShareActions) {
		console.info('CfgShareLinks: Registering rename action')
		OCA.Sharing.ExternalShareActions.registerAction(new RenameShareAction())
	}
})
