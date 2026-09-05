/**
 * Adsity Global Logout Confirmation Modal
 * Premium, bulletproof confirmation dialog for all logout actions across the platform.
 */
(function () {
  'use strict';

  var currentLogoutUrl = 'logout.php';

  // Injected CSS guarantees styles are immediately applied without browser cache delays
  var modalStyles = `
    .adsity-logout-backdrop {
      position: fixed;
      inset: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(15, 23, 42, 0.65);
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      z-index: 999999;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 16px;
      box-sizing: border-box;
      opacity: 0;
      transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .adsity-logout-backdrop.is-active {
      opacity: 1;
    }

    .adsity-logout-dialog {
      background-color: #ffffff;
      border-radius: 16px;
      width: 100%;
      max-width: 440px;
      box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.35), 0 0 0 1px rgba(0, 0, 0, 0.08);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      box-sizing: border-box;
      transform: scale(0.95) translateY(8px);
      transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
      font-family: 'Raleway', system-ui, -apple-system, sans-serif;
    }

    .adsity-logout-dialog * {
      font-family: 'Raleway', system-ui, -apple-system, sans-serif;
    }

    .adsity-logout-backdrop.is-active .adsity-logout-dialog {
      transform: scale(1) translateY(0);
    }

    .adsity-logout-header {
      padding: 18px 22px 16px;
      border-bottom: 1px solid #f1f5f9;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background-color: #ffffff;
      box-sizing: border-box;
    }

    .adsity-logout-title-group {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .adsity-logout-badge {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background-color: #fef2f2;
      color: #ef4444;
      border: 1px solid #fee2e2;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .adsity-logout-badge svg {
      width: 18px;
      height: 18px;
      stroke: currentColor;
    }

    .adsity-logout-title {
      font-size: 1.15rem;
      font-weight: 800;
      color: #0f172a;
      margin: 0;
      line-height: 1.25;
      letter-spacing: -0.01em;
    }

    .adsity-logout-close-btn {
      background: none;
      border: none;
      font-size: 24px;
      line-height: 1;
      color: #94a3b8;
      cursor: pointer;
      padding: 4px 6px;
      border-radius: 6px;
      transition: color 0.15s ease, background-color 0.15s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .adsity-logout-close-btn:hover {
      color: #0f172a;
      background-color: #f1f5f9;
    }

    .adsity-logout-body {
      padding: 22px 24px;
      background-color: #ffffff;
      box-sizing: border-box;
    }

    .adsity-logout-message {
      font-size: 0.96rem;
      color: #1e293b;
      line-height: 1.5;
      margin: 0 0 8px 0;
      font-weight: 700;
    }

    .adsity-logout-subtext {
      font-size: 0.86rem;
      color: #64748b;
      line-height: 1.5;
      margin: 0;
    }

    .adsity-logout-footer {
      padding: 16px 24px;
      border-top: 1px solid #f1f5f9;
      background-color: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 10px;
      box-sizing: border-box;
    }

    .adsity-logout-btn {
      padding: 10px 18px;
      border-radius: 9px;
      font-size: 0.9rem;
      font-weight: 700;
      font-family: inherit;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      transition: all 0.15s ease;
      border: none;
      box-sizing: border-box;
    }

    .adsity-logout-btn-cancel {
      background-color: #ffffff;
      color: #475569;
      border: 1px solid #cbd5e1;
    }

    .adsity-logout-btn-cancel:hover {
      background-color: #f1f5f9;
      color: #0f172a;
      border-color: #94a3b8;
    }

    .adsity-logout-btn-confirm {
      background-color: #ef4444;
      color: #ffffff !important;
      box-shadow: 0 3px 10px rgba(239, 68, 68, 0.28);
    }

    .adsity-logout-btn-confirm:hover {
      background-color: #dc2626;
      box-shadow: 0 4px 14px rgba(239, 68, 68, 0.38);
      transform: translateY(-1px);
    }

    .adsity-logout-btn-confirm:active {
      transform: translateY(0);
    }

    .adsity-logout-btn-confirm svg {
      width: 16px;
      height: 16px;
      stroke: currentColor;
      pointer-events: none;
    }
  `;

  function injectStyles() {
    if (!document.getElementById('adsityLogoutModalStyles')) {
      var styleEl = document.createElement('style');
      styleEl.id = 'adsityLogoutModalStyles';
      styleEl.textContent = modalStyles;
      document.head.appendChild(styleEl);
    }
  }

  function getOrBuildModal() {
    injectStyles();
    var modal = document.getElementById('adsityLogoutModal');
    if (modal) return modal;

    modal = document.createElement('div');
    modal.id = 'adsityLogoutModal';
    modal.className = 'adsity-logout-backdrop';
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-labelledby', 'adsityLogoutTitle');

    modal.innerHTML = `
      <div class="adsity-logout-dialog" role="document">
        <div class="adsity-logout-header">
          <div class="adsity-logout-title-group">
            <div class="adsity-logout-badge">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
            </div>
            <h3 id="adsityLogoutTitle" class="adsity-logout-title">Sign Out Confirmation</h3>
          </div>
          <button type="button" class="adsity-logout-close-btn" id="adsityLogoutCloseBtn" aria-label="Close dialog">&times;</button>
        </div>
        <div class="adsity-logout-body">
          <p class="adsity-logout-message">Are you sure you want to log out of Adsity?</p>
          <p class="adsity-logout-subtext">You will be securely signed out of your account. Any active sessions or unsaved form changes will be closed.</p>
        </div>
        <div class="adsity-logout-footer">
          <button type="button" class="adsity-logout-btn adsity-logout-btn-cancel" id="adsityLogoutCancelBtn">Stay Logged In</button>
          <button type="button" class="adsity-logout-btn adsity-logout-btn-confirm" id="adsityLogoutConfirmBtn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
              <polyline points="16 17 21 12 16 7"></polyline>
              <line x1="21" y1="12" x2="9" y2="12"></line>
            </svg>
            <span>Log Out</span>
          </button>
        </div>
      </div>
    `;

    document.body.appendChild(modal);

    // Event listeners
    var closeBtn = modal.querySelector('#adsityLogoutCloseBtn');
    var cancelBtn = modal.querySelector('#adsityLogoutCancelBtn');
    var confirmBtn = modal.querySelector('#adsityLogoutConfirmBtn');

    function closeModal() {
      modal.classList.remove('is-active');
      modal.setAttribute('aria-hidden', 'true');
      setTimeout(function () {
        modal.style.display = 'none';
        document.body.style.overflow = '';
      }, 150);
    }

    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (e) {
      if (e.target === modal) {
        closeModal();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && modal.style.display === 'flex') {
        closeModal();
      }
    });

    if (confirmBtn) {
      confirmBtn.addEventListener('click', function (e) {
        e.preventDefault();
        confirmBtn.disabled = true;
        confirmBtn.style.opacity = '0.7';
        confirmBtn.style.pointerEvents = 'none';
        confirmBtn.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="animation: spin 1s linear infinite;">
            <line x1="12" y1="2" x2="12" y2="6"></line>
            <line x1="12" y1="18" x2="12" y2="22"></line>
            <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
            <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
            <line x1="2" y1="12" x2="6" y2="12"></line>
            <line x1="18" y1="12" x2="22" y2="12"></line>
            <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
            <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
          </svg>
          <span>Signing Out...</span>
        `;
        window.location.href = currentLogoutUrl;
      });
    }

    return modal;
  }

  function openLogoutModal(url) {
    if (url) {
      currentLogoutUrl = url;
    }
    var modal = getOrBuildModal();
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    // Trigger transition
    requestAnimationFrame(function () {
      modal.classList.add('is-active');
      modal.setAttribute('aria-hidden', 'false');
      var cancelBtn = modal.querySelector('#adsityLogoutCancelBtn');
      if (cancelBtn) cancelBtn.focus();
    });
  }

  // Intercept all logout clicks across the page using capture phase
  document.addEventListener('click', function (e) {
    var link = e.target.closest('a[href*="logout.php"], [data-logout-trigger]');
    if (!link) return;

    // Do not intercept if clicked inside the modal itself
    if (link.closest('#adsityLogoutModal')) return;

    e.preventDefault();
    e.stopPropagation();

    // Determine the exact URL from the clicked element
    var url = link.href || link.getAttribute('href') || 'logout.php';
    openLogoutModal(url);
  }, true); // Use capture phase so no inner or inline handlers can swallow it

  // Strip inline onclick confirm on DOM load
  function cleanupInlineOnclick() {
    var links = document.querySelectorAll('a[href*="logout.php"]');
    for (var i = 0; i < links.length; i++) {
      links[i].removeAttribute('onclick');
      links[i].onclick = null;
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', cleanupInlineOnclick);
  } else {
    cleanupInlineOnclick();
  }

  // Export global trigger for convenience
  window.adsityOpenLogoutModal = openLogoutModal;
})();
