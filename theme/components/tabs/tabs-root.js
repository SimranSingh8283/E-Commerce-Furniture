import BaseElement from "../../core/BaseElement";


/**
 * @class TabsRoot
 * @extends BaseElement
 *
 * Root container for a tabs component.
 * Responsibilities:
 * - Manages tab activation state
 * - Handles keyboard navigation
 * - Emits `tab-change` custom events
 *
 * Expected children:
 * - <tab-trigger>
 * - <tab-panel>
 *
 * Attributes:
 * - value {number} : active tab index
 *
 * Events:
 * - tab-change { index: number }
 */
class TabsRoot extends BaseElement {

    /**
     * Lifecycle hook when element is connected to the DOM
     * Waits for dependent custom elements before initializing
     */
    async connectedCallback() {
        await this.waitForComponents("tab-trigger", "tab-panel");
        super.connectedCallback();
        this.init();
    }

    /**
     * Initializes tabs, panels, listeners, and initial state
     * @private
     */
    init() {
        this.tabs = [...this.querySelectorAll("tab-trigger")];
        this.panels = [...this.querySelectorAll("tab-panel")];

        const value = this.numAttr("value", 0);
        this.activate(value);

        this.tabs.forEach((tab, index) => {
            this.onEvent(tab, "click", () => this.activate(index));
            this.onEvent(tab, "keydown", (e) => this.onKey(e, index));
        });
    }

    /**
     * Handles keyboard navigation for tabs
     * @param {KeyboardEvent} e
     * @param {number} index - Index of the focused tab
     * @private
     */
    onKey(e, index) {
        const len = this.tabs.length;
        let newIndex = index;

        if (e.key === "ArrowRight") newIndex = (index + 1) % len;
        if (e.key === "ArrowLeft") newIndex = (index - 1 + len) % len;
        if (e.key === "Enter" || e.key === " ") return this.activate(index);

        this.tabs[newIndex]?.focus();
    }

    /**
     * Activates a tab and its corresponding panel
     * Emits a `tab-change` event after state update
     *
     * @param {number} index - Index to activate
     */
    activate(index) {
        this.tabs.forEach((tab, i) => tab.setActive(i === index));
        this.panels.forEach((panel, i) => panel.setActive(i === index));

        this.emit("tab-change", { index });
        this.setAttribute("value", index);
    }
}

customElements.define("tabs-root", TabsRoot);