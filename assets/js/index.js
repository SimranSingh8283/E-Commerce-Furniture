import { select, _queueCallback, _debounce, _throttle, getLuminance, rgbToRgba, reflow, getScrollbarWidth } from "./utils.js";


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
 * @param {Window} w
 */
(function Parallax(w) {
    const _p = Array.from(select(".BlockParallax-root", true));

    const friction = 0.25;
    let activeParallaxElements = [];
    const media = useMedia("(min-width: 992px)");

    /**
     * Callback function for IntersectionObserver.
     * 
     * @param {IntersectionObserverEntry[]} entries - Array of observed elements.
     * @param {IntersectionObserver} [observer] - The IntersectionObserver instance (optional).
     */
    const handleObserver = (entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                if (!activeParallaxElements.includes(entry.target)) {
                    activeParallaxElements.push(entry.target);
                }
            } else {
                activeParallaxElements = activeParallaxElements.filter(el => el !== entry.target);
            }
        });
    };

    const observer = new IntersectionObserver(handleObserver, { threshold: 0 });

    if (Array.isArray(_p)) {
        _p.forEach(el => observer.observe(el));
    }

    const handleScroll = () => {
        activeParallaxElements.forEach(el => {
            const f = el.dataset.friction;
            const images = el.querySelectorAll(".Block-object > *");
            const top = el.offsetTop;

            const isFluid = el.classList.contains("Block-fluid");

            if (!images || (media.matches && isFluid)) return;

            const translateY = (w.scrollY - top) * (f || friction);
            images.forEach(image => {
                image.style.transform = `translateY(${translateY}px)`;
            })
        });
    };

    w.addEventListener('scroll', handleScroll);
})(window);

const formControlRoot = select(".FormControl-root", true);

function checkAllInputs() {
    formControlRoot.forEach(el => {
        const input = el.querySelector(".InputBase-input");
        if (!input) return;

        const isAutofilled = input.matches(":-webkit-autofill");
        const hasValue = input.value.trim() !== "";
        const isFocused = document.activeElement === input;

        if (hasValue || isFocused || isAutofilled) {
            el.classList.add("FormControl-shrink");
        } else {
            el.classList.remove("FormControl-shrink");
        }
    });
}

checkAllInputs();

function startFocusWatcher() {
    let animationFrame;

    const loop = () => {
        checkAllInputs();
        animationFrame = requestAnimationFrame(loop);
    };

    loop();

    const stop = () => cancelAnimationFrame(animationFrame);

    return stop;
}

formControlRoot.forEach(el => {
    const input = el.querySelector(".InputBase-input");
    if (!input) return;

    input.addEventListener("input", checkAllInputs);

    let stopWatcher;

    input.addEventListener("focus", () => {
        stopWatcher = startFocusWatcher();
    });

    input.addEventListener("blur", () => {
        if (stopWatcher) stopWatcher();
        checkAllInputs();
    });
});

const isLikelyMacTrackpad = () => {
    const isMac = navigator.platform.toUpperCase().includes('MAC');
    const hasFineScroll = window.matchMedia("(pointer: fine)").matches;
    return isMac && hasFineScroll;
};

document.addEventListener("DOMContentLoaded", () => {

    /**
    * Updates the header class based on scroll position.
    * @param {number} scrollY
    */
    function updateHeaderScrolled(scrollY) {
        const header = document.querySelector("#Header-root");
        if (!header) return;

        const headerHeight = header.offsetHeight;
        const scrolled = scrollY > headerHeight;

        header.classList.toggle("Header-scrolled", scrolled);
    }

    updateHeaderScrolled(window.scrollY);

    if (!isLikelyMacTrackpad()) {
        const lenis = new Lenis({
            lerp: 0.1,
            smooth: true,
            smoothTouch: true,
            wrapper: window,
            autoResize: true,
            direction: 'vertical'
        });

        lenis.on('scroll', (e) => {
            updateHeaderScrolled(e.scroll);
        });

        function raf(time) {
            lenis.raf(time)
            requestAnimationFrame(raf)
        }

        requestAnimationFrame(raf)

        const scrollToOptions = {
            offset: 0,
            lerp: 0.075,
            immediate: false,
            lock: false,
            force: false,
        };

        select('.scrollTo', true).forEach(a => {
            a.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = select(targetId);

                if (targetElement) {
                    lenis.scrollTo(targetElement, scrollToOptions);
                }
            });
        });

        let prevEl = null;
        select('#navbar .scrollTo', true).forEach(a => {
            a.addEventListener('click', function (e) {
                e.preventDefault();

                if (this.classList.contains('in-view')) return;

                if (prevEl) {
                    prevEl.classList.remove('in-view');
                }

                this.classList.add('in-view');
                prevEl = this;
            });
        });

        const scrollableEls = select("[data-scroll='lenis']", true);
        scrollableEls.forEach(el => {
            const lenisDiv = new Lenis({
                wrapper: el,
                smooth: true,
            });

            function rafDiv(time) {
                lenisDiv.raf(time);
                requestAnimationFrame(rafDiv);
            }

            requestAnimationFrame(rafDiv);
        })
    }


    /**
    * DrawerSystem
    * A vanilla JavaScript implementation of a configurable drawer (sidebar/panel) system.
    * 
    * Features:
    * - Open drawers via elements with `data-drawer` attribute.
    * - Controls animation direction via `data-direction` (`left`, `right`, `top`, `bottom`).
    * - Supports close buttons with `data-drawer-close`.
    * - Supports `data-timeout` for animation duration.
    * - Supports ESC key to close (unless `data-close-on-esc="false"`).
    */
    (function DrawerSystem() {
        /**
        * All elements that can trigger a drawer to open.
        * Must have a `data-drawer` attribute pointing to a drawer selector (e.g. `#Drawer-sidebar`)
        * @type {NodeListOf<HTMLElement>}
        */
        const buttons = select("[data-drawer]", true);


        buttons.forEach(button => {
            /** @type {string} */
            const targetSelector = button.dataset.drawer;
            /** @type {HTMLElement|null} */
            const drawer = select(targetSelector);
            if (!drawer) return;

            /** @type {HTMLElement|null} */
            const container = drawer.querySelector(".Drawer-container");
            if (!container) return;

            /** @type {number} */
            const timeout = parseInt(drawer.getAttribute("data-timeout") || "400");
            /** @type {"left"|"right"|"top"|"bottom"} */
            const direction = drawer.getAttribute("data-direction") || "left";

            drawer.setAttribute("data-direction", direction);
            drawer.style.setProperty('--duration', timeout + "ms");

            const axis = (direction === "left" || direction === "right") ? "X" : "Y";
            const sign = (direction === "right" || direction === "bottom") ? "" : "-";

            container.style.transform = `translate${axis}(${sign}100%)`;
            container.style.transition = `transform ${timeout}ms cubic-bezier(0.245, 0.97, 0.125, 1)`;

            button.addEventListener("click", () => {
                drawer.style.display = "block";
                reflow(drawer);

                if (direction === "left" || direction === "right") {
                    document.body.classList.add(`Drawer-active-${direction}`);
                    document.documentElement.style.setProperty("--scrollbar-width", getScrollbarWidth() + "px");
                    document.body.style.overflow = "hidden";
                    document.body.style.maxWidth = `calc(100% - ${getScrollbarWidth()}px)`;

                    if (direction === "left") {
                        drawer.style.marginLeft = "auto";
                        drawer.style.marginRight = "unset";
                    } else {
                        drawer.style.marginRight = "auto";
                        drawer.style.marginLeft = "unset";
                    }
                }

                container.style.transform = `translate${axis}(0)`;
                drawer.classList.add("Drawer-open");

                if (drawer.getAttribute("data-close-on-esc") !== "false") {
                    const escHandler = (e) => {
                        if (e.key === "Escape") closeDrawer(drawer, container, axis, sign, timeout, direction);
                    };
                    document.addEventListener("keydown", escHandler, { once: true });
                }
            });

            drawer.querySelectorAll("[data-drawer-close]").forEach(closeBtn => {
                closeBtn.addEventListener("click", () => {
                    closeDrawer(drawer, container, axis, sign, timeout, direction);
                });
            });
        });

        /**
         * Closes the drawer with animation.
         * @param {HTMLElement} drawer - The drawer element to close.
         * @param {HTMLElement} container - The animated container inside the drawer.
         * @param {"X"|"Y"} axis - Transform axis.
         * @param {string} sign - Direction sign (- or "").
         * @param {number} timeout - Animation duration.
         * @param {"left"|"right"|"top"|"bottom"} direction - Drawer slide direction.
         */
        function closeDrawer(drawer, container, axis, sign, timeout, direction) {
            container.style.transform = `translate${axis}(${sign}100%)`;
            drawer.classList.remove("Drawer-open");

            setTimeout(() => {
                drawer.style.display = "none";
                document.body.classList.remove(`Drawer-active-${direction}`);
                document.documentElement.style.removeProperty("--scrollbar-width");

                if (direction === "left" || direction === "right") {
                    document.body.style.overflow = "";
                    document.body.style.maxWidth = "";
                    drawer.style.marginLeft = "";
                    drawer.style.marginRight = "";
                }
            }, timeout);
        }
    })();


    (function TooltipSystem() {
        let tooltipEl = null;
        let hoveredTarget = null;
        let tooltipTrigger = null; // 'hover' | 'focus'
        let showTimer = null;
        let hideTimer = null;
        const SHOW_DELAY = 120; // ms before showing
        const HIDE_DELAY = 80;  // ms before hiding
        const GAP = 8;

        const closest = (el, sel) => el ? el.closest(sel) : null;

        // Create tooltip
        function createTooltip(message) {
            const el = document.createElement('div');
            el.className = 'Tooltip-root';
            el.textContent = message;
            el.style.position = 'fixed';
            el.style.top = '0px';
            el.style.left = '0px';
            el.style.willChange = 'transform, opacity';
            document.body.appendChild(el);
            requestAnimationFrame(() => el.classList.add('Tooltip-show'));
            return el;
        }

        // Destroy tooltip immediately
        function destroyTooltipImmediate() {
            if (!tooltipEl) return;
            tooltipEl.classList.remove('Tooltip-show');
            const el = tooltipEl;
            setTimeout(() => {
                if (el && el.parentNode) el.parentNode.removeChild(el);
            }, 160);
            tooltipEl = null;
            hoveredTarget = null;
            tooltipTrigger = null;
        }

        // Safety destroy (clears timers)
        function destroyTooltip() {
            if (showTimer) { clearTimeout(showTimer); showTimer = null; }
            if (hideTimer) { clearTimeout(hideTimer); hideTimer = null; }
            destroyTooltipImmediate();
        }

        // Position tooltip with auto-flip
        function positionTooltip(target, tooltip, preferred = 'bottom') {
            if (!target || !tooltip) return;
            const rect = target.getBoundingClientRect();
            tooltip.style.left = '0px';
            tooltip.style.top = '0px';
            const tRect = tooltip.getBoundingClientRect();

            const fits = {
                top: rect.top >= tRect.height + GAP,
                bottom: (window.innerHeight - rect.bottom) >= tRect.height + GAP,
                left: rect.left >= tRect.width + GAP,
                right: (window.innerWidth - rect.right) >= tRect.width + GAP
            };

            const placements = ['top', 'bottom', 'right', 'left'];
            let placement = preferred;
            if (!fits[placement]) placement = placements.find(p => fits[p]) || placement;

            let top = 0, left = 0;
            switch (placement) {
                case 'top':
                    top = rect.top - tRect.height - GAP;
                    left = rect.left + rect.width / 2 - tRect.width / 2;
                    break;
                case 'bottom':
                    top = rect.bottom + GAP;
                    left = rect.left + rect.width / 2 - tRect.width / 2;
                    break;
                case 'right':
                    top = rect.top + rect.height / 2 - tRect.height / 2;
                    left = rect.right + GAP;
                    break;
                case 'left':
                    top = rect.top + rect.height / 2 - tRect.height / 2;
                    left = rect.left - tRect.width - GAP;
                    break;
            }

            left = Math.max(6, Math.min(left, window.innerWidth - tRect.width - 6));
            top = Math.max(6, Math.min(top, window.innerHeight - tRect.height - 6));

            tooltip.style.left = `${Math.round(left)}px`;
            tooltip.style.top = `${Math.round(top)}px`;
        }

        // Track hover via mousemove (prevents click/drag duplication)
        document.addEventListener('mousemove', e => {
            const target = closest(e.target, '[data-tooltip]');

            // Ignore if mouse button pressed
            if (e.buttons > 0) return;

            if (target !== hoveredTarget) {
                // Mouse left previous element
                if (hoveredTarget && tooltipEl) destroyTooltip();

                hoveredTarget = target;

                if (target) {
                    // Schedule tooltip show
                    if (showTimer) clearTimeout(showTimer);
                    showTimer = setTimeout(() => {
                        const message = target.getAttribute('data-tooltip');
                        if (!message) return;
                        tooltipEl = createTooltip(message);
                        tooltipTrigger = 'hover';
                        positionTooltip(target, tooltipEl, target.getAttribute('data-position') || 'bottom');
                    }, SHOW_DELAY);
                }
            }
        });

        // Hide tooltip when leaving element (or moving out quickly)
        document.addEventListener('mouseout', e => {
            if (!tooltipEl) return;
            const from = closest(e.target, '[data-tooltip]');
            const to = e.relatedTarget;

            // Ignore moving to child
            if (from && to && from.contains(to)) return;

            if (hideTimer) clearTimeout(hideTimer);
            hideTimer = setTimeout(() => destroyTooltip(), HIDE_DELAY);
        });

        // Reposition tooltip on scroll/resize
        function onScrollResize() {
            if (tooltipEl && hoveredTarget) {
                positionTooltip(hoveredTarget, tooltipEl, hoveredTarget.getAttribute('data-position') || 'bottom');
            }
        }

        window.addEventListener('scroll', onScrollResize, { passive: true });
        window.addEventListener('resize', onScrollResize);

        // Keyboard focus support
        document.addEventListener('focusin', e => {
            const target = closest(e.target, '[data-tooltip]');
            if (!target) return;

            if (showTimer) clearTimeout(showTimer);
            showTimer = setTimeout(() => {
                const message = target.getAttribute('data-tooltip');
                if (!message) return;
                if (tooltipEl) destroyTooltipImmediate();
                tooltipEl = createTooltip(message);
                tooltipTrigger = 'focus';
                positionTooltip(target, tooltipEl, target.getAttribute('data-position') || 'bottom');
            }, 40);
        });

        document.addEventListener('focusout', e => {
            const target = closest(e.target, '[data-tooltip]');
            if (!target) return;
            destroyTooltip();
        });

        // Clean up on pagehide
        window.addEventListener('pagehide', () => destroyTooltipImmediate());
    })();


    const formControlRoot = select(".FormControl-root", true);

    formControlRoot?.forEach(el => {
        const input = el.querySelector(".InputBase-input");

        if (input.value.length > 0 || document.activeElement === input) {
            el.classList.add("FormControl-shrink");
        }

        const handleFocus = () => {
            el.classList.add("FormControl-shrink");
        };

        const handleBlur = () => {
            if (input.value.length === 0) {
                el.classList.remove("FormControl-shrink");
            }
        };

        input.addEventListener("focus", handleFocus);
        input.addEventListener("blur", handleBlur);
    });



    let elements = select('.Button-root', true);

    if (elements) {
        elements.forEach(el => {
            const btnRootSize = el.dataset.size;

            if (!btnRootSize) {
                el.setAttribute('data-size', 'md');
            }
        })
    }

    document.addEventListener("pointerdown", (e) => {
        if (!e.target.matches('.Button-root')) return;

        createRippleWave(e.target, e);
    })

    const createRippleWave = (button, e) => {
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
        rippleEl.style.backgroundColor = getLuminance(textColor) > 0.8 ? rgbToRgba(textColor, 0.35) : getLuminance(textColor) > 0.18 ? rgbToRgba(textColor, 0.2) : rgbToRgba(textColor, 0.1);
        rippleEl.style.transition = "transform 2s cubic-bezier(0.257, 0.97, 0, 1), opacity 1s ease 100ms";

        button.appendChild(rippleEl);
        setTimeout(() => {
            rippleEl.style.transform = "scale(1.5)";
        }, 0);

        const cleanUp = () => {
            rippleEl.style.opacity = 0;

            _queueCallback(() => {
                rippleEl.remove();
            }, rippleEl, true)

        };

        button.addEventListener("pointerup", cleanUp);
        button.addEventListener("pointerleave", cleanUp);
    };

    const buttonLoaderObserver = new MutationObserver(mutations => {
        mutations.forEach(m => {
            if (m.type !== 'attributes' || m.attributeName !== 'class') return;

            const target = m.target;

            if (!target.querySelector('.Button-loader') && target.classList.contains('Button-loading')) {
                const loader = document.createElement('div');
                loader.className = "Button-loader";

                const spinner = document.createElement('iconify-icon');
                spinner.setAttribute('icon', 'svg-spinners:ring-resize');

                loader.appendChild(spinner);
                target.appendChild(loader);
                target.setAttribute('disabled', 'true');
            } else if (!target.classList.contains('Button-loading')) {
                const loader = target.querySelector('.Button-loader');
                if (loader) loader.remove();
                target.removeAttribute('disabled');
            }
        });
    });

    if (elements) {
        elements.forEach(btn => buttonLoaderObserver.observe(btn, { attributes: true }));
    }

    const bodyObserver = new MutationObserver(mutations => {
        mutations.forEach(m => {
            m.addedNodes.forEach(node => {
                if (!(node instanceof HTMLElement)) return;
                const newBtns = node.querySelectorAll('.Button-root');
                newBtns.forEach(btn => buttonLoaderObserver.observe(btn, { attributes: true }));
            });
        });
    });

    bodyObserver.observe(document.body, { childList: true, subtree: true });
});