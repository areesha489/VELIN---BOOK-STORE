(function () {
    var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    var fine = window.matchMedia("(pointer: fine)").matches;
    var toggle = document.querySelector("[data-nav-toggle]");
    var panel = document.querySelector("[data-nav-panel]");
    var header = document.querySelector(".site-header");

    if (toggle && panel) {
        toggle.addEventListener("click", function () {
            var open = panel.classList.toggle("is-open");
            toggle.setAttribute("aria-expanded", open ? "true" : "false");
        });
    }

    if (header) {
        var syncHeader = function () {
            header.classList.toggle("is-scrolled", window.scrollY > 12);
        };
        syncHeader();
        window.addEventListener("scroll", syncHeader, { passive: true });
    }

    document.addEventListener("submit", function (event) {
        var form = event.target;
        if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) {
            return;
        }
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });

    document.querySelectorAll("[data-auto-submit]").forEach(function (form) {
        form.querySelectorAll("select").forEach(function (select) {
            select.addEventListener("change", function () {
                if (typeof form.requestSubmit === "function") {
                    form.requestSubmit();
                }
            });
        });
    });

    var tilt = document.querySelector("[data-tilt]");
    if (tilt && fine && !reduce) {
        tilt.addEventListener("pointermove", function (event) {
            var rect = tilt.getBoundingClientRect();
            var x = (event.clientX - rect.left) / rect.width - 0.5;
            var y = (event.clientY - rect.top) / rect.height - 0.5;
            tilt.style.transform = "rotateY(" + (x * 10) + "deg) rotateX(" + (y * -7) + "deg)";
        });
        tilt.addEventListener("pointerleave", function () {
            tilt.style.transform = "";
        });
    }

    var detailCover = document.querySelector(".detail-cover");
    if (detailCover && fine && !reduce) {
        detailCover.addEventListener("pointermove", function (event) {
            var rect = detailCover.getBoundingClientRect();
            var x = (event.clientX - rect.left) / rect.width - 0.5;
            var y = (event.clientY - rect.top) / rect.height - 0.5;
            detailCover.style.transform = "translate3d(" + (x * 8) + "px, " + (y * 6) + "px, 0)";
        });
        detailCover.addEventListener("pointerleave", function () {
            detailCover.style.transform = "";
        });
    }

    if (!reduce) {
        document.querySelectorAll("[data-countup]").forEach(function (node) {
            var target = Number(node.getAttribute("data-countup"));
            if (!Number.isFinite(target)) {
                return;
            }
            var prefix = node.getAttribute("data-prefix") || "";
            var started = performance.now();
            var duration = 700;
            function frame(now) {
                var progress = Math.min(1, (now - started) / duration);
                var eased = 1 - Math.pow(1 - progress, 3);
                var value = Math.round(target * eased);
                node.textContent = prefix + value.toLocaleString("en-IN");
                if (progress < 1) {
                    requestAnimationFrame(frame);
                }
            }
            requestAnimationFrame(frame);
        });
    }

    if (reduce || !window.gsap || !window.ScrollTrigger) {
        return;
    }

    window.gsap.registerPlugin(window.ScrollTrigger);

    window.gsap.utils.toArray("[data-reveal]").forEach(function (el) {
        var cards = el.querySelectorAll(".shelf .book-card, .rail .book-card, [data-stagger] > .book-card");
        if (cards.length) {
            window.gsap.from(cards, {
                y: 36,
                opacity: 0,
                duration: 0.75,
                stagger: 0.08,
                ease: "power3.out",
                clearProps: "transform,opacity",
                scrollTrigger: { trigger: el, start: "top 86%" },
            });
            var head = el.querySelector(".section-head, .page-head, .rail-hint");
            if (head) {
                window.gsap.from(head, {
                    y: 20,
                    opacity: 0,
                    duration: 0.7,
                    ease: "power3.out",
                    clearProps: "transform,opacity",
                    scrollTrigger: { trigger: el, start: "top 88%" },
                });
            }
            return;
        }

        window.gsap.from(el, {
            y: 32,
            opacity: 0,
            duration: 0.9,
            ease: "power3.out",
            clearProps: "transform,opacity",
            scrollTrigger: { trigger: el, start: "top 86%" },
        });
    });

    window.gsap.utils.toArray("[data-stagger]:not([data-reveal])").forEach(function (el) {
        var cards = el.querySelectorAll(".book-card, .cart-line, .order-row, .panel.method, .empty-panel");
        if (!cards.length) {
            return;
        }
        window.gsap.from(cards, {
            y: 28,
            opacity: 0,
            duration: 0.7,
            stagger: 0.07,
            ease: "power3.out",
            clearProps: "transform,opacity",
            scrollTrigger: { trigger: el, start: "top 88%" },
        });
    });

    window.gsap.utils.toArray(".genre-index a").forEach(function (el, index) {
        window.gsap.from(el, {
            y: 18,
            opacity: 0,
            duration: 0.6,
            delay: Math.min(index * 0.04, 0.4),
            ease: "power3.out",
            clearProps: "transform,opacity",
            scrollTrigger: { trigger: el, start: "top 92%" },
        });
    });

    var detail = document.querySelector(".detail");
    if (detail) {
        var cover = detail.querySelector(".detail-cover");
        var copy = detail.querySelector(".detail-copy");
        if (cover) {
            window.gsap.from(cover, {
                y: 28,
                opacity: 0,
                duration: 0.9,
                ease: "power3.out",
                clearProps: "transform,opacity",
            });
        }
        if (copy) {
            window.gsap.from(copy, {
                y: 24,
                opacity: 0,
                duration: 0.85,
                delay: 0.08,
                ease: "power3.out",
                clearProps: "transform,opacity",
            });
        }
    }

    var ghost = document.querySelector(".hero-ghost");
    if (ghost) {
        window.gsap.to(ghost, {
            y: 70,
            ease: "none",
            scrollTrigger: { trigger: ".hero", start: "top bottom", end: "bottom top", scrub: true },
        });
    }
})();
