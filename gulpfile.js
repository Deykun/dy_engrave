var gulp = require('gulp');

var sass = require('gulp-sass');
var prefix = require('gulp-autoprefixer');
var minifycss = require('gulp-minify-css');
var rename = require('gulp-rename');

//css
gulp.task('css', function (){
    gulp.src(['./sass/engrave.scss'])
        .pipe(sass())
		.pipe(rename('engrave.css'))
        .pipe(prefix(
            "last 1 version", "> 1%", "ie 8", "ie 7"
            ))
        .pipe(minifycss())
        .pipe(gulp.dest('./css/'));
});


gulp.task('default', function(){

    gulp.watch("./sass/*.scss", function(event){
        gulp.run('css');
    });
	
});
