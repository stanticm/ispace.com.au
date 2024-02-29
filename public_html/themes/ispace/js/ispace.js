let elementsArray = document.querySelectorAll(".paragraph");
console.log(elementsArray);
window.addEventListener("scroll", fadeIn);
function fadeIn() {
  for (var i = 0; i < elementsArray.length; i++) {
    var elem = elementsArray[i];
    var distInView = elem.getBoundingClientRect().top - window.innerHeight + 20;
    if (distInView < 0) {
      elem.classList.add("inView");
    } else {
      elem.classList.remove("inView");
    }
  }
}
fadeIn();

jQuery(document).ready(function () {
  // Function to add classes to fields with a specified delay
  function addClassesWithDelay(element, className, delay) {
    setTimeout(function () {
      element.addClass(className);
    }, delay);
  }

  // Function to remove classes from fields
  function removeClasses(element, className) {
    element.removeClass(className);
  }
  jQuery(".paragraph-card-with-overlay").hover(
    function () {
      var overlayContainer = jQuery(this).find(
        ".paragraph-card-with-overlay__overlay-container"
      );
      var fieldTitle = overlayContainer.find(".title");
      var fieldSubtitle = overlayContainer.find(".field-subtitle");
      var fieldButton = overlayContainer.find(".field-button");

      // Adding classes with the specified delays
      console.log(fieldTitle);
      addClassesWithDelay(fieldTitle, "slide-in", 400);
      addClassesWithDelay(fieldSubtitle, "slide-in", 800);
      addClassesWithDelay(fieldButton, "slide-in", 1200);
    },
    function () {
      var overlayContainer = jQuery(this).find(
        ".paragraph-card-with-overlay__overlay-container"
      );
      var fieldTitle = overlayContainer.find(".title");
      var fieldSubtitle = overlayContainer.find(".field-subtitle");
      var fieldButton = overlayContainer.find(".field-button");

      // Removing classes when the hover ends
      removeClasses(fieldTitle, "slide-in");
      removeClasses(fieldSubtitle, "slide-in");
      removeClasses(fieldButton, "slide-in");
    }
  );
  jQuery(".paragraph-banner-with-overlay").hover(
    function () {
      var bannerContainer = jQuery(this).find(
        ".paragraph-banner-with-overlay__overlay-container"
      );
      var fieldTitle = bannerContainer.find(".title");
      var fieldSubtitle = bannerContainer.find(".field-subtitle");
      var fieldButton = bannerContainer.find(".field-button");

      // Adding classes with the specified delays
      console.log(fieldTitle);
      addClassesWithDelay(fieldTitle, "slide-in", 400);
      addClassesWithDelay(fieldSubtitle, "slide-in", 800);
      addClassesWithDelay(fieldButton, "slide-in", 1200);
    },
    function () {
      var overlayContainer = jQuery(this).find(
        ".paragraph-banner-with-overlay__overlay-container"
      );
      var fieldTitle = overlayContainer.find(".title");
      var fieldSubtitle = overlayContainer.find(".field-subtitle");
      var fieldButton = overlayContainer.find(".field-button");

      // Removing classes when the hover ends
      removeClasses(fieldTitle, "slide-in");
      removeClasses(fieldSubtitle, "slide-in");
      removeClasses(fieldButton, "slide-in");
    }
  );
});

// let prevScrollPos = window.scrollY;
// const header = document.getElementById("header");

// window.onscroll = function () {
//   const currentScrollPos = window.scrollY;

//   if (prevScrollPos > currentScrollPos) {
//     // Scrolling up
//     header.classList.remove("hidden");
//   } else {
//     // Scrolling down
//     header.classList.add("hidden");
//   }

//   prevScrollPos = currentScrollPos;
// };

jQuery(document).ready(function () {
  // Add click event listener to each title with class "field-title"
  jQuery(".field-title").click(function () {
    // Toggle the "active" class for the container when the title is clicked
    jQuery(this).closest(".paragraph-q-a").toggleClass("active");
  });
});
