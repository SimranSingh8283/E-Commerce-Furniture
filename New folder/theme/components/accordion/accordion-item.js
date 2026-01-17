import BaseElement from "../../core/BaseElement.js";

class AccordionItem extends BaseElement {
    constructor() {
        super();
        this._active = false;

        this.shadowRoot.innerHTML = `
            <slot name="trigger"></slot>
            <slot name="panel"></slot>
        `;

    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/accordion/accordion-item.css"
        ]);

        this.trigger = this.querySelector("accordion-trigger");
        this.panel = this.querySelector("accordion-panel");

        if (!this.trigger || !this.panel) return;

        AccordionItem._idCounter ||= 0;
        const id = AccordionItem._idCounter++;

        this.trigger.id ||= `acc-trigger-${id}`;
        this.panel.id ||= `acc-panel-${id}`;

        this.trigger.setAttribute("aria-controls", this.panel.id);
        this.panel.setAttribute("aria-labelledby", this.trigger.id);

        if (!this.hasAttribute("state")) {
            this.setAttribute("state", "closed");
        }

        this.trigger.setActive(this._active);
        this.panel.setActive(this._active);
    }

    setActive(active) {
        this._active = active;

        const state = active ? "open" : "closed";
        this.setAttribute("state", state);

        this.trigger?.setActive(active);
        this.panel?.setActive(active);
    }
}

customElements.define("accordion-item", AccordionItem);