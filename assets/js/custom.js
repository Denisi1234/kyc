/* Pikasa HTML5 Template  */

/************ TABLE OF CONTENTS ***************
01. Owl carousel slider
02. Sticky header
03. Prealoader
04. Language switcher
05. PrettyPhoto
06. BrandCarousel
07. Testimonial carousel
08. ScrollToTop 
09. Cart Touch Spin
10. PriceFilter
11. Cart touch spin
12. Fancybox activator
13. ContactFormValidation
14. Scoll to target
15. PrettyPhoto

**********************************************/

"use strict";

//Submenu Dropdown Toggle
if ($(".main-header li.dropdown ul").length) {
  $(".main-header .navigation li.dropdown").append(
    '<div class="dropdown-btn"><span class="fa fa-angle-right"></span></div>',
  );
}

//Mobile Nav Hide Show
if ($(".mobile-menu").length) {
  var populateMobileMenu = function () {
    var $mobileOuter = $(".mobile-menu .menu-box .menu-outer");
    if (!$mobileOuter.length) return;

    var $desktopNavList = $(".main-header .nav-outer .main-menu .navigation");
    var fallbackMenu =
      '<ul class="navigation clearfix">' +
      '<li><a href="index.html"><span>Home</span></a></li>' +
      '<li><a href="mission.html"><span>Mission &amp; Vision</span></a></li>' +
      '<li><a href="leadership.html"><span>Leadership</span></a></li>' +
      '<li><a href="gallery.html"><span>Gallery</span></a></li>' +
      '<li><a href="portal.html"><span>Member Portal</span></a></li>' +
      '<li><a href="join.html"><span>Join the Choir</span></a></li>' +
      "</ul>";

    if ($desktopNavList.length && $desktopNavList.find("li").length) {
      var $clonedList = $desktopNavList.first().clone();
      if (!$clonedList.find('a[href="join.html"]').length) {
        $clonedList.append('<li><a href="join.html"><span>Join the Choir</span></a></li>');
      }
      $mobileOuter.empty().append($clonedList);
    } else if (!$mobileOuter.find(".navigation li").length) {
      $mobileOuter.empty().append(fallbackMenu);
    } else if (!$mobileOuter.find('a[href="join.html"]').length) {
      $mobileOuter.find(".navigation").first().append(
        '<li><a href="join.html"><span>Join the Choir</span></a></li>'
      );
    }
  };

  populateMobileMenu();
  $(window).on("load", populateMobileMenu);

  //Dropdown Button
  $(document).on("click", ".mobile-menu li.dropdown .dropdown-btn", function () {
    $(this).toggleClass("open");
    $(this).prev("ul").slideToggle(500);
    $(this).prev(".megamenu").slideToggle(900);
  });

  //Menu Toggle Btn
  $(".mobile-nav-toggler").on("click", function () {
    populateMobileMenu();
    $("body").addClass("mobile-menu-visible");
  });

  //Menu Toggle Btn
  $(".mobile-menu .menu-backdrop,.mobile-menu .close-btn").on(
    "click",
    function () {
      $("body").removeClass("mobile-menu-visible");
    },
  );
}

// Sticky Header On Scroll
if ($(".main-header").length) {
  var $mainHeader = $(".main-header");
  var $boxedWrapper = $(".boxed_wrapper");
  var isSolidHeader = $mainHeader.hasClass("portal-solid-header");
  var stickyHeaderOffset = 120;
  var headerPlaceholderHeight = isSolidHeader ? $mainHeader.outerHeight() : 0;

  var toggleStickyHeader = function () {
    if (!$mainHeader.hasClass("kyc-sticky") && isSolidHeader) {
      headerPlaceholderHeight = $mainHeader.outerHeight();
    }
    if ($(window).scrollTop() > stickyHeaderOffset) {
      if (!$mainHeader.hasClass("kyc-sticky")) {
        if (isSolidHeader && $boxedWrapper.length) {
          $boxedWrapper.css("padding-top", headerPlaceholderHeight + "px");
        }
        $mainHeader.addClass("kyc-sticky");
      }
    } else {
      if ($mainHeader.hasClass("kyc-sticky")) {
        $mainHeader.removeClass("kyc-sticky");
        if (isSolidHeader && $boxedWrapper.length) {
          $boxedWrapper.css("padding-top", "");
        }
      }
    }
  };

  $(window).on("scroll resize", toggleStickyHeader);
  toggleStickyHeader();
}

// Scroll to a Specific Div
if ($(".scroll-to-target").length) {
  $(".scroll-to-target").on("click", function () {
    var target = $(this).attr("data-target");
    // animate
    $("html, body").animate(
      {
        scrollTop: $(target).offset().top,
      },
      1000,
    );
  });
}

//Parallax Scene for Icons
if ($(".parallax-scene-1").length) {
  var scene = $(".parallax-scene-1").get(0);
  var parallaxInstance = new Parallax(scene);
}
if ($(".parallax-scene-2").length) {
  var scene = $(".parallax-scene-2").get(0);
  var parallaxInstance = new Parallax(scene);
}
if ($(".parallax-scene-3").length) {
  var scene = $(".parallax-scene-3").get(0);
  var parallaxInstance = new Parallax(scene);
}
if ($(".parallax-scene-4").length) {
  var scene = $(".parallax-scene-4").get(0);
  var parallaxInstance = new Parallax(scene);
}
if ($(".parallax-scene-5").length) {
  var scene = $(".parallax-scene-5").get(0);
  var parallaxInstance = new Parallax(scene);
}

//Add One Page nav
if ($(".scroll-nav").length) {
  $(".scroll-nav").onePageNav();
}

// Cart Touch Spin
if ($(".quantity-spinner").length) {
  $("input.quantity-spinner").TouchSpin({
    verticalbuttons: true,
  });
}

// Isotop Layout
function isotopeBlock() {
  if ($(".isotope-block").length) {
    var $grid = $(".isotope-block").isotope();
  }
}

isotopeBlock();

function beforeAfterTwentyTwenty() {
  if ($(".before-after-twentytwenty").length && $.fn.twentytwenty) {
    $(".before-after-twentytwenty").each(function () {
      var Self = $(this);
      var objName = Self.attr("id");
      $("#" + objName).twentytwenty();

      // hack for bs tab
      $(document).on("shown.bs.tab", 'a[data-toggle="tab"]', function (e) {
        var paneTarget = $(e.target).attr("data-target");
        var $thePane = $(".tab-pane" + paneTarget);
        var twentyTwentyContainer = "#" + objName;
        var twentyTwentyHeight = $thePane.find(twentyTwentyContainer).height();
        if (0 === twentyTwentyHeight) {
          $thePane.find(twentyTwentyContainer).trigger("resize");
        }
      });
    });
  }
}

//Fact counter
function CounterNumberChanger() {
  var timer = $(".timer");
  if (timer.length && $.fn.appear && $.fn.countTo) {
    timer.appear(function () {
      timer.countTo();
    });
  }
}

// ===Project===
function projectMasonaryLayout() {
  if ($(".masonary-layout").length) {
    $(".masonary-layout").isotope({
      layoutMode: "masonry",
    });
  }
  if ($(".post-filter").length) {
    $(".post-filter li")
      .children(".filter-text")
      .on("click", function () {
        var Self = $(this);
        var selector = Self.parent().attr("data-filter");
        $(".post-filter li").removeClass("active");
        Self.parent().addClass("active");
        $(".filter-layout").isotope({
          filter: selector,
          animationOptions: {
            duration: 500,
            easing: "linear",
            queue: false,
          },
        });
        return false;
      });
  }

  if ($(".post-filter.has-dynamic-filters-counter").length) {
    // var allItem = $('.single-filter-item').length;
    var activeFilterItem = $(".post-filter.has-dynamic-filters-counter").find(
      "li",
    );
    activeFilterItem.each(function () {
      var filterElement = $(this).data("filter");
      var count = $(".filter-layout").find(filterElement).length;
      $(this)
        .children(".filter-text")
        .append('<span class="count">' + count + "</span>");
    });
  }
}

// ===Image Hover Script===
function onHoverthreeDmovement() {
  var tiltBlock = $(".js-tilt");
  if (tiltBlock.length && $.fn.tilt) {
    $(".js-tilt").tilt({
      maxTilt: 20,
      perspective: 700,
      glare: true,
      maxGlare: 0,
    });
  }
}

// Tab Box
function tabBox() {
  if ($(".tabs-box").length) {
    $(".tabs-box .tab-buttons .tab-btn").on("click", function (e) {
      e.preventDefault();
      var target = $($(this).attr("data-tab"));

      if ($(target).is(":visible")) {
        return false;
      } else {
        target
          .parents(".tabs-box")
          .find(".tab-buttons")
          .find(".tab-btn")
          .removeClass("active-btn");
        $(this).addClass("active-btn");
        target
          .parents(".tabs-box")
          .find(".tabs-content")
          .find(".tab")
          .fadeOut(0);
        target
          .parents(".tabs-box")
          .find(".tabs-content")
          .find(".tab")
          .removeClass("active-tab");
        $(target).fadeIn(300);
        $(target).addClass("active-tab");
      }
    });
  }
}

// page direction
function directionswitch() {
  if ($(".page_direction").length) {
    $(".direction_switch button").on("click", function () {
      $(".boxed_wrapper").toggleClass(function () {
        return $(this).is(".rtl, .ltr") ? "rtl ltr" : "rtl";
      });
    });
  }
}

// Color switcher
function swithcerMenu() {
  if ($(".switch_menu").length) {
    $(".switch_btn button").on("click", function () {
      $(".switch_menu").toggle(500);
    });

    $("#styleOptions").styleSwitcher({
      hasPreview: true,
      fullPath: "assets/css/color/",
      cookie: {
        expires: 30,
        isManagingLoad: true,
      },
    });
  }
}

// Date picker
function datepicker() {
  if ($("#datepicker").length && $.fn.datepicker) {
    $("#datepicker").datepicker();
  }
}

// Price Filter
function priceFilter() {
  if ($(".price-ranger").length && $.fn.slider) {
    $(".price-ranger #slider-range").slider({
      range: true,
      min: 10,
      max: 200,
      values: [11, 99],
      slide: function (event, ui) {
        $(".price-ranger .ranger-min-max-block .min").val("$" + ui.values[0]);
        $(".price-ranger .ranger-min-max-block .max").val("$" + ui.values[1]);
      },
    });
    $(".price-ranger .ranger-min-max-block .min").val(
      "$" + $(".price-ranger #slider-range").slider("values", 0),
    );
    $(".price-ranger .ranger-min-max-block .max").val(
      "$" + $(".price-ranger #slider-range").slider("values", 1),
    );
  }
}

function bannerSlider() {
  if ($(".banner-slider").length > 0) {
    var bannerSlider = new Swiper(".banner-slider", {
      spaceBetween: 0,
      slidesPerView: 1,
      mousewheel: true,
      height: 640,
      grabCursor: true,
      loop: true,
      speed: 1400,
      autoplay: {
        delay: 5000,
      },
      pagination: {
        el: ".swiper-pagination",
        type: "progressbar",
      },
      navigation: {
        nextEl: ".banner-slider-button-next",
        prevEl: ".banner-slider-button-prev",
      },
    });
    bannerSlider.on("slideChange", function () {
      var csli = bannerSlider.realIndex + 1,
        curnum = $("#current");
      TweenMax.to(curnum, 0.2, {
        force3D: true,
        y: -10,
        opacity: 0,
        ease: Power2.easeOut,
        onComplete: function () {
          TweenMax.to(curnum, 0.1, {
            force3D: true,
            y: 10,
          });
          curnum.html("0" + csli);
        },
      });
      TweenMax.to(curnum, 0.2, {
        force3D: true,
        y: 0,
        delay: 0.3,
        opacity: 1,
        ease: Power2.easeOut,
      });
    });

    var totalSlides = bannerSlider.slides.length - 2;
    $("#total").html("0" + totalSlides);
  }
}

//===Language switcher===
function languageSwitcher() {
  if ($("#polyglot-language-options").length && $.fn.polyglotLanguageSwitcher) {
    $("#polyglotLanguageSwitcher").polyglotLanguageSwitcher({
      effect: "slide",
      animSpeed: 500,
      testMode: true,
      onChange: function (evt) {
        alert("The selected language is: " + evt.selectedItem);
      },
    });
  }
}

/////////////////////////////
//Universal Code for All Owl Carousel Sliders
/////////////////////////////

if ($(".theme_carousel").length) {
  $(".theme_carousel").each(function (index) {
    var $owlAttr = {},
      $extraAttr = $(this).data("options");
    $.extend($owlAttr, $extraAttr);

    $(this).owlCarousel($owlAttr);
  });
}

// Main Slider Carousel
if ($(".banner-carousel").length) {
  $(".banner-carousel").owlCarousel({
    animateOut: "fadeOut",
    animateIn: "fadeIn",
    loop: true,
    margin: 0,
    dots: true,
    nav: true,
    singleItem: true,
    smartSpeed: 500,
    autoplay: true,
    autoplayTimeout: 9000,
    navText: [
      '<span class="icon-right-arrow"></span>',
      '<span class="icon-right-arrow right"></span>',
    ],
    responsive: {
      0: {
        items: 1,
      },
      600: {
        items: 1,
      },
      1024: {
        items: 1,
      },
    },
  });
}

//  Partner Carousel
if ($(".partner-carousel").length) {
  $(".partner-carousel").owlCarousel({
    loop: true,
    margin: 20,
    dots: false,
    nav: false,
    stagePadding: 0,
    singleItem: true,
    smartSpeed: 500,
    autoplay: true,
    autoplayTimeout: 6000,
    navText: [
      '<span class="flaticon-next left"></span>',
      '<span class="flaticon-next right"></span>',
    ],
    responsive: {
      0: {
        items: 1,
      },
      500: {
        items: 2,
      },
      768: {
        items: 2,
      },
      992: {
        items: 3,
      },
      1200: {
        items: 4,
      },
      1300: {
        items: 4,
      },
    },
  });
}

//  Partner Carousel
if ($(".partner-carousel-2").length) {
  $(".partner-carousel-2").owlCarousel({
    loop: true,
    margin: 20,
    dots: false,
    nav: false,
    stagePadding: 0,
    singleItem: true,
    smartSpeed: 500,
    autoplay: true,
    autoplayTimeout: 6000,
    navText: [
      '<span class="flaticon-next left"></span>',
      '<span class="flaticon-next right"></span>',
    ],
    responsive: {
      0: {
        items: 1,
      },
      500: {
        items: 1,
      },
      768: {
        items: 3,
      },
      992: {
        items: 4,
      },
      1200: {
        items: 5,
      },
      1300: {
        items: 5,
      },
    },
  });
}

// Team Carousel
if ($(".team-carousel").length) {
  $(".team-carousel").owlCarousel({
    loop: true,
    margin: 30,
    dots: false,
    nav: true,
    stagePadding: 0,
    singleItem: true,
    smartSpeed: 500,
    autoplay: true,
    autoplayTimeout: 6000,
    navText: [
      '<span class="flaticon-down-arrow-2 left"></span>',
      '<span class="flaticon-down-arrow-2 right"></span>',
    ],
    responsive: {
      0: {
        items: 1,
      },
      768: {
        items: 1,
      },
      850: {
        items: 1,
      },
      991: {
        items: 1,
      },
      1300: {
        items: 1,
      },
      1700: {
        items: 1,
      },
    },
  });
}

// Vegas Slider
if ($(".slider-bg-slide").length && $.fn.vegas) {
  $(".slider-bg-slide").each(function () {
    var Self = $(this);
    var bgSlideOptions = Self.data("options");
    var bannerTwoSlides = Self.vegas(bgSlideOptions);
  });
}

//Accordion Box
if ($(".accordion-box").length) {
  $(".accordion-box").on("click", ".acc-btn", function () {
    var outerBox = $(this).parents(".accordion-box");
    var target = $(this).parents(".accordion");

    if ($(this).hasClass("active") !== true) {
      $(outerBox).find(".accordion .acc-btn").removeClass("active");
    }

    if ($(this).next(".acc-content").is(":visible")) {
      return false;
    } else {
      $(this).addClass("active");
      $(outerBox).children(".accordion").removeClass("active-block");
      $(outerBox).find(".accordion").children(".acc-content").slideUp(300);
      target.addClass("active-block");
      $(this).next(".acc-content").slideDown(300);
    }
  });
}

if ($(".dial").length && $.fn.appear && $.fn.knob) {
  $(".dial").appear(
    function () {
      var elm = $(this);
      var color = elm.attr("data-fgColor");
      var perc = elm.attr("value");
      elm.knob({
        value: 0,
        min: 0,
        max: 100,
        skin: "tron",
        readOnly: true,
        thickness: 0.05,
        dynamicDraw: true,
        displayInput: false,
      });
      $({
        value: 0,
      }).animate(
        {
          value: perc,
        },
        {
          duration: 2000,
          easing: "swing",
          progress: function () {
            elm.val(Math.ceil(this.value)).trigger("change");
          },
        },
      );
      $(this).append(function () {});
    },
    {
      accY: 20,
    },
  );
}

//Progress Bar / Levels
if ($(".progress-levels .progress-box .bar-fill").length) {
  $(".progress-box .bar-fill").each(
    function () {
      $(".progress-box .bar-fill").appear(function () {
        var progressWidth = $(this).attr("data-percent");
        $(this).css("width", progressWidth + "%");
      });
    },
    {
      accY: 0,
    },
  );
}

//Progress Bar / Levels Style2
if ($(".progress-levels-style2 .progress-box-style2 .bar-fill").length) {
  $(".progress-box-style2 .bar-fill").each(
    function () {
      $(".progress-box-style2 .bar-fill").appear(function () {
        var progressWidth = $(this).attr("data-percent");
        $(this).css("width", progressWidth + "%");
      });
    },
    {
      accY: 0,
    },
  );
}

//Fact Counter + Text Count
if ($(".count-box").length) {
  $(".count-box").appear(
    function () {
      var $t = $(this),
        n = $t.find(".count-text").attr("data-stop"),
        r = parseInt($t.find(".count-text").attr("data-speed"), 10);

      if (!$t.hasClass("counted")) {
        $t.addClass("counted");
        $({
          countNum: $t.find(".count-text").text(),
        }).animate(
          {
            countNum: n,
          },
          {
            duration: r,
            easing: "linear",
            step: function () {
              $t.find(".count-text").text(Math.floor(this.countNum));
            },
            complete: function () {
              $t.find(".count-text").text(this.countNum);
            },
          },
        );
      }
    },
    {
      accY: 0,
    },
  );
}

//====== Magnific Popup
if ($.fn.magnificPopup && $(".video-popup").length) if ($.fn.magnificPopup && $(".video-popup").length) $(".video-popup").magnificPopup({
  type: "iframe",
  // other options
});

//LightBox / Fancybox
if ($(".lightbox-image").length) {
  $(".lightbox-image").fancybox({
    openEffect: "fade",
    closeEffect: "fade",

    youtube: {
      controls: 0,
      showinfo: 0,
    },

    helpers: {
      media: {},
    },
  });
}

if ($(".paroller").length) {
  if ($.fn.paroller) if ($.fn.paroller) $(".paroller").paroller({
    factor: -0.1, // multiplier for scrolling speed and offset, +- values for direction control
    factorLg: -0.1, // multiplier for scrolling speed and offset if window width is less than 1200px, +- values for direction control
    type: "foreground", // background, foreground
    direction: "vertical", // vertical, horizontal
  });
}

if ($(".paroller-2").length) {
  if ($.fn.paroller) if ($.fn.paroller) $(".paroller-2").paroller({
    factor: 0.05, // multiplier for scrolling speed and offset, +- values for direction control
    factorLg: 0.05, // multiplier for scrolling speed and offset if window width is less than 1200px, +- values for direction control
    type: "foreground", // background, foreground
    direction: "horizontal", // vertical, horizontal
  });
}

//Contact Form Validation & Submission
if ($("#contact-form").length) {
  $("#contact-form").on("submit", function (e) {
    e.preventDefault();
    var $form = $(this);
    var name = $.trim($form.find('[name="form_name"]').val());
    var email = $.trim($form.find('[name="form_email"]').val());
    var message = $.trim($form.find('[name="form_message"]').val());
    var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    $("#form-result").remove();
    if (!name || !email || !emailRe.test(email) || !message) {
      $form.find('button[type="submit"]').before(
        '<div id="form-result" class="alert alert-danger" role="alert" style="margin-bottom:15px;">Please fill in your name, a valid email address, and your message.</div>'
      );
      return;
    }

    $form[0].reset();
    $form.find('button[type="submit"]').before(
      '<div id="form-result" class="alert alert-success" role="alert" style="margin-bottom:15px;">Thank you, ' +
        $("<div>").text(name).html() +
        '! Your message has been received by Kijitonyama Youth Choir. We will get back to you shortly.</div>'
    );
  });
}

//Search Popup
if ($("#search-popup").length) {
  //Show Popup
  $(".search-toggler").on("click", function () {
    $("#search-popup").addClass("popup-visible");
  });
  $(document).keydown(function (e) {
    if (e.keyCode === 27) {
      $("#search-popup").removeClass("popup-visible");
    }
  });
  //Hide Popup
  $(".close-search,.search-popup .overlay-layer").on("click", function () {
    $("#search-popup").removeClass("popup-visible");
  });
}

if ($.fn.niceSelect) { if ($.fn.niceSelect) { $("select:not(.ignore)").niceSelect(); } }

// Dom Ready Function
jQuery(document).on("ready", function () {
  (function ($) {
    // add your functions
    tabBox();
    directionswitch();
    swithcerMenu();
    CounterNumberChanger();
    onHoverthreeDmovement();
    datepicker();
    beforeAfterTwentyTwenty();
    priceFilter();
    languageSwitcher();
  })(jQuery);
});

// Instance Of Fuction while Window Load event
jQuery(window).on("load", function () {
  (function ($) {
    projectMasonaryLayout();
    bannerSlider();
    isotopeBlock();
  })(jQuery);
});

if ($.fn.enllax) { if ($.fn.enllax) { $(window).enllax(); } }
