import BaseElement from "../../core/BaseElement.js";
import { getLuminance, queueCallback, rgbToRgba } from "../../core/utils.js";

export default class UIButton extends BaseElement {
    static observedAttributes = [
        "href",
        "as",
        "size",
        "variant",
        "class",
        "color",
        "loading",
        "disabled"
    ];

    constructor() {
        super();
        this._root = null;
    }

    async connectedCallback() {
        super.connectedCallback();

        await this.adoptStyles([
            "theme/styles/components/button/button.css"
        ]);

        this.mount();
        this.update();
        this.attachEvents();
    }

    attributeChangedCallback() {
        if (!this.shadowRoot) return;
        this.update();
    }

    /* ---------------------------------- */
    /* Mount once                         */
    /* ---------------------------------- */

    mount() {
        const tag = this.getTag();
        const el = document.createElement(tag);
        this._root = el;

        el.setAttribute("part", "root");
        this.shadowRoot.appendChild(el);

        el.innerHTML = `
            <span part="content" class="Button-content">
                <slot></slot>
            </span>
        `;
    }

    update() {
        const tag = this.getTag();

        if (this._root.tagName.toLowerCase() !== tag) {
            const next = document.createElement(tag);
            next.replaceChildren(...this._root.childNodes);
            this._root.replaceWith(next);
            this._root = next;
            this._root.setAttribute("part", "root");
        }

        const el = this._root;

        const size = this.attr("size", "md");
        const variant = this.attr("variant", "text");
        const color = this.attr("color", "text");
        const loading = this.boolAttr("loading");
        const disabled = this.boolAttr("disabled") || loading;


        el.classList.add("Button-root");

        el.classList.toggle("Button-primary", color === "primary");
        el.classList.toggle("Button-icon-start", this.hasAttribute("start-icon"));
        el.classList.toggle("Button-icon-end", this.hasAttribute("end-icon"));


        el.dataset.size = size;
        el.dataset.variant = variant;


        if (tag === "a") {
            el.href = this.getAttribute("href") || "";
            el.setAttribute("role", "button");
        } else {
            el.removeAttribute("href");
            el.removeAttribute("role");
        }

        if (disabled) {
            el.setAttribute("aria-disabled", "true");
            if (tag === "button") el.disabled = true;
        } else {
            el.removeAttribute("aria-disabled");
            if (tag === "button") el.disabled = false;
        }

        this.syncIcons(el);
        this.syncLoader(el, loading);
    }


    syncIcons(el) {
        this.removeIfExists(".Button-icon-start-node");
        this.removeIfExists(".Button-icon-end-node");

        if (this.hasAttribute("start-icon")) {
            const icon = this.createIcon(this.getAttribute("start-icon"));
            icon.classList.add("Button-icon-start-node");
            el.prepend(icon);
        }

        if (this.hasAttribute("end-icon")) {
            const icon = this.createIcon(this.getAttribute("end-icon"));
            icon.classList.add("Button-icon-end-node");
            el.append(icon);
        }
    }

    createIcon(value) {
        const wrapper = document.createElement("span");
        wrapper.innerHTML = /<[a-z][\s\S]*>/i.test(value)
            ? value
            : `<iconify-icon icon="${value}"></iconify-icon>`;
        return wrapper.firstElementChild;
    }


    syncLoader(el, loading) {
        let loader = el.querySelector(".Button-loader");

        if (loading && !loader) {
            loader = document.createElement("span");
            loader.className = "Button-loader";
            loader.setAttribute("part", "loader");
            loader.innerHTML = `
                <iconify-icon part="icon" icon="svg-spinners:ring-resize"></iconify-icon>
            `;
            el.appendChild(loader);
        }

        if (!loading && loader) {
            loader.remove();
        }
    }

    removeIfExists(selector) {
        this._root.querySelector(selector)?.remove();
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
        const ripple = document.createElement("span");
        const diameter = Math.max(
            parseInt(styles.height),
            parseInt(styles.width) * 1.5
        );

        ripple.className = "ripple-wave";
        ripple.style.cssText = `
            width:${diameter}px;
            height:${diameter}px;
            position:absolute;
            pointer-events:none;
            z-index:-1;
            border-radius:50%;
            transform:scale(0);
            translate:-50% -50%;
            left:${e.offsetX}px;
            top:${e.offsetY}px;
            transition:transform 2s cubic-bezier(0.257,0.97,0.134,1),
                       opacity 1s ease 100ms;
        `;

        const lum = getLuminance(getComputedStyle(button).color);
        ripple.style.backgroundColor = rgbToRgba(
            getComputedStyle(button).color,
            this.getRippleAlpha(lum)
        );

        button.appendChild(ripple);
        requestAnimationFrame(() => ripple.style.transform = "scale(1.5)");

        const clean = () => {
            ripple.style.opacity = 0;
            queueCallback(() => ripple.remove(), ripple, true);
        };

        button.addEventListener("pointerup", clean, { once: true });
        button.addEventListener("pointerleave", clean, { once: true });
    }


    getTag() {
        if (this.hasAttribute("as")) return this.getAttribute("as");
        if (this.hasAttribute("href")) return "a";
        return "button";
    }
}

customElements.define("ui-button", UIButton);