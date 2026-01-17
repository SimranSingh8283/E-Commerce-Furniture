import BaseElement from "../../core/BaseElement.js";

class TabList extends BaseElement {
    constructor() {
        super();

        this.setAttribute("role", "tablist");
        this.setAttribute("slot", "list");

        this.shadowRoot.innerHTML = `
            <button class="Button-root scroll-btn left" part="scroll-left">
                <iconify-icon icon="line-md:chevron-left"></iconify-icon>
            </button>

            <div class="scroll-container" part="scroll-container">
                <slot></slot>
                <div class="tab-indicator" part="indicator"></div>
            </div>

            <button class="Button-root scroll-btn right" part="scroll-right">
                <iconify-icon icon="line-md:chevron-right"></iconify-icon>
            </button>
        `;
    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/tabs/tab-list.css"
        ]);

        this.setAttribute("role", "tablist");

        this.scrollContainer = this.shadowRoot.querySelector(".scroll-container");
        this.leftBtn = this.shadowRoot.querySelector(".scroll-btn.left");
        this.rightBtn = this.shadowRoot.querySelector(".scroll-btn.right");
        this.indicator = this.shadowRoot.querySelector(".tab-indicator");
        this.slotEl = this.shadowRoot.querySelector("slot");

        this.onEvent(this.leftBtn, "click", () => this.scroll(-150));
        this.onEvent(this.rightBtn, "click", () => this.scroll(150));

        this.onEvent(this, "tab-change", e => {
            const tabs = [...this.querySelectorAll("tab-trigger")];
            const active = tabs[e.detail.index];
            if (active) this.moveIndicator(active);
            this.updateScrollButtons();
        });

        this.onEvent(this.slotEl, "slotchange", () => this.syncFromSlot());
        this.onEvent(this.scrollContainer, "scroll", () => this.updateScrollButtons());
        this.onEvent(window, "resize", () => this.updateScrollButtons());

        requestAnimationFrame(() => this.syncFromSlot());
    }

    syncFromSlot() {
        const tabs = this.slotEl.assignedElements({ flatten: true });
        const active = tabs.find(t => t.getAttribute("aria-selected") === "true");

        if (active) this.moveIndicator(active, false);
        this.updateScrollButtons();
    }

    scroll(amount) {
        this.scrollContainer.scrollBy({
            left: amount,
            behavior: "smooth"
        });
    }

    updateScrollButtons() {
        if (!this.scrollContainer) return;

        const { scrollLeft, scrollWidth, clientWidth } = this.scrollContainer;
        const EPS = 2;

        const atStart = scrollLeft <= EPS;
        const atEnd = scrollLeft + clientWidth >= scrollWidth - EPS;

        this.leftBtn.style.display = atStart ? "none" : "grid";
        this.rightBtn.style.display = atEnd ? "none" : "grid";
    }

    moveIndicator(tab, animate = true) {
        const tabRect = tab.getBoundingClientRect();
        const containerRect = this.scrollContainer.getBoundingClientRect();

        const left =
            tabRect.left -
            containerRect.left +
            this.scrollContainer.scrollLeft;

        const width = tabRect.width;

        if (!animate) this.indicator.style.transition = "none";

        this.indicator.style.transform = `translateX(${ left }px)`;
        this.indicator.style.width = `${width}px`;

        if (!animate) {
            requestAnimationFrame(() => {
                this.indicator.style.transition = "";
            });
        }

        const overLeft = left < this.scrollContainer.scrollLeft;
        const overRight =
            left + width >
            this.scrollContainer.scrollLeft + this.scrollContainer.clientWidth;

        if (overLeft) {
            this.scrollContainer.scrollTo({ left, behavior: "smooth" });
        } else if (overRight) {
            this.scrollContainer.scrollTo({
                left: left + width - this.scrollContainer.clientWidth,
                behavior: "smooth"
            });
        }
    }
}

customElements.define("tab-list", TabList);