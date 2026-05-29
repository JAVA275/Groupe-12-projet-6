// public/js/main.js

// Attendre que tout le DOM soit chargé avant d’exécuter le code
document.addEventListener('DOMContentLoaded', () => {

    // ── Auto-dismiss flash après 5s ──────────────────
    // Récupère l’élément avec l’id "flash" (message de notification)
    const flash = document.getElementById('flash');
    if (flash) {
        // Programmer la disparition après 5 secondes
        setTimeout(() => {
            // Ajouter une transition CSS pour l’opacité et la translation
            flash.style.transition = 'opacity .5s ease, transform .5s ease';
            flash.style.opacity = '0';               // Rendre transparent
            flash.style.transform = 'translateY(-10px)'; // Glisser vers le haut
            // Après 0.5s (durée de la transition), supprimer l’élément du DOM
            setTimeout(() => flash.remove(), 500);
        }, 5000); // 5000 ms = 5 secondes
    }

    // ── Confirmations suppression ─────────────────────
    // Cherche tous les éléments qui ont l’attribut `data-confirm`
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', e => {
            // Affiche une boîte de confirmation avec le texte de l’attribut (ou un message par défaut)
            if (!confirm(el.dataset.confirm || 'Confirmer cette action ?')) {
                e.preventDefault(); // Annule l’action si l’utilisateur clique sur "Annuler"
            }
        });
    });

    // ── Scroll Reveal (apparition au défilement) ────
    // Crée un observateur d’intersection (détecte quand un élément entre dans la zone visible)
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {  // Si l’élément devient visible
                // Ajoute un délai progressif (i * 80 ms) pour un effet d’échelonnement
                setTimeout(() => {
                    entry.target.classList.add('visible'); // Ajoute la classe .visible (définie en CSS)
                }, i * 80);
                observer.unobserve(entry.target); // On arrête d’observer cet élément une fois qu’il est apparu
            }
        });
    }, { threshold: 0.1 }); // Seuil : déclenche quand 10% de l’élément est visible

    // On observe tous les éléments ayant la classe `.reveal`
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // ── Stagger animation pour les cartes ───────────
    // Applique un délai d’animation progressif aux cartes de sujet et aux cartes de statistiques
    document.querySelectorAll('.sujet-card, .stat-card').forEach((card, i) => {
        // Calcule un délai = index * 0.07 seconde (ex: 1ère carte 0s, 2ème 0.07s, 3ème 0.14s...)
        card.style.animationDelay = (i * 0.07) + 's';
        card.classList.add('slide-up'); // Ajoute la classe qui déclenche l’animation CSS slide-up
    });

    // ── Preview avatar avant upload ──────────────────
    const avatarInput = document.getElementById('avatar-input');   // Input file (caché)
    const avatarPreview = document.getElementById('avatar-preview'); // Élément img pour l’aperçu
    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', () => {
            const file = avatarInput.files[0]; // Premier fichier sélectionné
            if (file) {
                const reader = new FileReader();   // Pour lire le fichier localement
                reader.onload = (e) => {
                    avatarPreview.src = e.target.result; // Met l’image en base64 dans la balise img
                    avatarPreview.style.animation = 'scaleIn .4s ease both'; // Animation d’apparition
                };
                reader.readAsDataURL(file); // Lit le fichier comme URL de données
            }
        });
    }

    // ── Star rating auto-submit ──────────────────────
    // Tous les inputs (boutons radio) à l’intérieur d’un élément .star-rating
    document.querySelectorAll('.star-rating input').forEach(input => {
        input.addEventListener('change', () => {
            // Remonte jusqu’au formulaire parent qui possède l’attribut `data-autosubmit`
            const form = input.closest('form[data-autosubmit]');
            if (form) {
                // Attend 300 ms puis soumet le formulaire (permet à l’utilisateur de voir l’étoile sélectionnée)
                setTimeout(() => form.submit(), 300);
            }
        });
    });

    // ── Animation nombre stat (count-up) ────────────
    // Pour chaque élément avec la classe .stat-card__value (affichant un nombre)
    document.querySelectorAll('.stat-card__value').forEach(el => {
        // Extraire la partie numérique du texte (supprime tout caractère non numérique)
        const target = parseInt(el.textContent.replace(/\D/g, '')) || 0;
        if (target === 0) return; // Si la valeur est 0, on ne fait rien
        let current = 0;
        const step = Math.ceil(target / 40); // Saut pour arriver à target en environ 40 étapes
        const timer = setInterval(() => {
            current = Math.min(current + step, target); // Incrémente sans dépasser la cible
            el.textContent = current;                   // Met à jour le texte affiché
            if (current >= target) clearInterval(timer); // Arrête quand on atteint la cible
        }, 35); // Intervalle de 35 ms → animation fluide
    });

    // ── Navbar active link ───────────────────────────
    // Récupère le chemin de l’URL actuelle (ex: "/sujets", "/profil")
    const currentPath = window.location.pathname;
    // Pour chaque lien de navigation (classe .nav-link)
    document.querySelectorAll('.nav-link').forEach(link => {
        // Si l’attribut href du lien correspond exactement au chemin actuel
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active'); // Ajoute une classe .active (pour styliser le lien actif)
        }
    });

    // ── Toggle visibilité mot de passe ──────────────
    // Pour chaque bouton ayant la classe .toggle-password
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            // Récupère l’id du champ cible stocké dans l’attribut `data-target`
            var targetId = btn.getAttribute('data-target');
            var input = document.getElementById(targetId);
            if (!input) return;

            // Si le champ est de type "password", on le transforme en "text" pour montrer le mot de passe
            if (input.type === 'password') {
                input.type = 'text';
                // Change l’icône du bouton : œil barré (car le mot de passe est visible)
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>';
            } else {
                // Sinon, on le repasse en "password" pour le cacher
                input.type = 'password';
                // Remet l’icône d’œil ouvert
                btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
            }
        });
    });

}); // Fin de DOMContentLoaded