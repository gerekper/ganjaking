const Bundler = require("parcel-bundler");
const Path = require("path");

const file = Path.join(__dirname, "./src/main.js");

const buildOptions = {
  outDir: Path.join(__dirname, "./dist"),
  outFile: "bundle.min.js",
  watch: true,
  cache: false,
  minify: true,
  hmr: false,
  sourceMaps: false
};

const buildBundler = new Bundler(file, buildOptions);

buildBundler.bundle();
