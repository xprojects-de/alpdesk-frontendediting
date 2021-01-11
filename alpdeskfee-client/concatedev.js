const fs = require('fs-extra');
const concat = require('concat');
(async function build() {
    const files = [
        './mootoolscompat.js',
        './dist/alpdeskfee-client/runtime.js',
        './dist/alpdeskfee-client/polyfills.js',
        './dist/alpdeskfee-client/vendor.js',
        './dist/alpdeskfee-client/main.js',
    ]
    await fs.ensureDir('elements')
    await concat(files, 'elements/alpdeskfee-elements.js');
    await fs.copyFile('./dist/alpdeskfee-client/styles.css', 'elements/styles.css')
    //await fs.copy('./dist/angular-elements/assets/', 'elements/assets/')
})()
