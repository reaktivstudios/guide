const globals = require('globals');
const js = require('@eslint/js');
const reactPlugin = require('eslint-plugin-react');
const prettierPlugin = require('eslint-plugin-prettier');

module.exports = [
	js.configs.recommended,
	{
		languageOptions: {
			ecmaVersion: 'latest',
			sourceType: 'module',
			parserOptions: {
				ecmaFeatures: {
					jsx: true,
				},
			},
			globals: {
				...globals.browser,
				...globals.node,
				...globals.jest,
				document: true,
				fetch: true,
				FormData: true,
				Headers: true,
				window: true,
				wp: true,
				wpApiSettings: true,
				jQuery: true,
				getUserSetting: true,
				switchEditors: true,
				setUserSetting: true,
				Promise: true,
			},
		},
		plugins: {
			react: reactPlugin,
			prettier: prettierPlugin,
		},
		rules: {
			...reactPlugin.configs.recommended.rules,
			...prettierPlugin.configs.recommended.rules,
			'func-style': [
				'error',
				'expression',
				{
					allowArrowFunctions: true,
				},
			],
			'no-console': [
				'error',
				{
					allow: ['warn', 'error'],
				},
			],
			'no-unused-vars': 'error',
			'no-var': 'error',
			'prefer-arrow-callback': 'error',
			'prefer-const': 'error',
			'react/no-unescaped-entities': 'off',
			'react/react-in-jsx-scope': 'off',
			'react/prop-types': 'off',
		},
		settings: {
			react: {
				version: 'detect',
			},
		},
	},
];
