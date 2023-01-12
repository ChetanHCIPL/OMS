(function ($) {
    'use strict';
  

    /*=====================================
    	03. Mainmenu Activation
    =========================================*/


    $('nav.mobilemenu__nav').meanmenu({
        meanMenuClose: 'X',
        meanMenuCloseSize: '18px',
        meanScreenWidth: '991',
        meanExpandableChildren: true,
        meanMenuContainer: '.mobile-menu',
        onePage: true
    });


    /*==================================== 
    	10. Headroom For Sticky Header
    ======================================*/

    $('.headroom--sticky').headroom();


    /*================================ 
    	11. Search Popup
    ==================================*/

    var $html = $('html'),
        $demoOption = $('.demo-option-container'),
        $body = $('body');

    function searchClose() {
        $body.removeClass('page-search-popup-opened'), $html.css({
            overflow: ""
        })
    }


    $('.btn-search-click').on("click", function (e) {
        e.preventDefault(),
            function () {
                $body.addClass('page-search-popup-opened'), $html.css({
                    overflow: "hidden"
                });
                var e = $('.brook-search-popup').find("form input[type='search']");
                setTimeout(function () {
                    e.focus()
                }, 500)
            }()
    });


    $('.search-close').on('click', function (e) {
        e.preventDefault();
        searchClose();
    });

    $('.brook-search-popup').on('click', function (e) {
        e.target === this && searchClose();
    });

    /* ===================================
    	12. Sidebar Mobile Menu  Active
    =====================================*/

    function menuClose() {
        $body.removeClass('popup-mobile-menu-wrapper'), $html.css({
            overflow: ""
        })
    };

    $('.popup-mobile-click').on('click', function (e) {
        e.preventDefault(),
            function () {
                $body.addClass('popup-mobile-menu-wrapper'), $html.css({
                    overflow: "hidden"
                });
            }()
    });
    

    $('.mobile-close').on('click', function (e) {
        e.preventDefault();
        menuClose();
    });
    $('.popup-mobile-visiable').on('click', function (e) {
        e.target === this && menuClose();
    });


    /* =============================
    	15. Sidebar Mobile Menu 
    ================================*/

    $('.object-custom-menu > li.has-mega-menu > a').on('click', function (e) {
        e.preventDefault();
        $(this).siblings('.object-submenu').slideToggle('400');
        $(this).toggleClass('active').siblings('.object-submenu').toggleClass('is-visiable');
    })

    /* =====================
    	16. Hamberger Menu 
    =========================*/

    $('.hamberger-trigger').on('click', function (e) {
        e.preventDefault();
        $('.open-hamberger-wrapper').addClass('is-visiable');
    });

    $('.page-close').on('click', function (e) {
        e.preventDefault();
        $('.open-hamberger-wrapper').removeClass('is-visiable');
    });

})(jQuery);