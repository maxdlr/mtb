import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['form']

    async submit(event) {
        event.preventDefault();

        const formData = new FormData(this.formTarget);

        await fetch(`/post/upload/${event.params.username}`, {
            method: 'POST',
            // headers: {
            // Content-Type may need to be completely **omitted**
            // or you may need something
            // "Content-Type": "You will perhaps need to define a content-type here"
            // },
            body: formData, // This is your file object
        }).then(
            response => response.json() // if the response is a JSON object
        ).then(
            success => console.log(success) // Handle the success response object
        ).catch(
            error => console.log(error) // Handle the error response object
        );
    }
}
