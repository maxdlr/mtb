import {Controller} from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {

    static targets = ['output', 'thumbnail', 'nextButton', 'previousButton'];
    otherPosts = this.thumbnailTargets;
    currentPost = this.outputTarget;
    nextButton = this.nextButtonTarget;
    previousButton = this.previousButtonTarget;
    output = this.outputTarget;

    initialize() {
        super.initialize();
        for (let i = 0; i < this.otherPosts.length; i++) {
            this.otherPosts[i].id = "post-" + i;
        }
    }

    showPost({params: {postfilename}}) {
        this.output.src = postfilename;
        this.updateCurrentPostId();
        this.checkNav();
    }

    next(event) {
        event.preventDefault();

        for (let i = 0; i < this.otherPosts.length; i++) {
            if (this.currentPost.id === this.otherPosts[i].id && this.otherPosts[i + 1]) {
                this.currentPost.src = this.otherPosts[i + 1].src;
                break;
            }
        }
        this.updateCurrentPostId();
        this.checkNav();
    }


    previous(event) {
        event.preventDefault();

        for (let i = 0; i < this.otherPosts.length; i++) {
            if (this.currentPost.id === this.otherPosts[i].id && this.otherPosts[i - 1]) {
                this.currentPost.src = this.otherPosts[i - 1].src;
                break;
            }
        }
        this.updateCurrentPostId();
        this.checkNav();
    }

    updateCurrentPostId() {
        for (const otherPost of this.otherPosts) {
            if (otherPost.src === this.output.src) {
                this.output.id = otherPost.id;
            }
        }
    }

    checkNav() {
        if (this.currentPost.id === this.otherPosts[this.otherPosts.length - 1].id) {
            this.removeGalleryNavButton(this.nextButton);
        } else if (this.nextButton.classList.contains('d-none')) {
            this.addGalleryNavButton(this.nextButton)
        }

        if (this.currentPost.id === this.otherPosts[0].id) {
            this.removeGalleryNavButton(this.previousButton);
        } else if (this.previousButton.classList.contains('d-none')) {
            this.addGalleryNavButton(this.previousButton)
        }
    }

    removeGalleryNavButton(navButton) {
        navButton.classList.add('d-none');
    }

    addGalleryNavButton(navButton) {
        navButton.classList.remove('d-none');
    }

}
