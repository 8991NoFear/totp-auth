const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// mix.js('resources/js/app.js', 'public/js')
//     .postCss('resources/css/app.css', 'public/css', [
//         //
//     ]);

mix.postCss('resources/css/auth/login.css', 'public/css/auth')
    .postCss('resources/css/account/sidebars.css', 'public/css/account')
    .postCss('resources/css/account/verify-setup2fa.css', 'public/css/account');