import {Controller} from '@hotwired/stimulus';
import {getComponent} from '@symfony/ux-live-component';

export default class extends Controller {

    static targets = ['content', 'title', 'body', 'footer']

    // Modal Elements
    content = this.contentTarget;
    title = this.titleTarget;
    body = this.bodyTarget;
    footer = this.footerTarget;

    // Components
    report

    async initialize() {
        super.initialize();
        this.report = document.getElementById('report-component');
        this.component = await getComponent(this.report);

        this.body = this.component;
    }

    async openReportForm(event) {
        console.log(event.params.postid)
        this.component.emit('setPostToReportForm', [
            'post_id', event.params.postid
        ]);
    }

}
