const path = require("path");
const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const { CleanWebpackPlugin } = require("clean-webpack-plugin");
const MiniCSSExtractPlugin = require("mini-css-extract-plugin");

const isProduction = process.env.NODE_ENV === "production";

const plugins = defaultConfig.plugins.filter(
    (plugin) =>
        plugin.constructor.name != "MiniCssExtractPlugin" &&
        plugin.constructor.name != "CleanWebpackPlugin"
);

const config = {
    ...defaultConfig,
    entry: {
        "betterdocs-analytics": path.resolve(__dirname, "src/index.js"),
    },
    module: {
        ...defaultConfig.module,
        rules: [
            ...defaultConfig.module.rules,
        ],
    },
    output: {
        ...defaultConfig.output,
        filename: "js/[name].js",
        path: path.resolve(process.cwd(), "../assets" ),
    },
    plugins: [
        new MiniCSSExtractPlugin({
            filename: ({ chunk }) => {
                return `css/${chunk.name}.css`;
            },
        }),
        ...plugins,
    ],
};

module.exports = config;
