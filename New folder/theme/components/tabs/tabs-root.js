import BaseElement from "../../core/BaseElement.js";

class TabsRoot extends BaseElement {

    constructor() {
        super();
        this.shadowRoot.innerHTML = `
            <slot name="list"></slot>
            <slot name="content"></slot>
        `;
    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/tabs/tabs-root.css"
        ]);

        await this.waitForComponents("tab-trigger", "tab-panel");

        this.tabs = [...this.querySelectorAll("tab-trigger")];
        this.panels = [...this.querySelectorAll("tab-panel")];

        const value = this.numAttr("value", 0);
        this.activate(value);

        this.onEvent(this, "tab-change", e => {
            this.activate(e.detail.index);
            this.setAttribute("value", e.detail.index);
        });
    }

    activate(index) {
        this.tabs.forEach((tab, i) => tab.setActive(i === index, i));
        this.panels.forEach((panel, i) => panel.setActive(i === index));
    }
}

customElements.define("tabs-root", TabsRoot);