import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    input;

    initialize() {
        super.initialize();
        this.input = document.getElementById('live-post-search');
    }

    filter(event) {
        this.input.value = event.params.promptname;
        window.location
    }
}
