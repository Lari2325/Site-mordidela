function showCategory(event, category, imageSrc) {
    let rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => row.classList.add('hidden'));

    let selectedRows = document.querySelectorAll(`.${category}`);
    selectedRows.forEach(row => row.classList.remove('hidden'));

    let popover = document.getElementById('popover');
    let popoverImage = document.getElementById('popover-image');
    popoverImage.src = imageSrc;

    let button = event.target;
    let rect = button.getBoundingClientRect();

    let cardapioContainer = document.querySelector('.cardapio-pg .container');
    let containerWidth = cardapioContainer.offsetWidth;

    if (category === 'hamburgueres') {
        popover.style.top = ''; 
        popover.style.left = '0'; 
    } else if (category === 'sobremesas') {
        let popoverWidth = popover.offsetWidth;
        popover.style.top = ''; 
        popover.style.left = (containerWidth / 2 - popoverWidth / 2) + 'px';
    } else if (category === 'pratos-executivos') {
        let popoverWidth = popover.offsetWidth;
        popover.style.top = ''; 
        popover.style.left = (containerWidth - popoverWidth) + 'px';
    }

    popover.style.display = 'block';

    let buttons = document.querySelectorAll('button');
    buttons.forEach(btn => btn.classList.remove('active'));

    button.classList.add('active');
}