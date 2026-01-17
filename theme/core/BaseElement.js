/**
 * @class BaseElement
 * @extends HTMLElement
 */
export default class BaseElement extends HTMLElement {

    /**
     * @param {Object} [options]
     * @param {boolean} [options.shadow=true]
     * @param {boolean} [options.delegatesFocus=false]
    */
    constructor({ shadow = true, delegatesFocus = false } = {}) {
        super();

        /** @private @type {boolean} */
        this._connected = false;

        /** @private @type {Array<Function>} Cleanup functions called on disconnect */
        this._cleanup = [];

        if (shadow) {
            this.attachShadow({
                mode: "open",
                delegatesFocus
            });
        }
    }

    /**
     * Called automatically when the element is connected to the DOM
     * Marks the element as connected and calls `onConnect` if defined
     */
    connectedCallback() {
        if (this._connected) return;
        this._connected = true;
        this.onConnect?.();
    }


    /**
     * Called automatically when the element is disconnected from the DOM
     * Cleans up all registered listeners/cleanup functions
     */
    disconnectedCallback() {
        this._cleanup.forEach(fn => fn());
        this._cleanup = [];
        this._connected = false;
        this.onDisconnect?.();
    }

    /**
    * Adds a cleanup function to be called when the element is disconnected
    * @param {() => void} fn - Function to call on disconnect
    */
    addCleanup(fn) {
        this._cleanup.push(fn);
    }

    /** @type {Map<string, CSSStyleSheet>} Static cache for loaded CSS styles */
    static styleCache = new Map();


    /**
     * Load a CSS file and cache it as a CSSStyleSheet
     * @param {string} url - URL of the CSS file
     * @returns {Promise<CSSStyleSheet>}
     */
    static async loadStyle(url) {
        if (this.styleCache.has(url)) {
            return this.styleCache.get(url);
        }

        const css = await fetch(url).then(r => r.text());
        const sheet = new CSSStyleSheet();
        sheet.replaceSync(css);

        this.styleCache.set(url, sheet);
        return sheet;
    }

    /**
     * Adopt one or more stylesheets into the shadow DOM
     * @param {string[]} urls - Array of CSS URLs to load and adopt
     */
    async adoptStyles(urls = []) {
        if (!this.shadowRoot || !urls.length) return;

        const sheets = await Promise.all(
            urls.map(url => this.constructor.loadStyle(url))
        );

        const existing = this.shadowRoot.adoptedStyleSheets || [];
        this.shadowRoot.adoptedStyleSheets = [...existing, ...sheets];
    }

    /**
     * Get the value of an attribute, with an optional fallback
     * @param {string} name - Attribute name
     * @param {string|null} [fallback=null] - Value if attribute does not exist
     * @returns {string|null}
     */
    attr(name, fallback = null) {
        return this.hasAttribute(name)
            ? this.getAttribute(name)
            : fallback;
    }

    /**
     * Check if an attribute exists
     * @param {string} name - Attribute name
     * @returns {boolean}
     */
    boolAttr(name) {
        return this.hasAttribute(name);
    }

    /**
     * Get a numeric attribute value
     * @param {string} name - Attribute name
     * @param {number} [fallback=0] - Value if attribute does not exist or is NaN
     * @returns {number}
     */
    numAttr(name, fallback = 0) {
        const val = Number(this.getAttribute(name));
        return Number.isNaN(val) ? fallback : val;
    }

    /**
     * Set or remove a boolean attribute
     * @param {string} name - Attribute name
     * @param {boolean} value - True to set, false to remove
     */
    setBoolAttr(name, value) {
        value
            ? this.setAttribute(name, "")
            : this.removeAttribute(name);
    }

    /**
     * Wait for multiple custom elements to be defined
     * @param {string[]} tags - Array of custom element tag names
     * @returns {Promise<void>}
     */
    async waitForComponents(...tags) {
        await Promise.all(tags.map(tag => customElements.whenDefined(tag)));
    }

    /**
     * Emit a custom event
     * @param {string} name - Event name
     * @param {any} [detail={}] - Event detail payload
     * @param {CustomEventInit} [options={}] - Additional CustomEvent options
     */
    emit(name, detail = {}, options = {}) {
        this.dispatchEvent(
            new CustomEvent(name, {
                bubbles: true,
                composed: true,
                detail,
                ...options
            })
        );
    }

    /**
     * Add an event listener with automatic cleanup
     * @param {EventTarget} target - Target to attach listener to
     * @param {string} name - Event name
     * @param {Function} fn - Callback function
     * @param {boolean|AddEventListenerOptions} [options={}] - Listener options
     */
    onEvent(target, name, fn, options = {}) {
        target.addEventListener(name, fn, options);
        this.addCleanup(() => target.removeEventListener(name, fn));
    }
}

window.BaseElement = BaseElement;