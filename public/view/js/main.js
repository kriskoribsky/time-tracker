// Main namespace
// ==========================================================================
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

    // Carousel
    // ==========================================================================
    // functions
    function carouselShift(element, slider, sliderShift, prevBtn, nextBtn, dots, cards) {
        // get current shift value
        const currentShift = parseFloat(slider.style.transform.match(/-?[0-9\.]+/g)[0]);

        // get currently selected card
        const oldCard = slider.getElementsByClassName(activeClass)[0];
        const oldCardIndex = [...cards].indexOf(oldCard);

        if (element === prevBtn) {
            // slider.style.transform = "translateX(" + (currentShift + sliderShift) + "%)";
            var newCardIndex = oldCardIndex - 1;

        } else if (element === nextBtn) {
            // slider.style.transform = "translateX(" + (currentShift - sliderShift) + "%)";
            var newCardIndex = oldCardIndex + 1;

        } else {
            var newCardIndex = [...dots].indexOf(element);
        }

        const newCard = cards[newCardIndex];

        // shift slider
        const shift = (oldCardIndex - newCardIndex) * sliderShift;

        slider.style.transform = "translateX(" + (currentShift + shift) + "%)";

        oldCard.classList.remove(activeClass);
        newCard.classList.add(activeClass);

        dots[oldCardIndex].classList.remove(activeClass);
        dots[newCardIndex].classList.add(activeClass);

        if (!newCard.nextElementSibling) {
            nextBtn.style.display = 'none';
        } else {
            nextBtn.style.display = 'flex';
        }

        if (!newCard.previousElementSibling) {
            prevBtn.style.display = 'none';
        } else {
            prevBtn.style.display = 'flex';
        }
    }

    // initialize all document carousels
    const targetClass = 'carousel';
    const viewportClass = 'carousel-viewport';
    const sliderClass = 'carousel-slider'
    const cardsClass = 'carousel-card';
    const nextBtnClass = 'carousel-next-btn';
    const prevBtnClass = 'carousel-previous-btn';
    const dotsClass = 'dot';
    const animationClass = 'carousel-animation';
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
        const nextBtn = carousel.getElementsByClassName(nextBtnClass)[0];
        const prevBtn = carousel.getElementsByClassName(prevBtnClass)[0];
        const dots = carousel.getElementsByClassName(dotsClass);

        // get all carousel child cards
        const cards = carousel.getElementsByClassName(cardsClass);

        // hide navigation buttons at start
        nextBtn.style.display = 'none';
        prevBtn.style.display = 'none';

        if (cards) {
            // carousel-specific parameters
            const parentWidth = carousel.offsetWidth;
            const cardWidth = cards[0].offsetWidth;
            const cardGap = cardWidth * (relativedGap / 100)

            // relative card shift in %
            const CardShift = (cardWidth + cardGap) / parentWidth * 100;
            let i = 0;

            // card default absolute positions
            Array.from(cards).forEach((card) => {
                card.style.position = 'absolute';
                card.style.left = i * CardShift + '%';
                i++;
            })
            
            // slider shift in %
            const sliderShift = (cardWidth + cardGap) / parentWidth * 100;
            const initialShift = (parentWidth / 2 - cardWidth / 2) / parentWidth * 100;
            slider.style.transform = "translateX(" + initialShift + "%)";

            // initial control elements setup
            setTimeout(() => {
                slider.classList.add(animationClass);
            }, 1);

            cards[0].classList.add(activeClass);
            dots[0].classList.add(activeClass);
            if (cards.length > 1) {
                nextBtn.style.display = 'flex';
            }

            // bind shift actions to control buttons & dots
            [prevBtn, nextBtn, ...dots].forEach(element => {
                element.addEventListener('click', event => {
                    event.preventDefault();
                    carouselShift(element, slider, sliderShift, prevBtn, nextBtn, dots, cards);
                })
            })
        }
    })


    // AJAX call to php to query DB for project group primary color value, then change :root primary color & gradient color custom properties
    
}