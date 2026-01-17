const sheet = new CSSStyleSheet();
sheet.replaceSync(`
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

class UITransition extends BaseElement {
    static get observedAttributes() {
        return ["origin", "orientation"];
    }

    constructor() {
        super();
        this.shadowRoot.innerHTML = `<slot></slot>`;
        this.shadowRoot.adoptedStyleSheets = [sheet];

        this._state = "exited";
        this._timer = null;
    }

    async connectedCallback() {
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