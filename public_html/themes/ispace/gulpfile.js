var gulp = require("gulp");
const sass = require("gulp-sass")(require("sass"));
var browserSync = require("browser-sync").create();

gulp.task("sass", function () {
  return gulp
    .src("scss/**/*.scss")
    .pipe(sass()) // Using gulp-sass
    .pipe(gulp.dest("css/"));
});

gulp.task("watch", function () {
  gulp.watch("scss/**/*.scss", gulp.series("sass"));
});
