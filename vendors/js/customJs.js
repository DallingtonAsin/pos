$(function () {
  'use strict'

  $('.az-sidebar .with-sub').on('click', function (e) {
    e.preventDefault();
    $(this).parent().toggleClass('show');
    $(this).parent().siblings().removeClass('show');
  });

  $(document).on('click touchstart', function (e) {
    e.stopPropagation();

    // closing of sidebar menu when clicking outside of it
    if (!$(e.target).closest('.az-header-menu-icon').length) {
      var sidebarTarg = $(e.target).closest('.az-sidebar').length;
      if (!sidebarTarg) {
        $('body').removeClass('az-sidebar-show');
      }
    }
  });


  $('#azSidebarToggle').on('click', function (e) {
    e.preventDefault();

    if (window.matchMedia('(min-width: 992px)').matches) {
      $('.az-sidebar').toggle();
    } else {
      $('body').toggleClass('az-sidebar-show');
    }
  });

});