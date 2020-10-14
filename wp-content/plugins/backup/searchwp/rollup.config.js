import commonjs from '@rollup/plugin-commonjs';
import path from 'path';
import alias from '@rollup/plugin-alias';
import replace from '@rollup/plugin-replace';
import resolve from '@rollup/plugin-node-resolve';
import RollupVue from 'rollup-plugin-vue';
import css from 'rollup-plugin-css-only';
import { terser } from 'rollup-plugin-terser';

let getConfig = function(handle, output, replace, terser) {
	return {
		input: 'assets/javascript/src/' + handle + '.js',
		output: {
			file: 'assets/javascript/dist/' + output + '.js',
			format: 'iife'
		},
		plugins: [
			alias({
				entries: [
					{ find: "vue", replacement: path.resolve("./node_modules/vue/dist/vue.js") }
				]
			}),
			commonjs({
				include: /node_modules/,
				namedExports: {
					'node_modules/lodash.clonedeep/index.js': ['cloneDeep'],
					'node_modules/lodash.isequal/index.js': ['isEqual']
				}
			}),
			resolve(),
			replace,
			css(),
			RollupVue({ css: false }),
			terser
		]
	};
};

export default ['engines', 'settings', 'advanced', 'statistics', 'support'].map(function(bundle){
	return [
		// Development version.
		getConfig(bundle, bundle, replace({
			'process.env.NODE_ENV': JSON.stringify('development')
		}), null),
		// Production version.
		getConfig(bundle, bundle + '.min', replace({
			'process.env.NODE_ENV': JSON.stringify('production')
		}), terser())
	];
}).reduce((a, b) => a.concat(b), []);