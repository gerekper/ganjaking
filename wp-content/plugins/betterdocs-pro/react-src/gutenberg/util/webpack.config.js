const path = require("path");
const defaultConfig = require("@wordpress/scripts/config/webpack.config");

module.exports = {
	...defaultConfig,
	entry: {
		index: path.resolve(__dirname, "index.js"),
	},
	output: {
		...defaultConfig.output,
		devtoolNamespace: "wp",
		filename: "index.js",
		path: path.resolve(__dirname, "../admin/block-controls"),
		library: ["eb_controls"],
		libraryTarget: "window",
	},
};