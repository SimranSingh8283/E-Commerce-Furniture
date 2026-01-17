import BaseElement from "../../core/BaseElement.js";

class TabTrigger extends BaseElement {

    constructor() {
        super();
        this.setAttribute("role", "tab");
        this.setAttribute("tabindex", "-1");
        this.setAttribute("aria-selected", "false");

        this.shadowRoot.innerHTML = `
            <ui-button tabindex="-1">
                <slot></slot>
            </ui-button>
        `;
    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/tabs/tab-trigger.css"
        ]);

        this.onEvent(this, "click", () => this.emitChange());
        this.onEvent(this, "keydown", e => this.onKey(e));
    }

    setActive(active) {
        this.setAttribute("aria-selected", String(active));
        this.setAttribute("tabindex", active ? "0" : "-1");
    }

    emitChange() {
        const tabs = [...this.parentElement.querySelectorAll("tab-trigger")];
        this.emit("tab-change", { index: tabs.indexOf(this) });
    }

    onKey(e) {
        const tabs = [...this.parentElement.querySelectorAll("tab-trigger")];
        let index = tabs.indexOf(this);

        if (e.key === "ArrowRight") index = (index + 1) % tabs.length;
        if (e.key === "ArrowLeft") index = (index - 1 + tabs.length) % tabs.length;
        if (e.key === "Enter" || e.key === " ") return this.emitChange();

        tabs[index]?.focus();
    }
}

customElements.define("tab-trigger", TabTrigger);