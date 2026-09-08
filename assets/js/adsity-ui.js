/**
 * Adsity Reusable UI & Client-Side Utility Library (AdsityUI)
 * 
 * Provides centralized, reusable JavaScript components and helpers for:
 * 1. Tab switching & hash routing across all dashboards (Student, Instructor, Admin)
 * 2. Mobile drawer/sidebar management
 * 3. Accessible modal dialogs (backdrop clicking, Escape key listeners, focus handling)
 * 4. Clipboard copy with button feedback and toast notices
 * 5. Dynamic toast notification system (success, error, warning, info)
 * 6. Client-side table filtering & live search
 * 7. HTML escaping and currency formatting
 * 8. Reusable JSON fetch client
 */
(function (window, document) {
  'use strict';

  // Inject standard styles for floating toasts and modal animations if not already present
  function injectStyles() {
    if (document.getElementById('adsity-ui-styles')) return;
    var style = document.createElement('style');
    style.id = 'adsity-ui-styles';
    style.textContent = `
      .adsity-toast-container {
        position: fixed;
        bottom: 28px;
        right: 28px;
        background-color: #0f172a;
        color: #f8fafc;
        padding: 12px 20px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 600;
        font-family: 'Raleway', system-ui, -apple-system, sans-serif;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999999;
        opacity: 0;
        transform: translateY(16px);
        transition: opacity 0.24s cubic-bezier(0.16, 1, 0.3, 1), transform 0.24s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
      }
      .adsity-toast-container.show {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
      }
      .adsity-toast-container.toast--success {
        background-color: #065f46;
        color: #ecfdf5;
        border: 1px solid #10b981;
      }
      .adsity-toast-container.toast--error {
        background-color: #991b1b;
        color: #fef2f2;
        border: 1px solid #ef4444;
      }
      .adsity-toast-container.toast--warning {
        background-color: #92400e;
        color: #fffbeb;
        border: 1px solid #f59e0b;
      }
      .adsity-toast-container.toast--info {
        background-color: #1e3a8a;
        color: #eff6ff;
        border: 1px solid #3b82f6;
      }
      body.adsity-modal-open {
        overflow: hidden;
      }
    `;
    document.head.appendChild(style);
  }

  var AdsityUI = {
    /**
     * Reusable Tab Switching
     * Switches active tab button and panel, updates URL hash, and notifies listeners.
     */
    switchTab: function (tabId, options) {
      if (!tabId) return;
      options = options || {};

      var btnSelector = options.buttonSelector || '.sidebar-nav-item[data-tab], .nav-tab-btn[data-tab]';
      var panelSelector = options.panelSelector || '.admin-tab-panel, .instructor-tab-panel, .tab-panel';
      var targetPanelId = options.targetPanelId || ('tab-' + tabId);

      // 1. Update buttons
      var buttons = document.querySelectorAll(btnSelector);
      buttons.forEach(function (btn) {
        var btnTab = btn.getAttribute('data-tab') || (btn.dataset ? btn.dataset.tab : null);
        if (btnTab === tabId) {
          btn.classList.add('active');
        } else {
          btn.classList.remove('active');
        }
      });

      // 2. Update panels
      var panels = document.querySelectorAll(panelSelector);
      panels.forEach(function (panel) {
        panel.classList.remove('active');
      });

      var targetPanel = document.getElementById(targetPanelId);
      if (targetPanel) {
        targetPanel.classList.add('active');
      }

      // 3. Update URL hash
      if (options.updateHash !== false) {
        if (history.replaceState) {
          history.replaceState(null, null, '#' + tabId);
        } else {
          location.hash = '#' + tabId;
        }
      }

      // 4. Smooth scroll to top if requested
      if (options.scrollToTop) {
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }

      // 5. Close any open mobile drawer
      if (options.closeMobileSidebar !== false) {
        AdsityUI.closeSidebar('adminSidebar', 'sidebarBackdrop');
        AdsityUI.closeSidebar('instructorSidebar', 'sidebarBackdrop');
      }

      // 6. Dispatch custom event for page-specific hooks
      window.dispatchEvent(new CustomEvent('adsity:tabchange', { detail: { tabId: tabId } }));
    },

    /**
     * Initializes Tab Navigation from URL Hash on Page Load
     */
    initHashRouting: function (allowedTabs, defaultTab, options) {
      function checkHash() {
        var hash = window.location.hash.replace('#', '').trim();
        if (hash && (!allowedTabs || allowedTabs.indexOf(hash) !== -1)) {
          AdsityUI.switchTab(hash, options);
        } else if (defaultTab) {
          AdsityUI.switchTab(defaultTab, options);
        }
      }

      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkHash);
      } else {
        checkHash();
      }

      window.addEventListener('hashchange', function () {
        var hash = window.location.hash.replace('#', '').trim();
        if (hash && (!allowedTabs || allowedTabs.indexOf(hash) !== -1)) {
          AdsityUI.switchTab(hash, options);
        }
      });
    },

    /**
     * Mobile Sidebar / Drawer Management
     */
    toggleSidebar: function (sidebarId, backdropId) {
      sidebarId = sidebarId || 'instructorSidebar';
      backdropId = backdropId || 'sidebarBackdrop';
      var sidebar = document.getElementById(sidebarId);
      var backdrop = document.getElementById(backdropId);
      if (sidebar && backdrop) {
        sidebar.classList.toggle('open');
        backdrop.classList.toggle('open');
      }
    },

    closeSidebar: function (sidebarId, backdropId) {
      sidebarId = sidebarId || 'instructorSidebar';
      backdropId = backdropId || 'sidebarBackdrop';
      var sidebar = document.getElementById(sidebarId);
      var backdrop = document.getElementById(backdropId);
      if (sidebar && backdrop) {
        sidebar.classList.remove('open');
        backdrop.classList.remove('open');
      }
    },

    openSidebar: function (sidebarId, backdropId) {
      sidebarId = sidebarId || 'instructorSidebar';
      backdropId = backdropId || 'sidebarBackdrop';
      var sidebar = document.getElementById(sidebarId);
      var backdrop = document.getElementById(backdropId);
      if (sidebar && backdrop) {
        sidebar.classList.add('open');
        backdrop.classList.add('open');
      }
    },

    /**
     * Accessible Modal Dialog Management
     */
    openModal: function (modalId) {
      var modal = document.getElementById(modalId);
      if (!modal) return;
      modal.style.display = 'flex';
      document.body.classList.add('adsity-modal-open');

      // Autofocus first interactive element
      var focusTarget = modal.querySelector('input:not([type=hidden]):not([disabled]), textarea:not([disabled]), select:not([disabled]), button:not([disabled])');
      if (focusTarget) {
        setTimeout(function () {
          focusTarget.focus();
        }, 60);
      }
    },

    closeModal: function (modalId) {
      var modal = document.getElementById(modalId);
      if (!modal) return;
      modal.style.display = 'none';

      // Only remove class if no other modals remain open
      var remaining = document.querySelectorAll('.admin-modal-backdrop, .modal-backdrop');
      var anyOpen = false;
      remaining.forEach(function (el) {
        if (el.style.display === 'flex' || el.style.display === 'block') {
          anyOpen = true;
        }
      });
      if (!anyOpen) {
        document.body.classList.remove('adsity-modal-open');
      }
    },

    handleBackdropClick: function (event, modalId) {
      if (event && event.target && event.target.id === modalId) {
        AdsityUI.closeModal(modalId);
      }
    },

    /**
     * Copy to Clipboard with Button State and Toast Feedback
     */
    copyToClipboard: function (text, btnElement, successMsg) {
      successMsg = successMsg || 'Copied to clipboard!';

      var onCopied = function () {
        AdsityUI.showToast(successMsg, 'success');
        if (btnElement) {
          var originalText = btnElement.innerText || btnElement.textContent;
          btnElement.innerText = 'Copied! ✓';
          setTimeout(function () {
            btnElement.innerText = originalText;
          }, 2000);
        }
      };

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(onCopied).catch(function () {
          AdsityUI._fallbackCopy(text, onCopied);
        });
      } else {
        AdsityUI._fallbackCopy(text, onCopied);
      }
    },

    _fallbackCopy: function (text, callback) {
      var tempInput = document.createElement('textarea');
      tempInput.value = text;
      tempInput.style.position = 'fixed';
      tempInput.style.opacity = '0';
      document.body.appendChild(tempInput);
      tempInput.select();
      try {
        document.execCommand('copy');
        if (callback) callback();
      } catch (err) {
        AdsityUI.showToast('Failed to copy to clipboard', 'error');
      }
      document.body.removeChild(tempInput);
    },

    /**
     * Toast Notification
     */
    showToast: function (message, type, duration) {
      injectStyles();
      duration = duration || 2800;
      type = type || 'info';

      var toast = document.getElementById('toast-notice');
      var toastText = document.getElementById('toast-text');

      if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast-notice';
        toast.className = 'adsity-toast-container';

        var iconSpan = document.createElement('span');
        iconSpan.id = 'toast-icon';
        iconSpan.style.fontSize = '1.05rem';
        toast.appendChild(iconSpan);

        toastText = document.createElement('span');
        toastText.id = 'toast-text';
        toast.appendChild(toastText);

        document.body.appendChild(toast);
      }

      var icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
      };

      var iconEl = document.getElementById('toast-icon');
      if (iconEl) {
        iconEl.textContent = icons[type] || icons.info;
      }

      if (toastText) {
        toastText.textContent = message;
      }

      // Reset previous classes
      toast.className = 'adsity-toast-container show toast--' + type;

      if (toast._timer) clearTimeout(toast._timer);
      toast._timer = setTimeout(function () {
        toast.classList.remove('show');
      }, duration);
    },

    /**
     * Client-side Table Filter & Search Utility
     */
    filterTableRows: function (options) {
      var rows = document.querySelectorAll(options.rowSelector);
      var query = (options.query || '').trim().toLowerCase();
      var filterValue = (options.filterValue || 'all').toLowerCase();
      var visibleCount = 0;

      rows.forEach(function (row) {
        var matchesQuery = true;
        if (query.length > 0) {
          var searchContent = '';
          if (options.searchAttrs && options.searchAttrs.length) {
            options.searchAttrs.forEach(function (attr) {
              searchContent += ' ' + (row.getAttribute(attr) || '');
            });
          } else {
            searchContent = row.textContent || '';
          }
          matchesQuery = searchContent.toLowerCase().indexOf(query) !== -1;
        }

        var matchesFilter = true;
        if (filterValue !== 'all' && filterValue !== '') {
          var rowFilter = (row.getAttribute(options.filterAttr || 'data-status') || '').toLowerCase();
          matchesFilter = rowFilter === filterValue;
        }

        var isVisible = matchesQuery && matchesFilter;
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
      });

      if (options.emptyStateSelector) {
        var emptyEl = document.querySelector(options.emptyStateSelector);
        if (emptyEl) {
          emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';
        }
      }

      return visibleCount;
    },

    /**
     * Utility: Safe HTML Escaping
     */
    escapeHtml: function (str) {
      if (str === null || str === undefined) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    },

    /**
     * Utility: Format Currency
     */
    formatCurrency: function (num, decimals) {
      decimals = decimals !== undefined ? decimals : 2;
      var n = parseFloat(num) || 0;
      return '$' + n.toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
      });
    },

    /**
     * Utility: Debounce Function for Search Inputs
     */
    debounce: function (func, wait) {
      var timeout;
      return function () {
        var context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function () {
          func.apply(context, args);
        }, wait || 250);
      };
    },

    /**
     * Reusable JSON Fetch Client
     */
    postJson: function (url, payload, options) {
      options = options || {};
      var headers = {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      };

      if (options.csrfToken) {
        headers['X-CSRF-Token'] = options.csrfToken;
      }

      return fetch(url, {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(payload)
      }).then(function (res) {
        if (!res.ok) {
          throw new Error('HTTP error ' + res.status);
        }
        return res.json();
      });
    }
  };

  // Global escape key handler to close any active modal
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
      var openModals = document.querySelectorAll('.admin-modal-backdrop, .modal-backdrop');
      openModals.forEach(function (m) {
        if (m.style.display === 'flex' || m.style.display === 'block') {
          m.style.display = 'none';
        }
      });
      document.body.classList.remove('adsity-modal-open');
    }
  });

  // Expose to window
  window.AdsityUI = AdsityUI;

  // Provide global aliases for backwards compatibility with inline HTML attributes
  if (typeof window.showToast === 'undefined') {
    window.showToast = AdsityUI.showToast;
  }
  if (typeof window.escapeHtml === 'undefined') {
    window.escapeHtml = AdsityUI.escapeHtml;
  }
  if (typeof window.copyToClipboard === 'undefined') {
    window.copyToClipboard = AdsityUI.copyToClipboard;
  }

})(window, document);
