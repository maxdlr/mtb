import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['followed', 'follower', 'button'];

    initialize() {
        super.initialize();
    }

    async followUser() {

        const followerId = this.followerTarget.value;
        const followedId = this.followedTarget.value;

        await fetch(`/follow/${followerId}/${followedId}`, {method: "POST"})
            .then(response => response.json())
            .then(json => {
                console.log(json.message)
            })


            .then(console.log('je suis apres le fetch'))
    }

}