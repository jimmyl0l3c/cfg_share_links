module.exports = {
	globals: {
		appVersion: true,
	},
	parserOptions: {
		requireConfigFile: false,
	},
	extends: ['@nextcloud', 'prettier'],
	rules: {
		'jsdoc/require-jsdoc': 'off',
		'jsdoc/tag-lines': 'off',
		'vue/first-attribute-linebreak': 'off',
		'import/extensions': 'off',
	},
}
