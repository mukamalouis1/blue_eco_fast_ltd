/**
 * BLUE ECO FAST - Main JavaScript
 * jQuery + AJAX powered interactions
 */

$(document).ready(function () {

  // ── Navbar scroll effect ──────────────────────────────
  $(window).on('scroll', function () {
    if ($(this).scrollTop() > 60) {
      $('.navbar-bef').addClass('scrolled');
      $('#backToTop').addClass('visible');
    } else {
      $('.navbar-bef').removeClass('scrolled');
      $('#backToTop').removeClass('visible');
    }
  });

  // ── Back to top ───────────────────────────────────────
  $('#backToTop').on('click', function () {
    $('html, body').animate({ scrollTop: 0 }, 600, 'swing');
  });

  // ── Smooth scroll for nav links ───────────────────────
  $(document).on('click', 'a[href^="#"]', function (e) {
    var target = $(this.getAttribute('href'));
    if (target.length) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: target.offset().top - 75 }, 600);
      // close mobile nav if open
      $('.navbar-collapse').collapse('hide');
    }
  });

  // ── Active nav highlighting on scroll ─────────────────
  $(window).on('scroll', function () {
    var scrollPos = $(this).scrollTop() + 100;
    $('section[id]').each(function () {
      var sectionTop = $(this).offset().top;
      var sectionId = $(this).attr('id');
      if (scrollPos >= sectionTop && scrollPos < sectionTop + $(this).outerHeight()) {
        $('.navbar-bef .nav-link').removeClass('active');
        $('.navbar-bef .nav-link[href="#' + sectionId + '"]').addClass('active');
      }
    });
  });

  // ── Car filter buttons ────────────────────────────────
  $(document).on('click', '.filter-btn', function () {
    $('.filter-btn').removeClass('active');
    $(this).addClass('active');
    var cat = $(this).data('filter');
    if (cat === 'all') {
      $('.car-card').parent().show();
    } else {
      $('.car-card').parent().hide();
      $('.car-card[data-cat="' + cat + '"]').parent().show();
    }
  });

  // ── Star rating ───────────────────────────────────────
  var ratingVal = 0;
  var ratingLabels = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];

  $(document).on('click', '.rating-stars .star', function () {
    ratingVal = parseInt($(this).data('val'));
    $('#ratingInput').val(ratingVal);
    updateStars(ratingVal);
    $('#starFeedback').text(ratingLabels[ratingVal]);
  });

  $(document).on('mouseenter', '.rating-stars .star', function () {
    updateStars(parseInt($(this).data('val')));
  });

  $(document).on('mouseleave', '.rating-stars', function () {
    updateStars(ratingVal);
  });

  function updateStars(val) {
    $('.rating-stars .star').each(function () {
      if (parseInt($(this).data('val')) <= val) {
        $(this).addClass('active');
      } else {
        $(this).removeClass('active');
      }
    });
  }

  // ── "Enquire about this car" quick-fill ───────────────
  $(document).on('click', '.btn-car-enquire', function () {
    var carName = $(this).data('car');
    $('html, body').animate({ scrollTop: $('#enquiry').offset().top - 80 }, 600, function () {
      // tick checkbox for this car
      $('.car-checkbox-item input').prop('checked', false);
      $('.car-checkbox-item').each(function () {
        if ($(this).find('input').val() === carName) {
          $(this).find('input').prop('checked', true);
        }
      });
    });
  });

  // ── Enquiry Form AJAX Submit ──────────────────────────
  $('#enquiryForm').on('submit', function (e) {
    e.preventDefault();

    if (ratingVal === 0) {
      showAlert('warning', '⭐ Please rate your satisfaction before submitting.');
      return;
    }

    var checkedCars = [];
    $('.car-checkbox-item input:checked').each(function () {
      checkedCars.push($(this).val());
    });
    if (checkedCars.length === 0) {
      showAlert('warning', '🚗 Please select at least one preferred car.');
      return;
    }

    var $btn = $('#submitBtn');
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Sending...');

    var formData = $(this).serialize() + '&preferred_cars=' + encodeURIComponent(checkedCars.join(', ')) + '&rating=' + ratingVal;

    $.ajax({
      url: 'ajax/send_enquiry.php',
      method: 'POST',
      data: formData,
      dataType: 'json',
      success: function (res) {
        $btn.prop('disabled', false).html('🚀 Send My Car Enquiry');
        if (res.success) {
          showAlert('success', '✅ ' + res.message);
          $('#enquiryForm')[0].reset();
          ratingVal = 0;
          updateStars(0);
          $('#starFeedback').text('');
          $('#ratingInput').val(0);
        } else {
          showAlert('danger', '❌ ' + res.message);
        }
      },
      error: function (xhr) {
        $btn.prop('disabled', false).html('🚀 Send My Car Enquiry');
        var msg = 'Server error. Please try again.';
        try {
          var r = JSON.parse(xhr.responseText);
          if (r.message) msg = r.message;
        } catch (ex) {}
        showAlert('danger', '❌ ' + msg);
      }
    });
  });

  function showAlert(type, msg) {
    var $alert = $('#formAlert');
    $alert.attr('class', 'alert alert-' + type + ' alert-result').html(msg).fadeIn(300);
    $('html, body').animate({ scrollTop: $alert.offset().top - 120 }, 400);
    setTimeout(function () { $alert.fadeOut(400); }, 6000);
  }

  // ── Animate elements on scroll ────────────────────────
  function revealOnScroll() {
    $('.fade-in-up:not(.revealed)').each(function () {
      var elemTop = $(this).offset().top;
      if (elemTop < $(window).scrollTop() + $(window).height() - 60) {
        $(this).addClass('revealed');
      }
    });
  }
  $(window).on('scroll', revealOnScroll);
  revealOnScroll();

  // ── Counter animation ─────────────────────────────────
  function animateCounters() {
    $('.stat-num[data-count]').each(function () {
      var $el = $(this);
      if ($el.hasClass('counted')) return;
      var target = parseInt($el.data('count'));
      var suffix = $el.data('suffix') || '';
      $el.addClass('counted');
      $({ n: 0 }).animate({ n: target }, {
        duration: 1800,
        easing: 'swing',
        step: function () { $el.text(Math.ceil(this.n) + suffix); },
        complete: function () { $el.text(target + suffix); }
      });
    });
  }
  // trigger counter when hero is visible
  $(window).on('scroll', function () {
    if ($('.hero-stats').length) {
      var top = $('.hero-stats').offset().top;
      if ($(this).scrollTop() + $(this).height() > top) {
        animateCounters();
      }
    }
  });
  animateCounters(); // trigger immediately if in view

});
