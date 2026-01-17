import BaseElement from "../../core/BaseElement.js";

class TabPanel extends BaseElement {

    constructor() {
        super();
        this.setAttribute("role", "tabpanel");

        this._active = false;
        this._hideTimer = null;
        this._transition = null;

        this.shadowRoot.innerHTML = `
            <ui-transition name="fade">
                <slot></slot>
            </ui-transition>
        `;
    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/tabs/tab-panel.css"
        ]);

        this._transition = this.shadowRoot.querySelector("ui-transition");

        requestAnimationFrame(() => {
            this.applyState();
        });
    }

    setActive(active) {
        this._active = active;
        this.applyState();
    }

    applyState() {
        const transition = this._transition;

        if (this._hideTimer) {
            clearTimeout(this._hideTimer);
            this._hideTimer = null;
        }

        if (!transition || typeof transition.show !== "function") {
            this.style.display = this._active ? "block" : "none";
            return;
        }

        if (this._active) {
            this.style.display = "block";
            transition.show();
        } else {
            transition.hide();
            this._hideTimer = setTimeout(() => {
                this.style.display = "none";
            }, transition.duration || 300);
        }
    }
}

customElements.define("tab-panel", TabPanel);