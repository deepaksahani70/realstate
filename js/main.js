$(document).ready(function () {

    /* ---------- Property Categories Slider ---------- */

    function initCategoriesSlider() {
        const $slider = $('.re-categories-slider');
        const $progressBar = $('.re-category-progress-bar');

        if (!$slider.length || typeof $.fn.slick !== 'function') {
            return;
        }

        function updateCategoryProgress(event, slick, currentSlide) {
            if (!slick) {
                if (!$slider.hasClass('slick-initialized')) return;
                slick = $slider.slick('getSlick');
            }

            const current = currentSlide !== undefined
                ? currentSlide
                : slick.currentSlide;

            const maxSlide = Math.max(
                0,
                slick.slideCount - slick.options.slidesToShow
            );

            const progress = maxSlide === 0
                ? 100
                : (current / maxSlide) * 100;

            $progressBar.css('width', progress + '%');
        }

        if (!$slider.hasClass('slick-initialized')) {

            // Register events before initializing Slick.
            $slider.on(
                'init reInit afterChange breakpoint',
                updateCategoryProgress
            );

            $slider.slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: false,
                speed: 450,
                arrows: true,
                dots: false,
                autoplay: false,
                swipeToSlide: true,
                touchThreshold: 8,

                prevArrow: $('.re-category-prev'),
                nextArrow: $('.re-category-next'),

                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        }

        updateCategoryProgress();
    }


    /* ---------- Testimonials Slider ---------- */

    function initTestimonialsSlider() {
        const $slider = $('.testimonials-slider');
        const $progressBar = $('.testimonial-progress-bar');

        if (!$slider.length || typeof $.fn.slick !== 'function') {
            return;
        }

        function updateTestimonialProgress(event, slick, currentSlide) {
            if (!slick) {
                if (!$slider.hasClass('slick-initialized')) return;
                slick = $slider.slick('getSlick');
            }

            const current = currentSlide !== undefined
                ? currentSlide
                : slick.currentSlide;

            const maxSlide = Math.max(
                0,
                slick.slideCount - slick.options.slidesToShow
            );

            const progress = maxSlide === 0
                ? 100
                : (current / maxSlide) * 100;

            $progressBar.css('width', progress + '%');
        }

        if (!$slider.hasClass('slick-initialized')) {

            // Register events before initializing Slick.
            $slider.on(
                'init reInit afterChange breakpoint',
                updateTestimonialProgress
            );

            $slider.slick({
                slidesToShow: 3,
                slidesToScroll: 1,
                infinite: false,
                speed: 450,
                arrows: true,
                dots: false,
                autoplay: false,
                swipeToSlide: true,
                touchThreshold: 8,

                prevArrow: $('.testimonial-prev'),
                nextArrow: $('.testimonial-next'),

                responsive: [
                    {
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 576,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        }

        updateTestimonialProgress();
    }


    /* ---------- Initialize Sliders ---------- */

    initCategoriesSlider();
    initTestimonialsSlider();

});
