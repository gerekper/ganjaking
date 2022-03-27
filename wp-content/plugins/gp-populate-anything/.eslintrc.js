module.exports = {
	extends: [
		'plugin:cypress/recommended',
		'plugin:@wordpress/eslint-plugin/recommended',
	],
	plugins: [ 'unused-imports' ],
	rules: {
		'unused-imports/no-unused-imports': 'error',
	},
	parserOptions: {
		requireConfigFile: false,
	},
};
