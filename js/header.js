
/* =====================================================
   REAL ESTATE HEADER - MOBILE NAVIGATION
===================================================== */

document.addEventListener("DOMContentLoaded", () => {
    const menuToggle = document.getElementById("reMenuToggle");
    const drawer = document.getElementById("reMobileDrawer");
    const overlay = document.getElementById("reMobileOverlay");
    const closeButton = document.getElementById("reDrawerClose");

    if (!menuToggle || !drawer || !overlay || !closeButton) {
        return;
    }

    const mobileBreakpoint = window.matchMedia("(max-width: 991.98px)");
    const submenuToggles = drawer.querySelectorAll(
        ".re-mobile-submenu-toggle"
    );

    let previousFocus = null;

    /* Open Mobile Drawer */

    function openDrawer() {
        if (!mobileBreakpoint.matches) return;

        previousFocus = document.activeElement;

        drawer.classList.add("is-open");
        overlay.classList.add("is-visible");
        document.body.classList.add("re-menu-open");

        drawer.setAttribute("aria-hidden", "false");
        drawer.removeAttribute("inert");

        menuToggle.setAttribute("aria-expanded", "true");
        menuToggle.setAttribute("aria-label", "Close navigation menu");

        closeButton.focus();
    }

    /* Close Mobile Drawer */

    function closeDrawer(restoreFocus = true) {
        drawer.classList.remove("is-open");
        overlay.classList.remove("is-visible");
        document.body.classList.remove("re-menu-open");

        drawer.setAttribute("aria-hidden", "true");
        drawer.setAttribute("inert", "");

        menuToggle.setAttribute("aria-expanded", "false");
        menuToggle.setAttribute("aria-label", "Open navigation menu");

        closeSubmenus();

        if (restoreFocus && previousFocus) {
            previousFocus.focus();
        }
    }

    /* Close All Submenus */

    function closeSubmenus() {
        submenuToggles.forEach((button) => {
            const submenuId = button.getAttribute("aria-controls");
            const submenu = document.getElementById(submenuId);
            const parent = button.closest(".re-mobile-has-dropdown");

            button.setAttribute("aria-expanded", "false");

            if (submenu) {
                submenu.hidden = true;
            }

            if (parent) {
                parent.classList.remove("is-expanded");
            }
        });
    }

    /* Toggle Individual Mobile Submenus */

    submenuToggles.forEach((button) => {
        button.addEventListener("click", () => {
            const submenuId = button.getAttribute("aria-controls");
            const submenu = document.getElementById(submenuId);
            const parent = button.closest(".re-mobile-has-dropdown");

            if (!submenu) return;

            const shouldOpen =
                button.getAttribute("aria-expanded") !== "true";

            button.setAttribute(
                "aria-expanded",
                String(shouldOpen)
            );

            submenu.hidden = !shouldOpen;

            if (parent) {
                parent.classList.toggle("is-expanded", shouldOpen);
            }
        });
    });

    /* Header Controls */

    menuToggle.addEventListener("click", () => {
        if (drawer.classList.contains("is-open")) {
            closeDrawer();
        } else {
            openDrawer();
        }
    });

    closeButton.addEventListener("click", () => {
        closeDrawer();
    });

    overlay.addEventListener("click", () => {
        closeDrawer();
    });

    /* Close After Clicking a Navigation Link */

    drawer.querySelectorAll("a").forEach((link) => {
        link.addEventListener("click", () => {
            closeDrawer(false);
        });
    });

    /* Escape Key */

    document.addEventListener("keydown", (event) => {
        if (
            event.key === "Escape" &&
            drawer.classList.contains("is-open")
        ) {
            closeDrawer();
            menuToggle.focus();
        }
    });

    /* Close Drawer When Switching to Desktop */

    function handleBreakpointChange(event) {
        if (!event.matches && drawer.classList.contains("is-open")) {
            closeDrawer(false);
        }
    }

    if (mobileBreakpoint.addEventListener) {
        mobileBreakpoint.addEventListener(
            "change",
            handleBreakpointChange
        );
    } else {
        mobileBreakpoint.addListener(handleBreakpointChange);
    }
});
