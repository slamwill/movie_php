/*
安裝
npm install gulp
npm install gulp-concat --save-dev
npm install gulp-clean-css --save-dev
npm install gulp-uglify-es --save-dev
npm install gulp-rename --save-dev
npm install jshint gulp-jshint --save-dev
npm install gulp-imagemin --save-dev
npm install gulp-util --save-dev

執行
gulp minifycss
gulp minifyjs

*/
var gulp       = require('gulp'),
    concat     = require('gulp-concat'),
    minifyCSS  = require('gulp-clean-css'),
    uglify     = require('gulp-uglify-es').default,
    rename	   = require('gulp-rename'),
	gulpImagemin = require('gulp-imagemin'),
    jshint	   = require('gulp-jshint');
var gutil = require('gulp-util');

gulp.task('minifycss', function() {
    /*return gulp.src('./public/css/*.css')*/
    return gulp.src([
		'./public/css/app.css',
		'./public/css/slidebars.css',
		'./public/css/video-js.min.css',
		'./public/css/videojs-skin.css',
		'./public/css/swiper.min.css',
		'./public/css/drawer.min.css',
		'./public/css/jquery.rwd.tabs.css',
		'./public/plugin/layer/3.1.0/theme/default/layer.css',
		'./public/css/jquery.scrollbar.css',
		'./public/css/globals.css',
		]).pipe(minifyCSS()).on('error',function (e){
			console.log(e)
		})
        .pipe(concat('all.css'))
		.pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./public/dist/'));
});




gulp.task('minifyjs', function() {

	/*return gulp.src('./public/js/*.js')*/
    return gulp.src([
		'./public/js/app.js',
		'./public/js/api.config.js',
		'./public/js/app.model.js',
		'./public/js/api.model.js',
		'./public/js/av.model.js',
		'./public/js/swiper.min.js',
		'./public/js/jquery.cookie.min.js',
		'./public/js/progress.js',
		'./public/js/nprogress.js',
		'./public/js/jquery.pjax.min.js',
		'./public/js/jquery.scrollbar.min.js',
		'./public/js/iscroll.min.js',
		'./public/js/drawer.min.js',
		]).pipe(uglify()).on('error', function (err) { gutil.log(gutil.colors.red('[Error]'), err.toString()); })
        .pipe(concat('all.js'))
		.pipe(rename({suffix: '.min'}))
        .pipe(gulp.dest('./public/dist/'));
});

gulp.task('image', function () {
    gulp.src('./public/images/**')
        .pipe(gulpImagemin())
        .pipe(gulp.dest('./public/dist/images/'));
});

gulp.task('default',['minifycss','minifyjs']);
