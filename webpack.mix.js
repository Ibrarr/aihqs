let mix = require('laravel-mix');

mix.js(['src/js/dropdown.js', 'src/js/app.js'], 'dist/js/app.js').setPublicPath('dist');
mix.sass('src/css/app.scss', 'dist/css').setPublicPath('dist');

mix.copy('node_modules/@selectize/selectize/dist/js/selectize.js', 'dist/js/selectize.js');
mix.copy('node_modules/@selectize/selectize/dist/css/selectize.css', 'dist/css/selectize.css');

mix.options({
    postCss: [
        require('autoprefixer')({
            overrideBrowserslist: ['last 3 versions'],
            cascade: false
        })
    ]
});

mix.disableNotifications();