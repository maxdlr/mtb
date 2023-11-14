import {Controller} from '@hotwired/stimulus';
import Toastify from 'toastify-js';
import {getComponent} from '@symfony/ux-live-component';

export default class extends Controller {

    static targets = ['form', 'files', 'token', 'newPostButton']
    input;
    searchPostByQuery;
    uploaded;

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
        this.uploaded = 0;

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
            this.component.emit('updatePosts');
            await this.confirm(event.params.username, this.uploaded);
        } catch (error) {
            console.error(`can't get the files:`, error);
        }
        this.filesTarget.value = null;
    }

    async post(username, data) {
        await fetch(`/post/upload/${username}`, {
            method: 'POST',
            body: data,
        }).then(response => {
            if (response.status === 403) {
                response.json().then(success =>
                        this.showToast(success),
                    this.uploaded += 0
                )
            } else if (response.status === 200) {
                response.json().then(success =>
                        this.showToast(success),
                    this.uploaded += 1
                )
            }
        })
            .catch(error => this.showToast(error));
    }

    async confirm(username, uploaded) {
        await fetch(`/post/upload/${username}/confirm/${uploaded}`, {
            method: 'GET',
        }).then(response => response.json())
            .then(success => this.showToast(success))
            .catch(error => this.showToast(error));
    }

    async deleteOnePost(event) {
        console.log(event.params.token)
        event.preventDefault();

        fetch(`/delete/post/${event.params.id}/${event.params.username}/${event.params.token}`, {
            method: 'POST',
            body: event.params.token
        }).then(response => response.json()
            .then(success => this.showToast(success))
        )
            .catch(error => this.showToast(error));
        this.component.emit('updatePosts');
    }

    async deleteAllPosts(event) {
        console.log(event.params.token)
        event.preventDefault();

        fetch(`/delete/posts/${event.params.username}/${event.params.token}`, {
            method: 'POST',
            body: event.params.token
        }).then(response => response.json()
            .then(success => this.showToast(success))
        )
            .catch(error => this.showToast(error));
        this.component.emit('updatePosts');
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
