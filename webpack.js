const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry['extend-share'] = path.join(__dirname, 'src', 'extend_share.js')

module.exports = webpackConfig
