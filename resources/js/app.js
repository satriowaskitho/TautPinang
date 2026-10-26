import "./bootstrap";

// Livewire 3 provides Alpine and the focus plugin through @livewireScripts.
// Seamless Navigation JavaScript untuk LinkMaker BPS
document.addEventListener("DOMContentLoaded", function () {
    // Loading Indicator
    const loadingIndicator = document.createElement("div");
    loadingIndicator.className = "loading-indicator";
    document.body.appendChild(loadingIndicator);

    // Page transition container
    const mainContent = document.querySelector("main");
    if (mainContent) {
        mainContent.classList.add("page-transition");
    }

    // Loading state management
    let isLoading = false;

    function showLoading() {
        if (!isLoading) {
            isLoading = true;
            loadingIndicator.classList.add("active");
            if (mainContent) {
                mainContent.classList.add("loading");
            }
            document.body.style.cursor = "wait";
        }
    }

    function hideLoading() {
        if (isLoading) {
            isLoading = false;
            loadingIndicator.classList.remove("active");
            if (mainContent) {
                mainContent.classList.remove("loading");
            }
            document.body.style.cursor = "default";
        }
    }

    // Livewire Navigation Events
    document.addEventListener("livewire:navigate", function () {
        showLoading();

        // Add subtle page transition
        if (mainContent) {
            mainContent.style.opacity = "0.8";
            mainContent.style.transform = "translateY(10px)";
        }
    });

    document.addEventListener("livewire:navigated", function () {
        setTimeout(() => {
            hideLoading();

            // Reset page transition
            if (mainContent) {
                mainContent.style.opacity = "1";
                mainContent.style.transform = "translateY(0)";
            }

            // Reinitialize components
            initializeComponents();

            // Update active navigation links
            updateActiveNavigation();
        }, 100);
    });

    // Initialize components after navigation
    function initializeComponents() {
        // Reinitialize Flowbite
        if (typeof initFlowbite !== "undefined") {
            initFlowbite();
        }

        // Reinitialize tooltips
        initTooltips();

        // Reinitialize any custom components
        initCustomComponents();
    }

    // Update active navigation links
    function updateActiveNavigation() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll(".sidebar-link");

        navLinks.forEach((link) => {
            const href = link.getAttribute("href");
            if (
                href &&
                currentPath.includes(href.replace(window.location.origin, ""))
            ) {
                link.classList.add("active");
            } else {
                link.classList.remove("active");
            }
        });
    }

    // Initialize tooltips
    function initTooltips() {
        const tooltips = document.querySelectorAll("[data-tooltip]");
        tooltips.forEach((element) => {
            element.addEventListener("mouseenter", showTooltip);
            element.addEventListener("mouseleave", hideTooltip);
        });
    }

    function showTooltip(event) {
        const text = event.target.getAttribute("data-tooltip");
        const tooltip = document.createElement("div");
        tooltip.className = "tooltip";
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: absolute;
            background: #1f2937;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            z-index: 1000;
            pointer-events: none;
            transform: translateY(-100%);
            margin-top: -8px;
        `;

        event.target.appendChild(tooltip);

        // Position tooltip
        const rect = event.target.getBoundingClientRect();
        tooltip.style.left = "50%";
        tooltip.style.transform = "translateX(-50%) translateY(-100%)";
    }

    function hideTooltip(event) {
        const tooltip = event.target.querySelector(".tooltip");
        if (tooltip) {
            tooltip.remove();
        }
    }

    // Initialize custom components
    function initCustomComponents() {
        // Auto-resize textareas
        const textareas = document.querySelectorAll(
            "textarea[data-auto-resize]"
        );
        textareas.forEach((textarea) => {
            textarea.addEventListener("input", function () {
                this.style.height = "auto";
                this.style.height = this.scrollHeight + "px";
            });
        });

        // Copy to clipboard functionality
        const copyButtons = document.querySelectorAll("[data-copy]");
        copyButtons.forEach((button) => {
            button.addEventListener("click", function () {
                const text = this.getAttribute("data-copy");
                navigator.clipboard.writeText(text).then(() => {
                    showToast("Copied to clipboard!", "success");
                });
            });
        });

        // Form validation
        const forms = document.querySelectorAll("form[data-validate]");
        forms.forEach((form) => {
            form.addEventListener("submit", function (e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                }
            });
        });
    }

    // Toast notification system
    function showToast(message, type = "success") {
        const toast = document.createElement("div");
        toast.className = `toast ${type}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${getToastIcon(type)}
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        document.body.appendChild(toast);

        // Show toast
        setTimeout(() => toast.classList.add("show"), 100);

        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    function getToastIcon(type) {
        const icons = {
            success:
                '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
            error: '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
            warning:
                '<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>',
        };
        return icons[type] || icons.success;
    }

    // Form validation
    function validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll(
            "input[required], textarea[required], select[required]"
        );

        inputs.forEach((input) => {
            if (!input.value.trim()) {
                showFieldError(input, "This field is required");
                isValid = false;
            } else {
                clearFieldError(input);
            }
        });

        return isValid;
    }

    function showFieldError(field, message) {
        clearFieldError(field);

        field.classList.add("border-red-500");
        const error = document.createElement("p");
        error.className = "mt-1 text-sm text-red-500 field-error";
        error.textContent = message;
        field.parentNode.appendChild(error);
    }

    function clearFieldError(field) {
        field.classList.remove("border-red-500");
        const error = field.parentNode.querySelector(".field-error");
        if (error) {
            error.remove();
        }
    }

    // Keyboard shortcuts
    document.addEventListener("keydown", function (e) {
        // Ctrl/Cmd + K untuk search
        if ((e.ctrlKey || e.metaKey) && e.key === "k") {
            e.preventDefault();
            const searchInput = document.querySelector("[data-search]");
            if (searchInput) {
                searchInput.focus();
            }
        }

        // Escape untuk close modals
        if (e.key === "Escape") {
            const modals = document.querySelectorAll(
                '.modal[style*="display: block"]'
            );
            modals.forEach((modal) => {
                modal.style.display = "none";
            });
        }
    });

    // Auto-save functionality
    function setupAutoSave() {
        const forms = document.querySelectorAll("form[data-auto-save]");
        forms.forEach((form) => {
            const inputs = form.querySelectorAll("input, textarea, select");
            inputs.forEach((input) => {
                input.addEventListener(
                    "input",
                    debounce(() => {
                        saveFormData(form);
                    }, 1000)
                );
            });
        });
    }

    function saveFormData(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        localStorage.setItem(`form_${form.id}`, JSON.stringify(data));
        showToast("Draft saved automatically", "success");
    }

    // Debounce utility
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Progress tracking
    function updateProgress(current, total) {
        const progressBars = document.querySelectorAll(".progress-fill");
        const percentage = (current / total) * 100;

        progressBars.forEach((bar) => {
            bar.style.width = `${percentage}%`;
        });
    }

    // Initialize everything
    updateActiveNavigation();
    initializeComponents();
    setupAutoSave();

    // Make functions globally available
    window.LinkMaker = {
        showToast,
        showLoading,
        hideLoading,
        updateProgress,
        validateForm,
    };
});

// Service Worker untuk caching (opsional)
// if ("serviceWorker" in navigator) {
//     window.addEventListener("load", function () {
//         navigator.serviceWorker
//             .register("/sw.js")
//             .then(function (registration) {
//                 console.log("SW registered: ", registration);
//             })
//             .catch(function (registrationError) {
//                 console.log("SW registration failed: ", registrationError);
//             });
//     });
// }
