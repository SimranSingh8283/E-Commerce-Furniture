
class TabsRoot extends HTMLElement {
    connectedCallback() {
        Promise.all([
            customElements.whenDefined("tab-trigger"),
            customElements.whenDefined("tab-panel")
        ]).then(() => this.init());
    }

    init() {
        this.tabs = [...this.querySelectorAll("tab-trigger")];
        this.panels = [...this.querySelectorAll("tab-panel")];

        const value = Number(this.getAttribute("value") || 0);
        this.activate(value);

        this.addEventListener("tab-change", e => {
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


const tabListSheet = new CSSStyleSheet();
tabListSheet.replaceSync(`
:host {
  position: relative;
  display: flex;
  align-items: center;
  overflow: hidden;
}

.scroll-container {
  display: flex;
  overflow-x: hidden;
  scroll-behavior: smooth;
  flex: 1;
  position: relative;
}

.scroll-btn {
  display: none;
  position: absolute;
  top: 50%;
  height: 100%;
  transform: translateY(-50%);
  z-index: 10;
  display: grid;
  place-content: center;
  background: var(--clr-gray-200);
  border: none;
  cursor: pointer;
  width: 2rem;
}

.scroll-btn.left {
  left: 0;
}

.scroll-btn.right {
  right: 0;
}

.tab-indicator {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 2px;
    background: currentColor;
    transform: translateX(0);
    width: 0;
    transition:
        transform 300ms cubic-bezier(0.245,0.97,0.125,1),
        width 300ms cubic-bezier(0.245,0.97,0.125,1);
}
`);

class TabList extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: "open" });
        this.shadowRoot.adoptedStyleSheets = [tabListSheet];

        this.shadowRoot.innerHTML = `
            <button class="Button-root scroll-btn left" part="scroll-left"><iconify-icon icon="line-md:chevron-left"></iconify-icon></button>
            <div class="scroll-container" part="scroll-container">
                <slot></slot>
                <div class="tab-indicator" part="indicator"></div>
            </div>
            <button class="Button-root scroll-btn right" part="scroll-right"><iconify-icon icon="line-md:chevron-right"></iconify-icon></button>
        `;
    }

    connectedCallback() {
        this.setAttribute("role", "tablist");

        this.scrollContainer = this.shadowRoot.querySelector(".scroll-container");
        this.leftBtn = this.shadowRoot.querySelector(".scroll-btn.left");
        this.rightBtn = this.shadowRoot.querySelector(".scroll-btn.right");
        this.indicator = this.shadowRoot.querySelector(".tab-indicator");

        this.leftBtn.addEventListener("click", () => this.scroll(-150));
        this.rightBtn.addEventListener("click", () => this.scroll(150));

        this.addEventListener("tab-change", e => {
            const tabs = [...this.querySelectorAll("tab-trigger")];
            const active = tabs[e.detail.index];
            if (active) this.moveIndicator(active);
            this.updateScrollButtons();
        });

        const slot = this.shadowRoot.querySelector("slot");
        slot.addEventListener("slotchange", () => {
            const tabs = slot.assignedElements({ flatten: true });
            const active = tabs.find(t => t.getAttribute("aria-selected") === "true");
            if (active) this.moveIndicator(active, false); // scroll into view on init
            this.updateScrollButtons();
        });

        this.scrollContainer.addEventListener("scroll", () => this.updateScrollButtons());
        window.addEventListener("resize", () => this.updateScrollButtons());

        // Initial scroll and indicator setup
        requestAnimationFrame(() => {
            const active = this.querySelector('[aria-selected="true"]');
            if (active) this.moveIndicator(active, false);
            this.updateScrollButtons();
        });
    }

    scroll(amount) {
        this.scrollContainer.scrollBy({ left: amount, behavior: "smooth" });
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

        const left = tabRect.left - containerRect.left + this.scrollContainer.scrollLeft;
        const width = tabRect.width;

        if (!animate) this.indicator.style.transition = "none";

        this.indicator.style.transform = `translateX(${left}px)`;
        this.indicator.style.width = `${width}px`;

        if (!animate) {
            requestAnimationFrame(() => {
                this.indicator.style.transition = "";
            });
        }

        // Auto-scroll active tab into view
        const overLeft = left < this.scrollContainer.scrollLeft;
        const overRight = left + width > this.scrollContainer.scrollLeft + this.scrollContainer.clientWidth;

        if (overLeft) {
            this.scrollContainer.scrollTo({ left: left, behavior: "smooth" });
        } else if (overRight) {
            this.scrollContainer.scrollTo({ left: left + width - this.scrollContainer.clientWidth, behavior: "smooth" });
        }
    }
}

customElements.define("tab-list", TabList);

class TabTrigger extends HTMLElement {
    connectedCallback() {
        this.setAttribute("role", "tab");
        this.setAttribute("tabindex", "-1");
        this.setAttribute("aria-selected", "false");

        this.addEventListener("click", () => this.emit());
        this.addEventListener("keydown", e => this.onKey(e));
        this.classList.add("Button-root");
    }

    setActive(active, index) {
        this.setAttribute("aria-selected", active);
        this.setAttribute("tabindex", active ? "0" : "-1");
        // if (active) this.focus({ preventScroll: true });
    }

    emit() {
        const tabs = [...this.parentElement.querySelectorAll("tab-trigger")];
        this.dispatchEvent(new CustomEvent("tab-change", {
            bubbles: true,
            detail: { index: tabs.indexOf(this) }
        }));
    }

    onKey(e) {
        const tabs = [...this.parentElement.querySelectorAll("tab-trigger")];
        let index = tabs.indexOf(this);

        if (e.key === "ArrowRight") index = (index + 1) % tabs.length;
        if (e.key === "ArrowLeft") index = (index - 1 + tabs.length) % tabs.length;
        if (e.key === "Enter" || e.key === " ") return this.emit();

        tabs[index]?.focus();
    }
}
customElements.define("tab-trigger", TabTrigger);


class TabContent extends HTMLElement {
    connectedCallback() {
        this.setAttribute("role", "presentation");
    }
}
customElements.define("tab-content", TabContent);


class TabPanel extends HTMLElement {
    connectedCallback() {
        this.setAttribute("role", "tabpanel");
        this.style.display = "none";
        this._hideTimer = null;
    }

    setActive(active) {
        const transition = this.querySelector("ui-transition");

        if (this._hideTimer) {
            clearTimeout(this._hideTimer);
            this._hideTimer = null;
        }

        if (active) {
            this.style.display = "block";
            transition?.show();
        } else {
            transition?.hide();

            this._hideTimer = setTimeout(() => {
                this.style.display = "none";
                this._hideTimer = null;
            }, transition?.duration || 300);
        }
    }
}

customElements.define("tab-panel", TabPanel);



const transitionSheet = new CSSStyleSheet();
transitionSheet.replaceSync(`
:host {
    display: block;
    will-change: transform, opacity, height, width;
}

/* ---------- Fade ---------- */
:host([name="fade"]) {
    opacity: 0;
}
:host([name="fade"][state="enter"]) {
    opacity: 1;
}

/* ---------- Zoom ---------- */
:host([name="zoom"]) {
    opacity: 0;
    transform: scale(0.8);
}
:host([name="zoom"][state="enter"]) {
    opacity: 1;
    transform: scale(1);
}

/* ---------- Grow ---------- */
:host([name="grow"]) {
    transform: scaleY(0);
}
:host([name="grow"][state="enter"]) {
    transform: scaleY(1);
}

/* ---------- Slide ---------- */
:host([name="slide"][direction="left"]) {
    transform: translateX(-100%);
}
:host([name="slide"][direction="right"]) {
    transform: translateX(100%);
}
:host([name="slide"][direction="top"]) {
    transform: translateY(-100%);
}
:host([name="slide"][direction="bottom"]) {
    transform: translateY(100%);
}
:host([name="slide"][state="enter"]) {
    transform: translate(0);
}

/* ---------- Collapse ---------- */
:host([name="collapse"]) {
    overflow: hidden;
}
`);

class UITransition extends HTMLElement {
    static get observedAttributes() {
        return ["origin", "orientation"];
    }

    constructor() {
        super();
        this.attachShadow({ mode: "open" });
        this.shadowRoot.adoptedStyleSheets = [transitionSheet];
        this.shadowRoot.innerHTML = `<slot></slot>`;

        this._state = "exited";
        this._timer = null;
    }

    connectedCallback() {
        this.duration = Number(this.getAttribute("duration") || 300);
        this.easing =
            this.getAttribute("easing") ||
            "cubic-bezier(0.245,0.97,0.125,1)";

        this._applyOrigin();
        this._applyTransition();

        this.setAttribute("state", "exit");

        this.style.display = "none";
    }

    attributeChangedCallback(name) {
        if (name === "origin") this._applyOrigin();
        if (name === "orientation") this._applyTransition();
    }

    _applyOrigin() {
        this.style.transformOrigin =
            this.getAttribute("origin") || "";
    }

    _getOrientation() {
        return this.getAttribute("orientation") === "horizontal"
            ? "horizontal"
            : "vertical";
    }

    _getSizeProp() {
        return this._getOrientation() === "horizontal"
            ? "width"
            : "height";
    }

    _getScrollSize() {
        return this._getOrientation() === "horizontal"
            ? this.scrollWidth
            : this.scrollHeight;
    }

    _applyTransition() {
        const sizeProp = this._getSizeProp();

        this.style.transition = `
            transform ${this.duration}ms ${this.easing},
            opacity ${this.duration}ms ${this.easing},
            ${sizeProp} ${this.duration}ms ${this.easing}
        `;
    }

    _clear() {
        clearTimeout(this._timer);
        this._timer = null;
    }

    show() {
        if (this._state === "entered") return;

        this._clear();
        this._state = "entering";
        this.dispatchEvent(new CustomEvent("ui-enter"));

        this.style.display = "block";

        if (this.getAttribute("name") === "collapse") {
            const prop = this._getSizeProp();
            this.style[prop] = "0px";
            this.getBoundingClientRect();
            this.style[prop] = this._getScrollSize() + "px";
        }

        this.getBoundingClientRect();
        this.setAttribute("state", "enter");

        this._timer = setTimeout(() => {
            if (this.getAttribute("name") === "collapse") {
                this.style[this._getSizeProp()] = "auto";
            }

            this._state = "entered";
            this.dispatchEvent(new CustomEvent("ui-entered"));
        }, this.duration);
    }

    hide() {
        if (this._state === "exited") return;

        this._clear();
        this._state = "exiting";
        this.dispatchEvent(new CustomEvent("ui-exit"));

        if (this.getAttribute("name") === "collapse") {
            const prop = this._getSizeProp();
            this.style[prop] = this._getScrollSize() + "px";
            this.getBoundingClientRect();
            this.style[prop] = "0px";
        }

        this.setAttribute("state", "exit");

        this._timer = setTimeout(() => {
            this.style.display = "none";

            this._state = "exited";
            this.dispatchEvent(new CustomEvent("ui-exited"));
        }, this.duration);
    }

    toggle(force) {
        if (typeof force === "boolean") {
            force ? this.show() : this.hide();
            return;
        }

        this._state === "entered" || this._state === "entering"
            ? this.hide()
            : this.show();
    }
}

customElements.define("ui-transition", UITransition);





const popoverSheet = new CSSStyleSheet();
popoverSheet.replaceSync(`
:host {
    position: fixed;
    inset: 0;
    z-index: 1300;
    display: none;
}

:host([open]) {
    display: block;
}

/* Backdrop */

.Popover-backdrop {
    position: absolute;
    inset: 0;
}

/* Paper */
.Popover-ui {
    position: absolute;
    min-width: 160px;
    background: white;
    border-radius: 8px;
    box-shadow:
        0px 5px 5px -3px rgba(0,0,0,0.2),
        0px 8px 10px 1px rgba(0,0,0,0.14),
        0px 3px 14px 2px rgba(0,0,0,0.12);
}
`);


class UIPopover extends HTMLElement {
    static get observedAttributes() {
        return ["origin"];
    }

    constructor() {
        super();
        this.attachShadow({ mode: "open" });
        this.shadowRoot.adoptedStyleSheets = [popoverSheet];

        const transitionName = this.getAttribute("transition") || "zoom";
        const backdrop = this.getAttribute("backdrop");
        const origin = this.getAttribute("origin");

        this.shadowRoot.innerHTML = `
            <div class="Popover-backdrop"></div>
            <ui-transition class="Popover-ui" name="${transitionName}" ${origin ? `origin="${origin}"` : ""}>
                <slot></slot>
            </ui-transition>
        `;
    }

    connectedCallback() {
        if (this.parentNode !== document.body) {
            document.body.appendChild(this);
        }

        this.backdrop = this.shadowRoot.querySelector(".Popover-backdrop");
        this.paper = this.shadowRoot.querySelector(".Popover-ui");
        this.transition = this.shadowRoot.querySelector("ui-transition");

        this.anchorSelector = this.getAttribute("anchor");
        this.placement = this.getAttribute("placement") || "bottom";

        if (this.anchorSelector) {
            this.anchorEl = document.querySelector(this.anchorSelector);
            this.anchorEl?.addEventListener("click", this.toggle);
        }

        this.backdrop.addEventListener("click", this.hide);
        window.addEventListener("keydown", this._onKeyDown);
        window.addEventListener("scroll", this._onScroll);
    }

    attributeChangedCallback(name, oldVal, newVal) {
        if (name === "origin" && this.transition) {
            if (newVal) {
                this.transition.setAttribute("origin", newVal);
            } else {
                this.transition.removeAttribute("origin");
            }
        }
    }

    disconnectedCallback() {
        this.anchorEl?.removeEventListener("click", this.toggle);
        window.removeEventListener("keydown", this._onKeyDown);
        window.removeEventListener("scroll", this._onScroll);
    }

    /* ---------- STATE ---------- */

    get open() {
        return this.hasAttribute("open");
    }

    set open(val) {
        val ? this.setAttribute("open", "") : this.removeAttribute("open");
    }

    /* ---------- ACTIONS ---------- */

    show = () => {
        if (this.open) return;

        this.open = true;
        this._position();
        this.transition?.show();

        requestAnimationFrame(() => {
            this._position();
        });
    };

    hide = () => {
        if (!this.open) return;

        this.transition?.hide();

        const duration = this.transition?.duration || 200;
        setTimeout(() => {
            this.open = false;
        }, duration);
    };

    toggle = () => {
        this.open ? this.hide() : this.show();
    };

    /* ---------- POSITION ---------- */

    _position() {
        if (!this.anchorEl) return;

        const paper = this.paper;
        const a = this.anchorEl.getBoundingClientRect();
        const gap = 8;

        const vw = window.innerWidth;
        const vh = window.innerHeight;

        const pw = paper.offsetWidth;
        const ph = paper.offsetHeight;

        let placement = this.placement;

        const compute = (p) => {
            switch (p) {
                case "top":
                    return {
                        top: a.top - ph - gap,
                        left: a.left
                    };
                case "bottom":
                    return {
                        top: a.bottom + gap,
                        left: a.left
                    };
                case "right":
                    return {
                        top: a.top,
                        left: a.right + gap
                    };
                case "left":
                    return {
                        top: a.top,
                        left: a.left - pw - gap
                    };
            }
        };

        let { top, left } = compute(placement);

        /* ---------- FLIP ---------- */

        const overTop = top < 0;
        const overBottom = top + ph > vh;
        const overLeft = left < 0;
        const overRight = left + pw > vw;

        if (placement === "bottom" && overBottom && !overTop) {
            placement = "top";
            ({ top, left } = compute(placement));
        } else if (placement === "top" && overTop && !overBottom) {
            placement = "bottom";
            ({ top, left } = compute(placement));
        } else if (placement === "right" && overRight && !overLeft) {
            placement = "left";
            ({ top, left } = compute(placement));
        } else if (placement === "left" && overLeft && !overRight) {
            placement = "right";
            ({ top, left } = compute(placement));
        }

        /* ---------- SHIFT (CLAMP) ---------- */

        top = Math.min(Math.max(top, gap), vh - ph - gap);
        left = Math.min(Math.max(left, gap), vw - pw - gap);

        paper.style.top = `${Math.round(top)}px`;
        paper.style.left = `${Math.round(left)}px`;
    }

    _onKeyDown = (e) => {
        if (e.key === "Escape") this.hide();
    };

    _onScroll = () => {
        this.hide();
    };
}

customElements.define("ui-popover", UIPopover);







/*
// multiple
<accordion-root value="0">
    <accordion-item>
        <accordion-trigger>
            Section 1
        </accordion-trigger>
        <accordion-panel>
            <ui-transition name="collapse">
                <div>Content 1</div>
            </ui-transition>
        </accordion-panel>
    </accordion-item>

    <accordion-item>
        <accordion-trigger>
            Section 2
        </accordion-trigger>
        <accordion-panel>
            <ui-transition name="collapse">
                <div>Content 2</div>
            </ui-transition>
        </accordion-panel>
    </accordion-item>
</accordion-root>
*/
class AccordionRoot extends HTMLElement {
    connectedCallback() {
        Promise.all([
            customElements.whenDefined("accordion-item"),
            customElements.whenDefined("accordion-trigger"),
            customElements.whenDefined("accordion-panel")
        ]).then(() => this.init());
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


const accordionItemStyle = new CSSStyleSheet();
accordionItemStyle.replaceSync(`
:host {
    display: block;
}
`);
class AccordionItem extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: "open" });

        this.shadowRoot.innerHTML = `
            <slot name="trigger"></slot>
            <slot name="panel"></slot>
        `;

        this.shadowRoot.adoptedStyleSheets = [accordionItemStyle];
    }

    connectedCallback() {
        this.trigger = this.querySelector("accordion-trigger");
        this.panel = this.querySelector("accordion-panel");

        if (!this.trigger || !this.panel) return;

        AccordionItem._idCounter = AccordionItem._idCounter || 0;
        const id = AccordionItem._idCounter++;

        this.trigger.id ||= `acc-trigger-${id}`;
        this.panel.id ||= `acc-panel-${id}`;

        this.trigger.setAttribute("aria-controls", this.panel.id);
        this.panel.setAttribute("aria-labelledby", this.trigger.id);

        if (!this.hasAttribute("state")) {
            this.setAttribute("state", "closed");
        }
    }

    setActive(active) {
        const state = active ? "open" : "closed";
        this.setAttribute("state", state);

        this.trigger?.setActive(active);
        this.panel?.setActive(active);
    }
}

customElements.define("accordion-item", AccordionItem);


class AccordionTrigger extends HTMLElement {
    constructor() {
        super();
        this.setAttribute("role", "button");
        this.setAttribute("tabindex", "0");
        this.setAttribute("aria-expanded", "false");
        this.setAttribute("slot", "trigger");
        this.className = "Button-root";

        this.emit = this.emit.bind(this);
        this.onKey = this.onKey.bind(this);
    }

    connectedCallback() {
        this.addEventListener("click", this.emit);
        this.addEventListener("keydown", this.onKey);
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
    }

    onKey(e) {
        if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            this.emit();
        }
    }
}
customElements.define("accordion-trigger", AccordionTrigger);



const accordionPanelStyle = new CSSStyleSheet();
accordionPanelStyle.replaceSync(`
:host {
    display: block;
}

.panel-inner {
    padding: 1rem;
}
`);
class AccordionPanel extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: "open" });

        this.setAttribute("role", "region");
        this.setAttribute("aria-hidden", "true");
        this.setAttribute("slot", "panel");

        this.shadowRoot.adoptedStyleSheets = [accordionPanelStyle];
    }

    connectedCallback() {
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
        this._transition.hide();
    }

    setActive(active) {
        this.setAttribute("aria-hidden", String(!active));

        if (active) this._transition?.show();
        else this._transition?.hide();
    }
}
customElements.define("accordion-panel", AccordionPanel);