document.addEventListener('DOMContentLoaded', function() {
    const containerImagem = document.querySelector('.container-imagem');
    const containerArrows = document.querySelector('.container-arrows');
    const nextButton = document.getElementById('nextCarouselCardapio');
    const prevButton = document.getElementById('prevCarouselCardapio');
    const images = document.querySelectorAll('.container-imagem img');
    const totalImages = images.length;
    const carouselItems = document.querySelectorAll('#carouselItems li');

    let currentIndex = 0;

    nextButton.addEventListener('click', () => {
        if (currentIndex < totalImages - 1) {
            currentIndex++;
            updateCarousel();
        }
    });

    prevButton.addEventListener('click', () => {
        if (currentIndex > 0) {
            currentIndex--;
            updateCarousel();
        }
    });

    function updateCarousel() {
        const translateXValue = -100 * currentIndex;
        containerImagem.style.transform = `translateX(${translateXValue}%)`;
        containerArrows.style.left = `${100 * currentIndex}%`;

        carouselItems.forEach((item, index) => {
            item.classList.toggle('active', index === currentIndex);
        });
    }

    updateCarousel();
});