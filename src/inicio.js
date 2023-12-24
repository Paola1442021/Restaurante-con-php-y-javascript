document.addEventListener('DOMContentLoaded', function() {
    const carousel = document.querySelector('.carousel');
    const images = document.querySelectorAll('.carousel img');
    const prevButton = document.querySelector('.prev');
    const nextButton = document.querySelector('.next');
  
    let currentIndex = 0;
  
    nextButton.addEventListener('click', function() {
      showImage(currentIndex + 1);
    });
  
    prevButton.addEventListener('click', function() {
      showImage(currentIndex - 1);
    });
  
    function showImage(index) {
      if (index < 0) {
        index = images.length - 1;
      } else if (index >= images.length) {
        index = 0;
      }
  
      currentIndex = index;
      const translateValue = -index * 100 + '%';
      carousel.style.transform = 'translateX(' + translateValue + ')';
    }
  });
  