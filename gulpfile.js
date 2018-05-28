var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
  mix.copy('./resources/assets/css', './public/css');
  mix.copy('./resources/assets/js', './public/js');
  mix.copy('./resources/assets/slideshow', './public/slideshow');
  mix.copy('./resources/assets/images', './public/images');

  mix.copy('./node_modules/bootstrap', './public/libraries/bootstrap');
  mix.copy('./node_modules/moment', './public/libraries/moment');
  mix.copy('./node_modules/chartjs', './public/libraries/chartjs');
  mix.copy('./node_modules/vuetable', './public/libraries/vuetable');

  mix.copy('./bower_components/eonasdan-bootstrap-datetimepicker', './public/libraries/eonasdan-bootstrap-datetimepicker');
});
