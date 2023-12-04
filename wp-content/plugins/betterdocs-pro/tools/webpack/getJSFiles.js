const fs = require('fs');
const path = require('path');

function getJsFiles(dirPath, fileList = {}, _recursive = true) {
    const files = fs.readdirSync(dirPath);

    files.forEach((file) => {
        const filePath = path.join(dirPath, file);

        if (_recursive && fs.statSync(filePath).isDirectory()) {
            fileList = getJsFiles(filePath, fileList);
        } else if (path.extname(filePath).toLowerCase() === '.js') {
            const keyName = `${path.basename(path.dirname(filePath))}/js/${path.basename(filePath, '.js')}`;
            fileList[keyName] = filePath;
        }
    });

    return fileList;
}

module.exports = getJsFiles;
