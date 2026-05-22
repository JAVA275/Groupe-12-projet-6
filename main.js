// public/js/main.js

document.addEventListener('DOMContentLoaded', () => {

  // ── Auto-dismiss flash après 5s ──────────────────
  const flash = document.getElementById('flash');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity .5s ease, transform .5s ease';
      flash.style.opacity = '0';
      flash.style.transform = 'translateY(-10px)';
      setTimeout(() => flash.remove(), 500);
    }, 5000);
  }

  // ── Confirmations suppression ─────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm || 'Confirmer cette action ?')) e.preventDefault();
    });
  });

  // ── Scroll Reveal ────────────────────────────────
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        setTimeout(() => {
          entry.target.classList.add('visible');
        }, i * 80);
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

  // ── Stagger animation pour les cartes ───────────
  document.querySelectorAll('.sujet-card, .stat-card').forEach((card, i) => {
    card.style.animationDelay = (i * 0.07) + 's';
    card.classList.add('slide-up');
  });

  // ── Preview avatar avant upload ──────────────────
  const avatarInput = document.getElementById('avatar-input');
  const avatarPreview = document.getElementById('avatar-preview');
  if (avatarInput && avatarPreview) {
    avatarInput.addEventListener('change', () => {
      const file = avatarInput.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          avatarPreview.src = e.target.result;
          avatarPreview.style.animation = 'scaleIn .4s ease both';
        };
        reader.readAsDataURL(file);
      }
    });
  }

  // ── Star rating auto-submit ──────────────────────
  document.querySelectorAll('.star-rating input').forEach(input => {
    input.addEventListener('change', () => {
      const form = input.closest('form[data-autosubmit]');
      if (form) {
        setTimeout(() => form.submit(), 300);
      }
    });
  });

  // ── Animation nombre stat (count-up) ────────────
  document.querySelectorAll('.stat-card__value').forEach(el => {
    const target = parseInt(el.textContent.replace(/\D/g, '')) || 0;
    if (target === 0) return;
    let current = 0;
    const step = Math.ceil(target / 40);
    const timer = setInterval(() => {
      current = Math.min(current + step, target);
      el.textContent = current;
      if (current >= target) clearInterval(timer);
    }, 35);
  });

  // ── Navbar active link ───────────────────────────
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav-link').forEach(link => {
    if (link.getAttribute('href') === currentPath) {
      link.classList.add('active');
    }
  });
  // Toggle visibilite mot de passe
  document.querySelectorAll('.toggle-password').forEach(function(btn) {
    btn.addEventListener('click', function () {
      var targetId = btn.getAttribute('data-target');
      var input = document.getElementById(targetId);
      if (!input) return;
      if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
      } else {
        input.type = 'password';
        btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
      }
    });
  });

});
