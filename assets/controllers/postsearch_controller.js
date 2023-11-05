import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    input;
    closeBtn;

    initialize() {
        super.initialize();
        this.input = document.getElementById('live-post-search');
        this.closeBtn = document.getElementById('close-search');
    }

    filter(event) {
        this.input.setAttribute('data-value', event.params.promptname);
        this.showElement(this.closeBtn);
        this.reload(this.input);
    }

    type() {
        this.input.setAttribute('data-value', this.input.value);
        this.showElement(this.closeBtn);
        this.reload(this.input);
    }

    clear() {
        this.input.value = '';
        this.input.setAttribute('data-value', '');
        this.hideElement(this.closeBtn);
        this.reload(this.input);
    }

    reload(e) {
        e.dispatchEvent(new Event('change', {bubbles: true}))
    }

    showElement(e) {
        if (e.classList.contains('d-none'))
            e.classList.remove('d-none')
    }

    hideElement(e) {
        if (!e.classList.contains('d-none'))
            e.classList.add('d-none')
    }
}