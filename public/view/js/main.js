// main namespace
{
    // list of components and their  classes in css
    const components = {
        'modal': 'modal-show'
    }

    // itarate over all page modals and add close event listeners
    Array.from(document.getElementsByClassName('modal')).forEach(modal => {

        // find all buttons inside modal that are supposed to close it & bind closing action to them
        const closeBtns = modal.querySelectorAll('a[data-dismiss="modal"], button[data-dismiss="modal"]').forEach(btn => {
            btn.onclick = () => {
                modal.classList.remove(components['modal']);
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        });

        // add event listener on modal in case user clicks anywhere not on modal-dialog, which would hide the modal
        modal.addEventListener('click', event => {
            if (event.target === modal) {
                modal.classList.remove(components['modal']);
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 300);
            }
        })    
    
    })

    // get all elements with data-target attribute and bind them to their targer
    document.querySelectorAll('a[data-toggle][data-target], button[data-toggle][data-target]').forEach(element => {
        const targetElement = document.getElementById(element.getAttribute('data-target').substring(1));
        // class to add to a target element
        const targetClass = components[element.getAttribute('data-toggle')];

        if (targetElement && targetClass) {
            // add button/a event listener
            element.addEventListener('click', event => {
                event.preventDefault();

                targetElement.style.display = 'block';
                setTimeout(() => {
                    targetElement.classList.add(targetClass);
                }, 1);
                console.log('click');
            })
        }
    })

    // carousel
    const targetClass = 'carousel';
    const viewportClass = 'carousel-viewport';
    const sliderClass = 'carousel-slider'
    const cardsClass = 'carousel-card';
    // relative gap between cards (in %)
    const relativedGap = 5;
    
    // dynamicaly generated classnames
    let activeClass = 'is-selected';

    // get all carousels on page
    const carousels = document.getElementsByClassName(targetClass);

    Array.from(carousels).forEach((carousel) => {
        // carousel utility elements
        const viewport = carousel.getElementsByClassName(viewportClass)[0];
        const slider = carousel.getElementsByClassName(sliderClass)[0];

        // get all carousel child cards
        const cards = carousel.getElementsByClassName(cardsClass);

        // carousel-specific parameters
        let n = cards.length;
        const parentWidth = carousel.offsetWidth;
        const cardWidth = cards[0].offsetWidth;
        const cardGap = cardWidth * (relativedGap / 100)

        // const relativeCardShift = parentWidth / (cardWidth + cardGap);
        let i = 0;

        // card default absolute positions
        Array.from(cards).forEach((card) => {
            card.style.position = 'absolute';
            card.style.left = (i * (cardWidth + cardGap))  + 'px';
            i++;
        })
        
        const currentPos = 0;

        // center slider so that first card is in the center
        slider.style.transform = "translateX(" + (parentWidth / 2 - cardWidth / 2) + "px)";

    })








    // AJAX call to php to query DB for project group primary color value, then change :root primary color & gradient color custom properties
    
}