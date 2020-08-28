const Bundler = require("parcel-bundler");
const Path = require("path");

const file = Path.join(__dirname, "./src/main.js");

const devOptions = {
  outDir: Path.join(__dirname, "./dist"),
  outFile: "bundle.js",
  watch: true,
  cache: false,
  minify: false,
  hmr: false,
  sourceMaps: false
};

const devBundler = new Bundler(file, devOptions);

devBundler.bundle();
