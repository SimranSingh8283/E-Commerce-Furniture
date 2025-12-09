/**
 * Selects elements from the DOM.
 *
 * @param {string|Element|NodeList|Array|Document} el - CSS selector string, DOM element, NodeList, array, or document.
 * @param {boolean} [all=false] - Whether to select all matching elements.
 * @param {Object} [options={}] - Additional options.
 * @param {string|Element|Document} [options.node=document] - The wrapper context (selector string, Element, or Document).
 * @returns {Element|Element[]|null} - The selected element(s) or null if none found.
 */
const select = (el, all = false, { node: wrapper = document } = {}) => {
    try {
        if (typeof wrapper === "string") {
            wrapper = document.querySelector(wrapper.trim());
            if (!wrapper) {
                console.warn(`Wrapper '${wrapper}' not found.`);
                return null;
            }
        } else if (!(wrapper instanceof Element || wrapper instanceof Document)) {
            console.warn("Invalid 'wrapper' argument: must be a selector string, Element, or Document. Using document.");
            wrapper = document;
        }

        if (typeof el === "string") {
            el = el.trim();
            const elements = wrapper.querySelectorAll(el);
            return all ? Array.from(elements) : elements[0] || null;
        }

        if (el instanceof Element || el instanceof Document) return all ? [el] : el;
        if (el instanceof NodeList || Array.isArray(el)) return all ? [...el] : el[0] || null;

        console.warn("Invalid 'el' argument: must be a selector string, Element, NodeList, Array, or Document.");
        return null;
    } catch (error) {
        console.error(`Error selecting elements ('${el}') within '${wrapper}':`, error);
        return null;
    }
};



/**
 * Queues a callback function to execute after a transition ends or via requestAnimationFrame.
 * @param {Function} cb - The callback function to execute.
 * @param {Element} e - The target element.
 * @param {boolean} [t=false] - Whether to wait for transition end.
 */
const _queueCallback = (cb, e, t = false) => {
    if (!t) {
        window.requestAnimationFrame(() => cb());
        return;
    }

    const handler = () => {
        cb();
        e.removeEventListener('transitionend', handler);
    };
    e.addEventListener('transitionend', handler, { once: true });
}

/**
 * Creates a debounced function that delays invoking `func` until after `wait` milliseconds.
 * @param {Function} func - The function to debounce.
 * @param {number} wait - The delay in milliseconds.
 * @returns {Function} - The debounced function.
 */
const _debounce = (func, wait) => {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

/**
 * Creates a throttled function that only invokes `func` at most once per every `wait` milliseconds.
 * @param {Function} func - The function to throttle.
 * @param {number} wait - The interval in milliseconds.
 * @returns {Function} - The throttled function.
 */
const _throttle = (func, wait) => {
    let timeout = null;
    let previous = 0;

    return function (...args) {
        const now = Date.now();
        const remaining = wait - (now - previous);

        if (remaining <= 0) {
            if (timeout) {
                clearTimeout(timeout);
                timeout = null;
            }
            previous = now;
            func.apply(this, args);
        } else if (!timeout) {
            timeout = setTimeout(() => {
                previous = Date.now();
                timeout = null;
                func.apply(this, args);
            }, remaining);
        }
    };
}

/**
 * Adds an event listener to one or multiple elements.
 * @param {string} type - The event type.
 * @param {string|Element|NodeList} el - The selector or element(s) to attach the event.
 * @param {Function} listener - The event listener function.
 * @param {boolean} [all=false] - Whether to attach to all matched elements.
 */
const on = (type, el, listener, all = false) => {
    let selectEl = el || select(el, all);
    if (selectEl) {
        if (all) {
            selectEl.forEach(e => e.addEventListener(type, listener));
        } else {
            selectEl.addEventListener(type, listener);
        }
    }
}


/**
 * Returns a promise that resolves after a given time.
 * @param {number} time - The delay in milliseconds.
 * @returns {Promise<void>} - A promise that resolves after the given time.
 */
const wait = (time) => new Promise(resolve => setTimeout(resolve, time))




/**
 * Sets multiple attributes on a DOM element.
 * @param {Element} element - The target element.
 * @param {Object} attributes - The attributes to set.
 */

const setAttributes = (element, attributes) => {
    Object.keys(attributes).forEach(attr => {
        element.setAttribute(attr, attributes[attr]);
    });
}



/**
 * Finds the closest matching sibling element.
 * @param {string} target - The CSS selector to match.
 * @param {Element} el - The reference element.
 * @returns {Element} - The matched sibling element.
 * @throws Will throw an error if no matching element is found.
 */
const findClosestTarget = (target, el) => {
    let match = el.nextElementSibling;
    while (match) {
        if (match.matches(target)) return match;
        match = match.nextElementSibling;
    }
    match = el.previousElementSibling;
    while (match) {
        if (match.matches(target)) return match;
        match = match.previousElementSibling;
    }
    throw new Error(`No matching element found for the target: ${target}`);
}

function getLuminance(rgb) {
    const values = rgb.match(/\d+/g).map(Number);
    return values.map(val => {
        val /= 255;
        return val <= 0.03928 ? val / 12.92 : Math.pow((val + 0.055) / 1.055, 2.4);
    }).reduce((acc, val, i) => acc + val * [0.2126, 0.7152, 0.0722][i], 0);
}

function rgbToRgba(rgb, alpha) {
    const values = rgb.match(/\d+/g).map(Number);
    return `rgba(${values[0]}, ${values[1]}, ${values[2]}, ${alpha})`;
}




/**
 * Checks if a media query matches the current viewport and listens for changes.
 *
 * @param {string} query - The media query string (e.g., "(min-width: 992px)").
 * @returns {{ matches: boolean, unsubscribe: () => void }} 
 */
function useMedia(query) {
    if (typeof window === "undefined" || !window.matchMedia) {
        console.warn("matchMedia is not supported in this environment.");
        return { matches: false, unsubscribe: () => { } };
    }

    const mediaQueryList = window.matchMedia(query);
    let matches = mediaQueryList.matches;

    /**
     * Updates the match state when the media query changes.
     * @param {MediaQueryListEvent} e - The media query event.
     */
    const updateMatch = (e) => {
        matches = e.matches;
    };

    mediaQueryList.addEventListener("change", updateMatch);

    return {
        get matches() {
            return matches; // Ensure it always reflects the latest state
        },
        unsubscribe: () => mediaQueryList.removeEventListener("change", updateMatch),
    };
}

/**
 * Forces browser reflow to ensure transitions start properly.
 * @param {HTMLElement} node
 */
function reflow(node) {
    // Reading offsetHeight triggers a reflow
    return node.scrollTop;
}


/**
 * Returns the current browser scrollbar width in pixels.
 * Works dynamically and reliably across browsers.
 * @returns {number}
 */
function getScrollbarWidth() {
    const div = document.createElement("div");
    div.style.visibility = "hidden";
    div.style.overflow = "scroll";
    div.style.position = "absolute";
    div.style.top = "-9999px";
    div.style.width = "100px";
    div.style.height = "100px";

    document.body.appendChild(div);

    const scrollbarWidth = div.offsetWidth - div.clientWidth;
    document.body.removeChild(div);

    return scrollbarWidth;
}

export { getScrollbarWidth, select, on, wait, setAttributes, findClosestTarget, _queueCallback, _debounce, _throttle, getLuminance, rgbToRgba, useMedia, reflow }