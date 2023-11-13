import {Controller} from '@hotwired/stimulus';
import Toastify from 'toastify-js';
import {getComponent} from '@symfony/ux-live-component';

export default class extends Controller {

    static targets = ['form', 'files', 'token', 'newPostButton']
    input;
    searchPostByQuery;

    async initialize() {
        super.initialize();
        this.input = document.getElementById('live-post-search');
        this.searchPostByQuery = document.getElementById('post-search-by-query-component');
        this.component = await getComponent(this.searchPostByQuery);
        this.newPostButtonTarget.addEventListener('click', () => {
            this.filesTarget.click()
        })
    }

    async submit(event) {
        event.preventDefault();
        const files = this.filesTarget;
        const token = this.tokenTarget;

        try {
            for (const file of files.files) {
                let data = new FormData();
                data.append(files.name, file);
                data.append(token.name, token)
                try {
                    await this.post(event.params.username, data)
                } catch (error) {
                    console.error(`Error uploading file ${file.name}:`, error);
                }
            }
            this.component.emit('postAdded');
            await this.confirm(event.params.username, files.files);
        } catch (error) {
            console.error(`can't get the files:`, error);
        }
        this.filesTarget.value = null;
    }

    async post(username, data) {
        await fetch(`/post/upload/${username}`, {
            method: 'POST',
            body: data,
        }).then(response => response.json())
            .then(success => this.showToast(success))
            .catch(error => this.showToast(error));
    }

    async confirm(username, files) {
        await fetch(`/post/upload/${username}/confirm/${files.length}`, {
            method: 'GET',
        }).then(response => response.json())
            .then(success => this.showToast(success))
            .catch(error => this.showToast(error));
    }

    showToast(response) {
        for (const toast of response) {
            Toastify({
                text: toast.message,
                className: toast.type + ' rounded-pill px-4 fw-bold',
                gravity: "bottom", // `top` or `bottom`
                position: "right", // `left`, `center` or `right`
                stopOnFocus: true, // Prevents dismissing of toast on hover
                duration: 10000
            }).showToast();
        }
    }
}
