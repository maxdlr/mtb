import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    input;

    initialize() {
        super.initialize();
        this.input = document.getElementById('live-post-search');
    }

    filter(event) {
        this.input.setAttribute('data-value', event.params.promptname);
        this.reload(this.input);
    }

    type() {
        this.input.setAttribute('data-value', this.input.value);
        this.reload(this.input);
    }

    reload(e) {
        e.dispatchEvent(new Event('change', {bubbles: true}))
    }
}
