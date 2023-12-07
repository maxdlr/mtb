import {Controller} from '@hotwired/stimulus';
import {getComponent} from '@symfony/ux-live-component';

export default class extends Controller {

    static targets = ['modal', 'title', 'body', 'footer']

    // Modal Elements
    modal = this.modalTarget;
    title = this.titleTarget;
    body = this.bodyTarget;
    footer = this.footerTarget;

    // Components
    report

    async initialize() {
        super.initialize();
        this.report = document.querySelector("[data-live-name-value=\"ReportComponent\"]")
        this.reportComponent = await getComponent(this.report);

        this.listenToModalClosure();
    }

    async openReportForm(event) {
        const postId = event.params.postid;
        const postPrompt = event.params.prompt;
        const postOwner = event.params.owner;

        await this.reportComponent.emit('setPostToReportForm', {
            'post_id': postId
        });
        await this.reportComponent.render();

        this.createTitle(
            `"${postPrompt}" de ${postOwner}`,
            'Signalement'
        );

        this.body.innerHTML = this.reportComponent.element.innerHTML;
        this.createInfoFooter(
            "Ton signalement sera traité aussi rapidement que possible. " +
            "En cas d'urgence absolue, nous sommes aussi joignables par Discord."
        )

    }

    createInfoFooter(string) {
        const p = document.createElement('p');
        p.classList.add('fst-italic');
        p.classList.add('fw-lighter');
        p.classList.add('mb-0');
        p.classList.add('p-3');
        p.classList.add('pt-0');
        p.innerText = string;

        this.footer.append(p);
    }

    createTitle(title, subtitle) {
        const h1 = this.title.firstElementChild;

        h1.innerText = title;

        if (subtitle) {
            const p = document.createElement('p');

            p.classList.add('fst-italic');
            p.classList.add('fw-lighter');
            p.classList.add('d-block');
            p.classList.add('mb-0');
            p.classList.add('align-middle');
            p.classList.add('px-3');
            p.innerText = ` -- ${subtitle} --`;

            h1.after(p);
        }
    }

    listenToModalClosure() {
        this.modal.addEventListener('hidden.bs.modal', event => {
            console.log('modal closed')

            this.title.firstElementChild.innerText = '';
            for (const e of this.title.getElementsByTagName('p')) {
                e.remove();
            }
            this.body.innerHTML = '';
            this.footer.innerHTML = '';
        })


    }
}
