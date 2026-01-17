import BaseElement from "../../core/BaseElement.js";
import { setAttributes } from "../../core/utils.js";

class AccordionPanel extends BaseElement {
    constructor() {
        super();
        this._active = false;

        setAttributes(this, {
            "role": "region",
            "aria-hidden": true,
            "slot": "panel"
        })

        this.shadowRoot.innerHTML = `<slot></slot>`;
    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/accordion/accordion-panel.css"
        ]);

        const root = this.closest("accordion-root");

        const duration = root?.getAttribute("duration");
        const easing = root?.getAttribute("easing");
        const orientation = root?.getAttribute("orientation");

        this.shadowRoot.innerHTML = `
        <ui-transition
            name="collapse"
            ${duration ? `duration="${duration}"` : ""}
            ${easing ? `easing="${easing}"` : ""}
            ${orientation ? `orientation="${orientation}"` : ""}
        >
            <div class="panel-inner" part="panel-inner">
                <slot></slot>
            </div>
        </ui-transition>
    `;

        this._transition = this.shadowRoot.querySelector("ui-transition");

        await customElements.whenDefined("ui-transition");
        await this._transition.updateComplete?.();

        if (this._active) this._transition.show();
        else this._transition.hide();
    }


    setActive(active) {
        this._active = active;
        this.setAttribute("aria-hidden", String(!active));

        if (!this._transition) return;

        if (active) this._transition.show();
        else this._transition.hide();

    }
}
customElements.define("accordion-panel", AccordionPanel);