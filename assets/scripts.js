
(function () {
  'use strict';

  // Mobiles Menü
  const menuBtn = document.querySelector('.site-nav .menu-btn');
  const navLinks = document.querySelector('.site-nav .nav-links');

  function closeMenu() {
    if (navLinks) navLinks.classList.remove('open');
  }

  if (menuBtn && navLinks) {
    menuBtn.addEventListener('click', function () {
      navLinks.classList.toggle('open');
    });
    navLinks.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', closeMenu);
    });
    document.addEventListener('click', function (e) {
      if (!e.target.closest('.site-nav')) closeMenu();
    });
  }

  // Aktiven Nav-Link markieren
  const current = window.location.pathname.split('/').filter(Boolean).pop() || 'index.html';
  document.querySelectorAll('.site-nav .nav-links a').forEach(function (a) {
    const href = a.getAttribute('href');
    if (href && (href.endsWith(current) || (current === '' && href.includes('index')))) {
      a.classList.add('active');
    }
  });

  // Impressum / DSGVO Modal
  const modalTriggers = document.querySelectorAll('[data-modal]');
  const modals = document.querySelectorAll('.modal-overlay');

  function closeAllModals() {
    modals.forEach(function (m) { m.classList.remove('open'); });
  }

  modalTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.getElementById(trigger.getAttribute('data-modal'));
      if (target) target.classList.add('open');
    });
  });

  modals.forEach(function (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeAllModals();
    });
    const closeBtn = modal.querySelector('.modal-close');
    if (closeBtn) closeBtn.addEventListener('click', closeAllModals);
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAllModals();
  });
})();
