import {Controller} from '@hotwired/stimulus';
import Toastify from 'toastify-js';

export default class extends Controller {
    flashes;

    connect() {
        this.flashes = document.getElementsByClassName('toastifyFlash');

        if (this.flashes != null) {
            for (const flash of this.flashes) {
                Toastify({
                    text: flash.getAttribute('data-message'),
                    className: flash.getAttribute('data-type') + ' rounded-pill px-4 fw-bold',
                    gravity: "bottom", // `top` or `bottom`
                    position: "right", // `left`, `center` or `right`
                    stopOnFocus: true, // Prevents dismissing of toast on hover
                    duration: 10000
                }).showToast();
            }
        }
    }
}
