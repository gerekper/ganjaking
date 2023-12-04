const fs = require('fs');
const path = require('path');

const isDir = (fileName) => {
    try {
        return fs.lstatSync(fileName).isDirectory();
    } catch (e) {
        return false;
    }
};
const isFile = (fileName) => {
    try {
        return fs.lstatSync(fileName).isFile();
    } catch (e) {
        return false;
    }
};

module.exports = (blocksFolder, editorMode = 'both', filterOut = [] ) => {
    const isDirExists = fs.existsSync(blocksFolder) && isDir(blocksFolder)

    if (isDirExists) {
        let entries = {};
        let srcIndex = '/src/index.js'
        let frontendJS = '/src/frontend.js'

        fs.readdirSync(blocksFolder).map((fileName) => {
            let _isDir = isDir(path.join(blocksFolder, fileName));

            if( filterOut?.length > 0 && filterOut?.includes( fileName ) ) {
                return;
            }

            if( ! _isDir ) {
                return;
            }

            const validBlock = isFile(path.join(blocksFolder, fileName + srcIndex));
            const validFrontEndJS = isFile(path.join(blocksFolder, fileName + frontendJS));

            if (validBlock && ( editorMode == 'both' || editorMode == 'editor' ) ) {
                let key = `blocks/${fileName}/index`
                let _path = `${blocksFolder}/${fileName}`
                entries[key] = `${_path}${srcIndex}`
            }

            if( validFrontEndJS && ( editorMode == 'both' || editorMode == 'frontend' ) ) {
                let key = `blocks/${fileName}/frontend`
                let _path = `${blocksFolder}/${fileName}`
                entries[key] = `${_path}${frontendJS}`
            }
        })
        return entries
    }
}
