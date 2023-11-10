import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['form', 'files', 'token']

    async submit(event) {
        event.preventDefault();
        const formData = new FormData(this.formTarget)
        const files = this.filesTarget;
        const token = this.tokenTarget
        // console.log(this.formTarget)

        for (const file of files.files) {
            let data = new FormData();
            // console.log(data)
            data.append(files.name, file);
            data.append(files.name, token.value)
            // console.log(data)
            await this.post(event, data)
        }

        // const files = formData.getAll('post[posts][]');
        // console.log(files);

        // for (const file of files) {
        //     console.log(file);
        //     await this.post(event, fileData)
        // }


    }

    async post(event, data) {
        await fetch(`/post/upload/${event.params.username}`, {
            method: 'POST',
            headers: {
                'Content-type': data.type,
                'Content-length': data.size
            },
            body: data, // This is your file object
        }).then(
            response => response.json() // if the response is a JSON object
        ).then(
            success => console.log(success) // Handle the success response object
        ).catch(
            error => console.log(error) // Handle the error response object
        );
    }
}
