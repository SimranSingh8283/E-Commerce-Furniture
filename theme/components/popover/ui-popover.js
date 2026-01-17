import BaseElement from "../../core/BaseElement.js";

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
    background-color: hsl(0, 0%, 0%, 20%);
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

class UIPopover extends BaseElement {
    static get observedAttributes() {
        return ["origin"];
    }

    constructor() {
        super();
        this.shadowRoot.adoptedStyleSheets = [popoverSheet];

        const transitionName = this.getAttribute("transition") || "zoom";
        const backdrop = this.getAttribute("backdrop");
        const origin = this.getAttribute("origin");

        this.shadowRoot.innerHTML = `
            ${backdrop ? `<ui-transition name="fade" class="Popover-backdrop"></ui-transition>` : ""}
            <ui-transition class="Popover-ui" name="${transitionName}" ${origin ? `origin="${origin}"` : ""}>
                <slot></slot>
            </ui-transition>
        `;
    }

    onConnect() {
        if (this.parentNode !== document.body) {
            document.body.appendChild(this);
        }

        this.backdrop = this.shadowRoot.querySelector(".Popover-backdrop");
        this.paper = this.shadowRoot.querySelector(".Popover-ui");
        this.transitions = [
            ...this.shadowRoot.querySelectorAll("ui-transition")
        ];

        this.anchorSelector = this.getAttribute("anchor");
        this.placement = this.getAttribute("placement") || "bottom";

        if (this.anchorSelector) {
            this.anchorEl = document.querySelector(this.anchorSelector);
            this.anchorEl?.addEventListener("click", this.toggle);
        }

        window.addEventListener("keydown", this._onKeyDown);
        window.addEventListener("scroll", this._onScroll);
        this.addEventListener("click", this._onHostClick);
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

    onDisconnect() {
        this.anchorEl?.removeEventListener("click", this.toggle);
        window.removeEventListener("keydown", this._onKeyDown);
        window.removeEventListener("scroll", this._onScroll);
        this.removeEventListener("click", this._onHostClick);
    }

    /* ---------- STATE ---------- */

    get open() {
        return this.hasAttribute("open");
    }

    set open(val) {
        val ? this.setAttribute("open", "") : this.removeAttribute("open");
    }


    _showTransitions() {
        this.transitions.forEach(t => t?.show());
    }

    _hideTransitions() {
        this.transitions.forEach(t => t?.hide());
    }

    _getMaxDuration() {
        return Math.max(
            ...this.transitions.map(t => t?.duration || 0),
            200
        );
    }
    
    _onHostClick = (e) => {
        const path = e.composedPath();

        if (path.includes(this.paper)) return;

        this.hide();
    };

    /* ---------- ACTIONS ---------- */

    show = () => {
        if (this.open) return;

        this.open = true;
        this._position();
        this._showTransitions();

        requestAnimationFrame(() => {
            this._position();
        });
    };

    hide = () => {
        if (!this.open) return;

        this._hideTransitions();

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