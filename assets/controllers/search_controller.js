import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['promptName']

    filterPostsByPrompt() {
        for (const promptName of this.promptNameTargets) {
            if (promptName.id === promptName.innerText) {
                console.log(promptName.innerText)
                break;

            }
            // window.location = '/' + promptName.innerText;
        }
    }
}
