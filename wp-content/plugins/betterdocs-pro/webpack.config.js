const path = require("path");
const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const MiniCSSExtractPlugin = require("mini-css-extract-plugin");
const RemoveEmptyScriptsPlugin = require("webpack-remove-empty-scripts");
const CopyPlugin = require("copy-webpack-plugin");

const isProduction = process.env.NODE_ENV === "production";
const getJSFiles = require("./tools/webpack/getJSFiles");
const getEntries = require("./tools/webpack/getEntries");

const plugins = defaultConfig.plugins.filter(
    (plugin) =>
        plugin.constructor.name != "MiniCssExtractPlugin" &&
        plugin.constructor.name != "CleanWebpackPlugin"
);

const shortcodeJSEntries = {
    "public/js/mkb-tab-grid": "./react-src/public/mkb-tab-grid.js",
};

const blocksEntriesFolder = path.join(__dirname, "/react-src/gutenberg/blocks");
const blocksEditorJSEntries = Object.values(
    getEntries(blocksEntriesFolder, "editor")
);

const config = {
    ...defaultConfig,
    entry: {
        "blocks/advanced-search/advanced-search":"./react-src/gutenberg/blocks/advanced-search/index.js",
        // Blocks Editor JS
        "blocks/editor": blocksEditorJSEntries,
        // Blocks Admin Editor CSS
        "blocks/controls": "./react-src/gutenberg/util/backend.scss",
        // Customizer,
        ...getJSFiles(__dirname + "/react-src/admin/customizer"),
        // Public JS
        ...getJSFiles(__dirname + "/react-src/public"),
        // Settings
        "admin/js/settings": path.resolve(
            __dirname,
            "react-src/admin/settings/index.js"
        ),
        // Admin
        "admin/js/betterdocs": path.resolve(
            __dirname,
            "react-src/admin/betterdocs.js"
        ),
        // Analytics
        "admin/js/analytics": path.resolve(
            __dirname,
            "react-src/admin/index.js"
        ),
    },
    module: {
        ...defaultConfig.module,
        rules: [...defaultConfig.module.rules],
    },
    output: {
        path: path.join(__dirname, "assets/"),
        filename: "[name].js",
    },
    plugins: [
        ...plugins,
        new RemoveEmptyScriptsPlugin(),
        new MiniCSSExtractPlugin({
            filename: ({ chunk }) =>
                `${chunk.name.replace("/js/", "/css/")}.css`,
        }),
        new CopyPlugin({
            patterns: [
                {
                    from: blocksEntriesFolder + "/**/block.json",
                    to: `./blocks`,
                    toType: "dir",
                    context: blocksEntriesFolder,
                },
            ],
            options: {
                concurrency: 50,
            },
        }),
    ],
};

module.exports = config;
