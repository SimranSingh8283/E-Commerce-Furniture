import BaseElement from "../../core/BaseElement.js";
import { setAttributes } from "../../core/utils.js";

class AccordionTrigger extends BaseElement {
    constructor() {
        super();

        this.shadowRoot.innerHTML = `
            <ui-button tabindex="-1">
                <slot></slot>
            </ui-button>
        `;

        setAttributes(this, {
            "role": "button",
            "tabindex": 0,
            "aria-expanded": "false",
            "slot": "trigger"
        })

        this.emit = this.emit.bind(this);
        this.onKey = this.onKey.bind(this);
    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/accordion/accordion-trigger.css"
        ]);

        this.addEventListener("click", this.emit);
        this.addEventListener("keydown", this.onKey);

        this.btn = this.shadowRoot.querySelector("ui-button");
    }

    emit() {
        const items = [...this.closest("accordion-root")?.querySelectorAll("accordion-item") || []];
        const item = this.closest("accordion-item");

        this.dispatchEvent(new CustomEvent("accordion-change", {
            bubbles: true,
            detail: { index: items.indexOf(item) }
        }));
    }

    setActive(active) {
        this.setAttribute("aria-expanded", String(active));

        if (active) {
            this.btn?.classList.add("Button-active")
        } else {
            this.btn?.classList.remove("Button-active")
        }
    }

    onKey(e) {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            this.emit();
        }
    }
}
customElements.define("accordion-trigger", AccordionTrigger);