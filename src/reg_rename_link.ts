import { RenameShareAction } from './rename-share-action.ts'

console.info('CfgShareLinks: RenameLink init')

// Add rename input
window.addEventListener('DOMContentLoaded', () => {
	if (OCA.Sharing && OCA.Sharing.ExternalShareActions) {
		OCA.Sharing.ExternalShareActions.registerAction(new RenameShareAction())
	}
})
