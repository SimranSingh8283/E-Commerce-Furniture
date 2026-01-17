import BaseElement from "../../core/BaseElement.js";

class AccordionRoot extends BaseElement {
    constructor() {
        super();
        this.shadowRoot.innerHTML = `<slot></slot>`;
    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/accordion/accordion-root.css"
        ]);

        await this.waitForComponents("accordion-item", "accordion-trigger", "accordion-panel");
        this.init();
    }

    init() {
        this.items = [...this.querySelectorAll("accordion-item")];

        // Initial state from value
        const valueAttr = this.getAttribute("value");

        if (this.hasAttribute("multiple")) {
            // value="0,2,3"
            const openIndexes = valueAttr
                ? valueAttr.split(",").map(v => Number(v.trim()))
                : [];

            this.items.forEach((item, i) => {
                item.setActive(openIndexes.includes(i));
            });
        } else {
            // value="0"
            const index = Number(valueAttr);
            this.activateSingle(isNaN(index) ? -1 : index);
        }

        this.addEventListener("accordion-change", e => {
            const index = e.detail.index;

            if (this.hasAttribute("multiple")) {
                this.toggleMultiple(index);
            } else {
                this.toggleSingle(index);
            }

            this.syncValue();
        });
    }

    toggleSingle(index) {
        const current = this.value;
        const next = current === index ? -1 : index;
        this.activateSingle(next);
    }

    activateSingle(index) {
        this.items.forEach((item, i) => {
            item.setActive(i === index);
        });
    }

    toggleMultiple(index) {
        const item = this.items[index];
        if (!item) return;

        const isOpen = item.getAttribute("state") === "open";
        item.setActive(!isOpen);
    }

    syncValue() {
        if (this.hasAttribute("multiple")) {
            const open = this.items
                .map((item, i) =>
                    item.getAttribute("state") === "open" ? i : null
                )
                .filter(i => i !== null);

            this.setAttribute("value", open.join(","));
        } else {
            const index = this.items.findIndex(
                item => item.getAttribute("state") === "open"
            );

            this.setAttribute("value", index);
        }
    }

    get value() {
        return this.hasAttribute("multiple")
            ? this.getAttribute("value")
            : Number(this.getAttribute("value"));
    }
}

customElements.define("accordion-root", AccordionRoot);