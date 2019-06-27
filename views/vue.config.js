module.exports = {
  outputDir: "../public",
  lintOnSave: false,
  devServer: {
    proxy: {
      "^/api": {
        target: "http://tool.dev.100cbc.com",
        ws: true,
        changeOrigin: true,
        pathRewrite: {
          "^/api": ""
        }
      }
    }
  }
};
