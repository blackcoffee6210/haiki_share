const mix = require('laravel-mix');

mix.webpackConfig({
      module: {
        rules: [ // scssファイルのimport時、ワイルドカードを使えるようにする設定
          {
            test: /\.scss/,
            enforce: "pre",
            loader: 'import-glob-loader'
          }
        ]
      }
    })
    .js('resources/js/app.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .options({ processCssUrls: false })
    .sourceMaps()
    .version();


// /*
//  |--------------------------------------------------------------------------
//  | Mix Asset Management
//  |--------------------------------------------------------------------------
//  |
//  | Mix provides a clean, fluent API for defining some Webpack build steps
//  | for your Laravel application. By default, we are compiling the Sass
//  | file for the application as well as bundling up all the JS files.
//  |
//  */
//
// mix.js('resources/js/app.js', 'public/js')
//     .sass('resources/sass/app.scss', 'public/css')
//     .sourceMaps();
