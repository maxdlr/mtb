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

    static targets = [
        'output',
        'outputTitle',
        'outputFooter',
        'listItemImg',
        'listItem',
        'nextButton',
        'previousButton',
    ];

    listItems = this.listItemTargets;

    shownItemBody = this.outputTarget;
    shownItemTitle = this.outputTitleTarget;
    shownItemFooter = this.outputFooterTarget;
    nextButton = this.nextButtonTarget;
    previousButton = this.previousButtonTarget;

    builtItem = null;
    listItemImgs = [];


    // output = this.outputTarget;


    initialize() {
        super.initialize();

        for (const item of this.listItems) {
            for (const itemChild of item.children) {
                if (itemChild.classList.contains('gallery-card')) {
                    this.listItemImgs.push(itemChild.getElementsByClassName('post-img'));

                }
            }
        }
    }

    showPost({params: {postfilename, postid}}) {
        this.emptyModalContent();
        this.builtItem = this.buildModalBodyContent(
            'img',
            ['img-fluid', 'p-0', 'm-1', 'post-img'],
            postid,
            null,
            postfilename,
        );
        this.shownItemBody.setAttribute('data-id', postid);


        this.shownItemBody.append(this.builtItem);
        this.hideNavButton();
    }

    editPost({params: {postid}}) {
        const fetchRoute = '/post/' + postid + '/edit';

        this.fetchHtmlContent(fetchRoute, 'edit-post-title', 'edit-post-body', 'edit-post-footer');
        this.shownItemBody.setAttribute('data-id', postid);
        this.hideNavButton();
    }

    next(event) {
        event.preventDefault();
        this.navigate();
        this.hideNavButton();
    }

    previous(event) {
        event.preventDefault();
        this.navigate(false);
        this.hideNavButton();
    }

    submitForm({params: {postid}}) {
        console.log('start')
        let form = document.getElementById('editForm');
        let formData = new FormData(form);
        let xhr = new XMLHttpRequest();
        const csrfTokenField = document.getElementById('post__token')

        const csrfTokenName = csrfTokenField.getAttribute('name');
        const csrfTokenValue = csrfTokenField.getAttribute('value');

        xhr.open('POST', '/post/' + postid + '/edit', true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    // Success
                    console.log('working');
                } else {
                    // Error
                    console.log('not working');
                    console.error('Error:', xhr.statusText);
                }
            }
        };

        // Send the form data
        formData.append(csrfTokenName, csrfTokenValue);
        xhr.send(formData);

        console.log('end');
    }

    // SOLID Function ------------------------------------------------------------------------

    fetchHtmlContent(route, titleId, bodyId, footerId) {

        let loader = this.buildModalBodyContent(
            'div',
            ['spinner-border'],
            null,
            null,
            null,
            'role',
            'status'
        )
        let loaderInner = this.buildModalBodyContent(
            'span',
            ['visually-hidden'],
            null,
            'Loading...',
        )
        this.emptyModalContent();
        loader.append(loaderInner)
        this.shownItemBody.append(loader);

        fetch(route)
            .then(response => response.text())
            .then(data => {

                // Convert the HTML string into a document object
                let parser = new DOMParser();
                let doc = parser.parseFromString(data, 'text/html');

                this.emptyModalContent();
                this.shownItemTitle.append(doc.getElementById(titleId));
                this.shownItemBody.append(doc.getElementById(bodyId));
                this.shownItemFooter.append(doc.getElementById(footerId));
            })
    }

    emptyModalContent() {
        this.shownItemBody.innerHTML = '';
        this.shownItemTitle.innerHTML = '';
        this.shownItemFooter.innerHTML = '';
    }

    buildModalBodyContent(
        element,
        eClasses = null,
        eId = null,
        eText = null,
        eSrc = null,
        eAttr = null,
        eAttrValue = null
    ) {
        let e = document.createElement(element);
        e.src = eSrc;
        e.innerText = eText;
        e.setAttribute(eAttr, eAttrValue)
        for (const eClass of eClasses) {
            e.classList.add(eClass);
        }
        e.id = 'post-img-' + eId;
        e.setAttribute('data-shownItem', 'active');

        return e;
    }

    navigate(direction = true) {

        // let shownElement = this.shownItemBody.childNodes[0];
        let shownElement = this.shownItemBody;

        let d = null;

        for (let i = 0; i < this.listItems.length; i++) {
            direction === true ? d = i + 1 : d = i - 1;
            if (shownElement.getAttribute('data-id') === this.listItems[i].id && this.listItems[d]) {

                if (shownElement.childNodes[0].tagName === 'IMG') {
                    shownElement.childNodes[0].src = this.listItemImgs[d][0].src;
                    shownElement.id = this.listItemImgs[d][0].id;
                    shownElement.setAttribute('data-id', this.listItems[d].id)
                    break;
                }
                if (shownElement.childNodes[0].id === 'edit-post-body') {
                    const fetchRoute = '/post/' + this.listItems[d].id + '/edit';
                    this.fetchHtmlContent(fetchRoute, 'edit-post-title', 'edit-post-body', 'edit-post-footer');

                    shownElement.setAttribute('data-id', this.listItems[d].id)

                    break;
                }
            }
        }
    }

    hideNavButton() {
        // let shownElement = this.shownItemBody.childNodes[0];
        let shownElement = this.shownItemBody;

        if (shownElement.getAttribute('data-id') === this.listItems[this.listItems.length - 1].id) {
            this.removeGalleryNavButton(this.nextButton);
        } else if (this.nextButton.classList.contains('d-none')) {
            this.addGalleryNavButton(this.nextButton)
        }

        if (shownElement.getAttribute('data-id') === this.listItems[0].id) {
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
