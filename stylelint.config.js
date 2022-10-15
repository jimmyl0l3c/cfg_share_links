const stylelintConfig = require('@nextcloud/stylelint-config')

stylelintConfig.ignoreFiles = ['**/*.js', '**/*.ts', '**/*.php', '**/*.md']
stylelintConfig.rules['selector-pseudo-element-no-unknown'] = [
	true,
	{
		ignorePseudoElements: ['v-deep'],
	},
]

module.exports = stylelintConfig
