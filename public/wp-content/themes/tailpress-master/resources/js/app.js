// Navigation toggle
window.addEventListener('load', function () {
      let main_navigation = document.querySelector('.primary-menu-container');
      document.querySelector('#primary-menu-toggle').addEventListener('click', function (e) {
            e.preventDefault()
            main_navigation.classList.toggle('active')
            document.body.classList.toggle('toggle-active')
      });

      // Header serach toggle
      document.querySelector('.search-toggle').addEventListener('click', function (e) {
            e.preventDefault()
            e.target.closest('.search-wrapper').classList.toggle('active')
            document.body.classList.toggle('search-active')
      });

      // Home: Products Slider
      const swiper1 = new Swiper('section.products .swiper', {
            slidesPerView: 1,
            spaceBetween: 32,

            breakpoints: {
                  768: {
                        slidesPerView: 2,
                        spaceBetween: 32,
                  },
                  960: {
                        slidesPerView: 3,
                        spaceBetween: 32,
                  }
            },
            pagination: {
                  el: 'section.products .swiper-pagination',
                  clickable: true,
            },

            navigation: {
                  nextEl: 'section.products .swiper-button-next',
                  prevEl: 'section.products .swiper-button-prev',
            },
      });

      // Home: Industries Slider
      const swiper2 = new Swiper('section.industries .swiper', {
            slidesPerView: 1,
            spaceBetween: 16,

            breakpoints: {
                  768: {
                        slidesPerView: 2,
                        spaceBetween: 16,
                  },
                  960: {
                        slidesPerView: 3,
                        spaceBetween: 16,
                  },
                  1280: {
                        slidesPerView: 'auto',
                        spaceBetween: 16,
                  },
            },
            pagination: {
                  el: 'section.industries .swiper-pagination',
                  clickable: true,
            },

            navigation: {
                  nextEl: 'section.industries .swiper-button-next',
                  prevEl: 'section.industries .swiper-button-prev',
            },
      });

      //Home: Contatc section paroller.js
      jQuery("[data-paroller-factor]").paroller();
});
