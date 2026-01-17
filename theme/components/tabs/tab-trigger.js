import BaseElement from "../../core/BaseElement";

/**
 * @class TabTrigger
 * @extends BaseElement
 *
 * Presentational tab trigger.
 * - No event logic
 * - No state management
 * - Controlled entirely by <tabs-root>
 */
class TabTrigger extends BaseElement {

    /**
     * Lifecycle hook
     */
    connectedCallback() {
        super.connectedCallback();

        this.setAttribute("role", "tab");
        this.setAttribute("tabindex", "-1");
        this.setAttribute("aria-selected", "false");
        this.classList.add("Button-root");
    }

    /**
     * Sets active state (called by TabsRoot)
     * @param {boolean} active
     */
    setActive(active) {
        this.setBoolAttr("active", active);
        this.setAttribute("aria-selected", String(active));
        this.setAttribute("tabindex", active ? "0" : "-1");
    }
}

customElements.define("tab-trigger", TabTrigger);