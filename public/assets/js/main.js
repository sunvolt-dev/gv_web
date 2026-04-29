// 사이트 공통 JS

document.addEventListener('DOMContentLoaded', () => {
  // 상품 상세 Swiper 자동 초기화
  if (document.querySelector('.product-swiper')) {
    new Swiper('.product-swiper', {
      loop: false,
      navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
      pagination: { el: '.swiper-pagination', clickable: true },
      thumbs: document.querySelector('.product-thumbs') ? {
        swiper: new Swiper('.product-thumbs', {
          slidesPerView: 5,
          spaceBetween: 8,
          watchSlidesProgress: true,
        }),
      } : undefined,
    });
  }
});
