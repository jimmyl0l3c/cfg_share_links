const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry['reg-new'] = path.join(__dirname, 'src', 'reg_new_link.js')
webpackConfig.entry['reg-rename'] = path.join(__dirname, 'src', 'reg_rename_link.js')
webpackConfig.entry['settings-admin'] = path.join(__dirname, 'src', 'settings_admin.js')

module.exports = webpackConfig
