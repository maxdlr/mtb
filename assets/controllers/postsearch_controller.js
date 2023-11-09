import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    input;
    closeBtn;

    initialize() {
        super.initialize();
        this.input = document.getElementById('live-post-search');
        this.closeBtn = document.getElementById('close-search');

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') this.clear();
        })
    }

    filter(event) {
        event.preventDefault();
        this.input.setAttribute('data-value', event.params.promptname);
        this.showElement(this.closeBtn);
        this.reload(this.input);
    }

    type() {
        this.input.setAttribute('data-value', this.input.value);
        this.showElement(this.closeBtn);

        if (this.input.value === '' || this.input.getAttribute('data-value') === '') {
            this.hideElement(this.closeBtn);
        }

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
        if (this.input.classList.contains('rounded-end-pill'))
            this.input.classList.remove('rounded-end-pill');
    }

    hideElement(e) {
        if (!e.classList.contains('d-none'))
            e.classList.add('d-none')
        if (!this.input.classList.contains('rounded-end-pill'))
            this.input.classList.add('rounded-end-pill');
    }
}
