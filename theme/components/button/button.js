import BaseElement from "../../core/BaseElement.js";
import { getLuminance, queueCallback, rgbToRgba } from "../../core/utils.js";

export default class UIButton extends BaseElement {
    static observedAttributes = [
        "href",
        "as",
        "size",
        "variant",
        "loading",
        "disabled"
    ];

    constructor() {
        super({ shadow: true, delegatesFocus: true });
        this._root = null;
    }

    async onConnect() {
        await this.adoptStyles([
            "theme/styles/components/button/button.css"
        ]);

        this.render();
        this.attachEvents();
    }

    attributeChangedCallback() {
        if (!this.shadowRoot) return;
        this.render();
    }

    render() {
        const tag = this.getTag();
        const size = this.attr("size", "md");
        const variant = this.attr("variant", "text");
        const loading = this.boolAttr("loading");
        const disabled = this.boolAttr("disabled") || loading;

        this.shadowRoot.innerHTML = "";

        const el = document.createElement(tag);
        this._root = el;

        el.className = "Button-root";
        el.dataset.size = size;
        el.dataset.variant = variant;
        el.setAttribute("part", "root");

        if (tag === "a") {
            el.href = this.getAttribute("href");
            el.setAttribute("role", "button");
        }

        if (disabled) {
            el.setAttribute("aria-disabled", "true");
            if (tag === "button") el.disabled = true;
        } else {
            el.removeAttribute("aria-disabled");
            if (tag === "button") el.disabled = false;
        }

        el.innerHTML = `
            <span part="content" class="Button-content">
                <slot></slot>
            </span>
            ${loading ? this.renderLoader() : ""}
        `;

        this.shadowRoot.appendChild(el);
    }

    renderLoader() {
        return `
            <span part="loader" class="Button-loader">
                <iconify-icon part="icon" icon="svg-spinners:ring-resize"></iconify-icon>
            </span>
        `;
    }

    getTag() {
        if (this.hasAttribute("as")) return this.getAttribute("as");
        if (this.hasAttribute("href")) return "a";
        return "button";
    }

    attachEvents() {
        this.onEvent(this.shadowRoot, "pointerdown", e => {
            const button = e.target.closest(".Button-root");
            if (!button || this.boolAttr("disabled") || this.boolAttr("loading")) return;
            this.createRippleWave(button, e);
        });
    }

    getRippleAlpha(lum) {
        if (lum > 0.8) return 0.35;
        if (lum > 0.18) return 0.25;
        return 0.2;
    }

    createRippleWave(button, e) {
        const styles = getComputedStyle(button);
        let rippleEl = document.createElement("span");
        let diameter = Math.max(parseInt(styles.height), parseInt(styles.width) * 1.5);

        rippleEl.className = "ripple-wave";
        rippleEl.style.height = rippleEl.style.width = `${diameter}px`;
        rippleEl.style.position = "absolute";
        rippleEl.style.pointerEvents = "none";
        rippleEl.style.zIndex = "-1";
        rippleEl.style.borderRadius = "50%";
        rippleEl.style.transform = "scale(0)";
        rippleEl.style.translate = "-50% -50%";
        rippleEl.style.left = `${e.offsetX}px`;
        rippleEl.style.top = `${e.offsetY}px`;

        const textColor = window.getComputedStyle(button).color;
        const lum = getLuminance(textColor);

        rippleEl.style.backgroundColor = rgbToRgba(textColor, this.getRippleAlpha(lum));
        rippleEl.style.transition = "transform 2s cubic-bezier(0.257, 0.97, 0.134, 1), opacity 1s ease 100ms";

        button.appendChild(rippleEl);
        setTimeout(() => {
            rippleEl.style.transform = "scale(1.5)";
        }, 0);

        const cleanUp = () => {
            rippleEl.style.opacity = 0;

            queueCallback(() => {
                rippleEl.remove();
            }, rippleEl, true)

        };

        button.addEventListener("pointerup", cleanUp);
        button.addEventListener("pointerleave", cleanUp);
    }
}

customElements.define("ui-button", UIButton);