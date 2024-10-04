const stylelintrc = require('@nextcloud/stylelint-config')

stylelintrc.ignoreFiles = ['**/*.js', '**/*.ts', '**/*.php', '**/*.md']
stylelintrc.rules['selector-pseudo-element-no-unknown'] = [
	true,
	{
		ignorePseudoElements: ['v-deep'],
	},
]

module.exports = stylelintrc
