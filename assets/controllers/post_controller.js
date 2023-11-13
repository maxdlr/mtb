import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['form', 'files', 'token']
    input;

    initialize() {
        super.initialize();
        this.input = document.getElementById('live-post-search');
    }

    async submit(event) {
        event.preventDefault();
        const formData = new FormData(this.formTarget)
        const files = this.filesTarget;
        const token = this.tokenTarget
        // console.log(this.formTarget)
        try {
            for (const file of files.files) {
                let data = new FormData();
                // console.log(data)
                data.append(files.name, file);
                // data.append(files.name, token.value)
                // console.log(data)
                try {
                    await this.post(event.params.username, data)
                } catch (error) {
                    console.error(`Error uploading file ${file.name}:`, error);
                }
            }

            await fetch(`/post/upload/${event.params.username}/confirm/${files.files.length}`, {
                method: 'GET',
            }).then(
                response => response.json()
            ).then(
                success => console.log(success.message) // Handle the success response object
            ).catch(
                error => console.log(error.message) // Handle the error response object
            );

            this.reload(this.input)
            location.reload();

        } catch (error) {
            console.error(`can't get the files:`, error);
        }


        // const files = formData.getAll('post[posts][]');
        // console.log(files);

        // for (const file of files) {
        //     console.log(file);
        //     await this.post(event, fileData)
        // }


    }

    async post(username, data) {
        await fetch(`/post/upload/${username}`, {
            method: 'POST',
            body: data, // This is your file object
        }).then(
            response => response.json() // if the response is a JSON object
        ).then(
            success => console.log(success.message) // Handle the success response object
        ).catch(
            error => console.log(error.message) // Handle the error response object
        );
    }

    reload(e) {
        e.dispatchEvent(new Event('change', {bubbles: true}))
    }
}
