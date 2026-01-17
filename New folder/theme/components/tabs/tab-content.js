import BaseElement from "../../core/BaseElement.js";

class TabContent extends BaseElement {

    constructor() {
        super();
        this.setAttribute("role", "presentation");
        this.setAttribute("slot", "content");

        this.shadowRoot.innerHTML = `
            <slot></slot>
        `;
    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/tabs/tab-content.css"
        ]);
    }
}

customElements.define("tab-content", TabContent);