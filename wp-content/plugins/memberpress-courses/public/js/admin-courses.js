(function ($) {
  // Document ready functions
  $(function () {
    const CourseEditor = new window.meprCourseEditor();

    if (CourseEditor.isGutenbergEditor()) {
      // Detect external link is clicked
      $('body').on("click", "a", function (e) {
        e.preventDefault();

        // Links to same page, do nothing
        if (
          this.pathname == window.location.pathname &&
          this.protocol == window.location.protocol &&
          this.host == window.location.host &&
          this.search == window.location.search
        ) {
          return;
        }

        let url = $(this).attr("href");
        let target = $(this).attr("target");
        let data = wp.data.select("memberpress/course/curriculum").getAll();
        let unSavedChanges = data.unSavedChanges;

        if (true != unSavedChanges) {
          if("_self" == target){
            window.location.href = url;
            return;
          }
          window.open(url, '_blank')
          return;
        }

        CourseEditor.showPrompt(url);
      });

    }
  });
})(jQuery);
