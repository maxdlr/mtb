import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['followed', 'follower', 'buttonIcon', 'button'];

    initialize() {
        for (const buttonIcon of this.buttonIconTargets) {

        }
    }

    async followUser() {

        const followerId = this.followerTarget.value;
        const followedId = this.followedTarget.value;

        await fetch(`/follow/${followerId}/${followedId}`)
            .then(response => response.json())
            .then(json => {
                console.log(json.message)
            })
            .then(console.log('je suis apres le fetch'))
    }

}