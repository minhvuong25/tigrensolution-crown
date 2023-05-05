/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    'jquery/ui',
    'manificPopup',
    'magiccart/slick',
    'magiccart/wow',
    'domReady!'
], function ($, keyboardHandler) {
    'use strict';

    new WOW().init();

    if($('#report-section').length > 0){


        $('#slider2').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: true,
            dots: false,
            fade: true,
            infinite: true,
            asNavFor: '#slider1'
        });

        $('#slider1').slick({
            slidesToShow: 6,
            slidesToScroll: 1,
            arrows: false,
            asNavFor: '#slider2',
            dots: false,
            centerMode: false,
            focusOnSelect: true,
            infinite: true,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        centerMode: true,
                        slidesToShow: 1
                    }
                }
            ]
        });

    }

    if($('.screenshot-section').length > 0){
        $('.screenshot-section').magnificPopup({
            delegate: 'a',
            type: 'image',
            tLoading: 'Loading image #%curr%...',
            mainClass: 'mfp-img-mobile',
            image: {
                tError: '<a href="%url%">The image #%curr%</a> could not be loaded.'
            }
        });

        $('.screenshot-section .slider.center').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            centerMode: true,
            centerPadding: '90px',
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        centerMode: false,
                        arrows: false,
                    }
                }
            ]
        });

        $('.slider.reports').slick({
            dots: true,
            arrows: true,
            infinite: false,
            slidesToShow: 1,
            slidesToScroll: 1,
            customPaging : function(slider, i) {
                var title = $(slider.$slides[i]).find('[data-title]').data('title');
                return '<span class="pager__item"> '+title+' </span>';
            },
        });

    }

    if($('.package-section').length > 0){
        $( ".package-head > div" ).click(function() {
            var tabData = $(this).attr("data-toggle");
            $( ".package-head > div" ).removeClass('active');
            $( ".package-section .list-option li > div" ).removeClass('active');
            $('.'+tabData).addClass('active');
            $(this).addClass('active');
        });

        var stickyNavTop = $('.package-head').offset().top;
        var scrollTo = $('.screenshot-section').offset().top;
        var stickyNav = function () {
            var scrollTop = $(window).scrollTop();
            if (scrollTop > ( stickyNavTop + 88 ) && scrollTop < (scrollTo - 88)) {
                $('.package-head').addClass('sticky');
                $('.package-section .content-box').addClass('sticky');
            } else {
                $('.package-head').removeClass('sticky');
                $('.package-section .content-box').removeClass('sticky');
            }
        };
        stickyNav();
        $(window).scroll(function () {
            stickyNav();
        });
    }

    if($('.compare-cms-page .features-table').length > 0){
        let tableEl = $('.features-table');
        let stickyTableHead = tableEl.offset().top;
        let endSticky = tableEl.offset().top +  tableEl.outerHeight(true);
        let stickyTable = function () {
            let scrollTop = $(window).scrollTop();
            if (scrollTop > ( stickyTableHead - 160) && scrollTop < (endSticky - 248)) {
                tableEl.addClass('sticky');
            } else {
                tableEl.removeClass('sticky');
            }
            console.log(scrollTop);
        };
        console.log(stickyTableHead);
        stickyTable();
        $(window).scroll(function () {
            stickyTable();
        });
    }


    if($('.mobile-fixed-menu').length > 0){
        var stickyNavMobileTop = $('.header').offset().top;
        var headerHeight = $('.header').outerHeight();
        var stickyNavMobile = function () {
            var scrollTop = $(window).scrollTop();
            if (scrollTop > ( stickyNavMobileTop + headerHeight)){
                $('.mobile-fixed-menu').addClass('sticky');
                $('.sections.nav-sections').addClass('sticky');
            } else {
                $('.mobile-fixed-menu').removeClass('sticky');
                $('.sections.nav-sections').removeClass('sticky');
            }
        };
        stickyNavMobile();
        $(window).scroll(function () {
            stickyNavMobile();
        });
    }


    if($('#product-options-wrapper').length > 0){
        $('#product-options-wrapper .fieldset > .field > .label').append( '<i class="icon-option">?</i>' );
        $( ".icon-option" ).click(function() {
            $('html, body').animate({
                scrollTop: ($(".product.info.detailed").offset().top - 94)
            }, 'slow');
            $('.product.info.detailed .data.item.title a[data-href="#options.tab"]').trigger('click');
        });

        $('#product-addtocart-button').click(function() {
            setTimeout(function() {
                $( "#product-options-wrapper .fieldset > .field.required" ).each(function() {
                    if($(this).has('.mage-error')){
                        if(!$(this).find('.control').hasClass('focus')){
                            $(this).find('.control').addClass('focus');
                        }
                        if(!$(this).find('.mage-error').is(":visible")){
                            $(this).find('.control').removeClass('focus');
                        }
                    }
                });
            }, 100);
        });

    }

    if($('.navbar-side').length > 0){
        var scrollLink = $('.navbar-side .scroll');

        scrollLink.click(function(e) {
            e.preventDefault();
            if(this.hash === '#package-section' || this.hash === '#choose-us-section'){
                $('body,html').animate({
                    scrollTop: $(this.hash).offset().top
                }, 1000 );
            }
            else {
                $('body,html').animate({
                    scrollTop: ( $(this.hash).offset().top - 93 )
                }, 1000 );
            }
        });
        $(window).scroll(function() {
            var scrollbarLocation = $(this).scrollTop();

            scrollLink.each(function() {
                var sectionOffset = 0;
                if(this.hash === '#package-section' || this.hash === '#choose-us-section'){
                    sectionOffset = $(this.hash).offset().top - 20 ;
                }
                else{
                    sectionOffset = $(this.hash).offset().top - 113;
                }

                if ( sectionOffset < scrollbarLocation ) {
                    $(this).parent().addClass('active');
                    $(this).parent().siblings().removeClass('active');
                }
            })

        })
    }

    keyboardHandler.apply();
});
