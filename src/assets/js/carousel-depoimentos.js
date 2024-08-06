document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('input[name="carousel-depoimentos"]');
    const depoimentos = document.querySelector('.depoimentos');
    
    function updateTransform() {
        radios.forEach(radio => {
            radio.addEventListener('click', function() {
                radios.forEach(r => r.classList.remove('active')); 
                this.classList.add('active'); 
                
                const value = parseInt(this.value, 10);
                let translateX;

                if (window.innerWidth >= 1000) {
                    translateX = -33.3 * value;
                } else if (window.innerWidth <= 999 && window.innerWidth > 768) {
                    translateX = -50 * value;
                } else {
                    translateX = -100 * value;
                }

                depoimentos.style.transform = `translateX(${translateX}%)`;
            });
        });
    }

    window.addEventListener('resize', updateTransform);
    updateTransform();
});