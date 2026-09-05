/**
 * SpondonIt Installer — Vanilla JavaScript
 *
 * Self-contained JS for the install wizard. No jQuery, no Alpine, no dependencies.
 * Handles: AJAX form submission, toast notifications, form validation,
 * modals, clipboard copy, DB connection toggle, and preloader.
 */
(function () {
    'use strict';

    /* ── Helpers ───────────────────────────────────────────────────────── */

    var csrfToken = function () {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    };

    /* ── Toast Notifications ──────────────────────────────────────────── */

    var toastContainer = null;

    function ensureToastContainer() {
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
        }
        return toastContainer;
    }

    var TOAST_ICONS = {
        success: '<svg viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>',
        error: '<svg viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>',
        warning: '<svg viewBox="0 0 20 20"><path d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/></svg>'
    };

    function showToast(message, type) {
        type = type || 'success';
        var container = ensureToastContainer();
        var el = document.createElement('div');
        el.className = 'toast toast-' + type;
        el.innerHTML = (TOAST_ICONS[type] || '') + '<span>' + escapeHtml(message) + '</span>';
        container.appendChild(el);

        setTimeout(function () {
            el.classList.add('removing');
            setTimeout(function () {
                if (el.parentNode) el.parentNode.removeChild(el);
            }, 300);
        }, 4500);
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }

    /* ── Preloader ────────────────────────────────────────────────────── */

    var preloader = null;
    var preloaderText = null;

    function showPreloader(message) {
        preloader = document.getElementById('preloader');
        if (!preloader) return;
        preloaderText = preloader.querySelector('.preloader-text');
        if (preloaderText && message) {
            preloaderText.textContent = message;
        }
        preloader.classList.add('active');
    }

    function hidePreloader() {
        preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.classList.remove('active');
        }
    }

    /* ── Form Validation ──────────────────────────────────────────────── */

    function clearErrors(form) {
        var errorEls = form.querySelectorAll('.form-error');
        for (var i = 0; i < errorEls.length; i++) {
            errorEls[i].textContent = '';
        }
        var hasErrors = form.querySelectorAll('.has-error');
        for (var j = 0; j < hasErrors.length; j++) {
            hasErrors[j].classList.remove('has-error');
        }
    }

    function showFieldError(form, fieldName, message) {
        var field = form.querySelector('[name="' + fieldName + '"]');
        if (!field) return;
        field.classList.add('has-error');

        var errorEl = field.parentNode.querySelector('.form-error');
        if (!errorEl) {
            errorEl = document.createElement('span');
            errorEl.className = 'form-error';
            field.parentNode.appendChild(errorEl);
        }
        errorEl.textContent = message;
    }

    function validateMatchFields(form) {
        var matches = form.querySelectorAll('[data-match]');
        var valid = true;
        for (var i = 0; i < matches.length; i++) {
            var el = matches[i];
            var target = form.querySelector(el.getAttribute('data-match'));
            if (target && el.value !== target.value) {
                el.classList.add('has-error');
                var errorEl = el.parentNode.querySelector('.form-error');
                if (!errorEl) {
                    errorEl = document.createElement('span');
                    errorEl.className = 'form-error';
                    el.parentNode.appendChild(errorEl);
                }
                errorEl.textContent = 'Passwords do not match';
                valid = false;
            }
        }
        return valid;
    }

    /* ── AJAX Form Submission ─────────────────────────────────────────── */

    function initForms() {
        var forms = document.querySelectorAll('[data-ajax-form]');

        for (var i = 0; i < forms.length; i++) {
            (function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    clearErrors(form);

                    // HTML5 validation
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    // Custom match validation (passwords)
                    if (!validateMatchFields(form)) {
                        return;
                    }

                    var submitBtn = form.querySelector('.btn-submit');
                    var loadingBtn = form.querySelector('.btn-loading');
                    var preloaderMsg = form.getAttribute('data-preloader-message');

                    // Show loading
                    if (submitBtn) submitBtn.classList.add('hidden');
                    if (loadingBtn) loadingBtn.classList.remove('hidden');
                    if (preloaderMsg) showPreloader(preloaderMsg);

                    var formData = new FormData(form);

                    fetch(form.getAttribute('action'), {
                        method: form.getAttribute('method') || 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken(),
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(function (response) {
                        return response.json().then(function (data) {
                            return { ok: response.ok, status: response.status, data: data };
                        });
                    })
                    .then(function (result) {
                        if (result.ok) {
                            if (result.data.message) {
                                showToast(result.data.message, 'success');
                            }
                            if (result.data.goto) {
                                window.location.href = result.data.goto;
                                return;
                            }
                            if (result.data.reload) {
                                window.location.href = '';
                                return;
                            }
                            // Reset loading
                            if (submitBtn) submitBtn.classList.remove('hidden');
                            if (loadingBtn) loadingBtn.classList.add('hidden');
                            hidePreloader();
                        } else {
                            handleErrors(form, result.data, result.status);
                            if (submitBtn) submitBtn.classList.remove('hidden');
                            if (loadingBtn) loadingBtn.classList.add('hidden');
                            hidePreloader();
                        }
                    })
                    .catch(function () {
                        showToast('Something went wrong. Please try again.', 'error');
                        if (submitBtn) submitBtn.classList.remove('hidden');
                        if (loadingBtn) loadingBtn.classList.add('hidden');
                        hidePreloader();
                    });
                });
            })(forms[i]);
        }
    }

    function handleErrors(form, data, status) {
        if (status === 404) {
            showToast('The requested resource was not found.', 'error');
            return;
        }
        if (status === 500) {
            showToast('Something went wrong. Please contact support.', 'error');
            return;
        }

        if (data.errors) {
            var keys = Object.keys(data.errors);
            for (var i = 0; i < keys.length; i++) {
                var messages = data.errors[keys[i]];
                var msg = Array.isArray(messages) ? messages[0] : messages;
                showFieldError(form, keys[i], msg);
                showToast(msg, 'error');
            }
        } else if (data.message) {
            showToast(data.message, 'error');
        }
    }

    /* ── Modal ────────────────────────────────────────────────────────── */

    function initModals() {
        // Open triggers
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-modal-open]');
            if (trigger) {
                e.preventDefault();
                var id = trigger.getAttribute('data-modal-open');
                var overlay = document.getElementById(id);
                if (overlay) overlay.classList.add('active');
            }
        });

        // Close triggers
        document.addEventListener('click', function (e) {
            var trigger = e.target.closest('[data-modal-close]');
            if (trigger) {
                e.preventDefault();
                var overlay = trigger.closest('.modal-overlay');
                if (overlay) overlay.classList.remove('active');
            }
        });

        // Close on overlay background click
        document.addEventListener('click', function (e) {
            if (e.target.classList && e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
            }
        });

        // Close on Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                var overlays = document.querySelectorAll('.modal-overlay.active');
                for (var i = 0; i < overlays.length; i++) {
                    overlays[i].classList.remove('active');
                }
            }
        });
    }

    /* ── Modal form submission (revoke/module/theme) ──────────────────── */

    function initModalForms() {
        var modalForms = document.querySelectorAll('[data-modal-form]');

        for (var i = 0; i < modalForms.length; i++) {
            (function (form) {
                form.addEventListener('submit', function (e) {
                    var msgEl = form.querySelector('[data-modal-message]');
                    var cancelBtn = form.querySelector('[data-modal-close]');
                    var submitBtn = form.querySelector('.btn-submit');
                    var loadingBtn = form.querySelector('.btn-loading');

                    if (msgEl) {
                        msgEl.textContent = msgEl.getAttribute('data-loading-message') ||
                            'Please wait. Do not refresh or close the browser.';
                    }
                    if (cancelBtn) cancelBtn.disabled = true;
                    if (submitBtn) submitBtn.classList.add('hidden');
                    if (loadingBtn) loadingBtn.classList.remove('hidden');
                });
            })(modalForms[i]);
        }
    }

    /* ── Clipboard Copy ───────────────────────────────────────────────── */

    function initClipboard() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-clipboard]');
            if (!btn) return;

            var text = btn.getAttribute('data-clipboard');
            var originalLabel = btn.querySelector('.copy-label');

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function () {
                    markCopied(btn, originalLabel);
                });
            } else {
                // Fallback
                var ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                markCopied(btn, originalLabel);
            }
        });
    }

    function markCopied(btn, labelEl) {
        btn.classList.add('copied');
        if (labelEl) {
            var origText = labelEl.textContent;
            labelEl.textContent = 'Copied!';
            setTimeout(function () {
                btn.classList.remove('copied');
                labelEl.textContent = origText;
            }, 2000);
        }
    }

    /* ── DB Connection Toggle ─────────────────────────────────────────── */

    function initDbToggle() {
        var select = document.getElementById('db_connection');
        var port = document.getElementById('db_port');
        if (!select || !port) return;

        select.addEventListener('change', function () {
            port.value = (select.value === 'pgsql') ? '5432' : '3306';
        });
    }

    /* ── Session Flash Toast ──────────────────────────────────────────── */

    function initSessionToast() {
        var el = document.getElementById('session-flash');
        if (!el) return;

        var message = el.getAttribute('data-message');
        var type = el.getAttribute('data-type') || 'error';

        if (message) {
            showToast(message, type);
        }
    }

    /* ── Password Visibility Toggle ───────────────────────────────────── */

    function initPasswordToggle() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-toggle-password]');
            if (!btn) return;

            var targetId = btn.getAttribute('data-toggle-password');
            var input = document.getElementById(targetId);
            if (!input) return;

            if (input.type === 'password') {
                input.type = 'text';
                btn.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                btn.setAttribute('aria-label', 'Show password');
            }
        });
    }

    /* ── Init ─────────────────────────────────────────────────────────── */

    function init() {
        initForms();
        initModals();
        initModalForms();
        initClipboard();
        initDbToggle();
        initSessionToast();
        initPasswordToggle();
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose toast for external use
    window.InstallerToast = showToast;

})();

