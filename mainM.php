/* ============================================================ */
/* SujetsFS — Faculté des Sciences — Université de Ngaoundéré */
/* Thème : Magique et coloré (nuit étoilée + néons) */
/* Palette : noir profond, violet, vert, cyan, magenta */
/* ============================================================ */

/* ----- VARIABLES (couleurs magiques) ----- */
:root {
/* Début de la définition des variables globales */
--bg-dark: #0b0c10;
/* Couleur de fond principale : noir profond */
--bg-card: #1a1b2f;
/* Fond des cartes : bleu nuit violacé */
--bg-side: #13142a;
/* Fond secondaire (ex: sidebars) */
--primary: #6a0dad;
/* Violet profond pour les éléments principaux */
--primary-light: #9b4dff;
/* Violet plus clair pour les dégradés */
--accent: #2ecc71;
/* Vert émeraude (accent principal, remplace l’or) */
--accent-cyan: #00e5ff;
/* Cyan néon pour les surbrillances */
--accent-magenta: #ff44cc;
/* Magenta pour les contrastes */
--text-light: #f0f0f8;
/* Texte clair (blanc légèrement teinté) */
--text-muted: #b0b0d0;
/* Texte grisé pour les informations secondaires */
--danger: #ff4d4d;
/* Rouge pour les actions dangereuses ou erreurs */
--success: #00e676;
/* Vert vif pour les messages de succès */
--radius: 16px;
/* Rayon de bordure standard (arrondi) */
--shadow-md: 0 8px 20px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(46, 204, 113, 0.3);
/* Ombre moyenne + liseré vert */
--shadow-lg: 0 12px 30px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(46, 204, 113, 0.2);
/* Ombre forte + liseré vert */
--transition: all 0.25s ease;
/* Transition douce pour tous les changements d’état */
--font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
/* Pile de polices modernes */
}

/* Fin des variables */

/* ----- RESET & BASE ----- */
*,
/* Sélecteur universel */
*::before,
/* Tous les pseudo‑éléments before */
*::after {
/* Tous les pseudo‑éléments after */
box-sizing: border-box;
/* Inclut bordures et paddings dans la largeur totale */
margin: 0;
/* Supprime les marges par défaut */
padding: 0;
/* Supprime les paddings par défaut */
}

/* Fin du reset */

html {
/* Élément racine */
scroll-behavior: smooth;
/* Défilement fluide pour les ancres */
}

/* Fin html */

body {
/* Corps de la page */
font-family: var(--font-sans);
/* Police principale */
background: var(--bg-dark);
/* Fond noir profond */
background-image: radial-gradient(circle at 10% 20%, rgba(106, 13, 173, 0.15) 0%, transparent 40%),
/* Dégradé violet transparent en haut à gauche */
radial-gradient(circle at 90% 80%, rgba(0, 229, 255, 0.1) 0%, transparent 50%);
/* Dégradé cyan transparent en bas à droite */
color: var(--text-light);
/* Couleur du texte principal */
line-height: 1.5;
/* Hauteur de ligne pour une bonne lisibilité */
min-height: 100vh;
/* Hauteur minimum = 100% de la fenêtre */
display: flex;
/* Active le mode flexbox */
flex-direction: column;
/* Disposition en colonne pour coller le footer en bas */
}

/* Fin body */

/* ----- ANIMATIONS magiques ----- */
@keyframes fadeIn {

/* Définition de l’animation fadeIn */
from {
opacity: 0;
}

/* Début : complètement transparent */
to {
opacity: 1;
}

/* Fin : totalement opaque */
}

/* Fin fadeIn */

@keyframes slideUp {

/* Définition de slideUp */
from {
/* État de départ */
opacity: 0;
/* Invisible au départ */
transform: translateY(25px);
/* Décalé vers le bas de 25px */
}

/* Fin from */
to {
/* État final */
opacity: 1;
/* Visible à la fin */
transform: translateY(0);
/* Revenu à sa position normale */
}

/* Fin to */
}

/* Fin slideUp */

@keyframes scaleIn {

/* Définition de scaleIn */
from {
/* Début */
opacity: 0;
/* Invisible */
transform: scale(0.95);
/* Légèrement réduit */
}

/* Fin from */
to {
/* Fin */
opacity: 1;
/* Visible */
transform: scale(1);
/* Taille normale */
}

/* Fin to */
}

/* Fin scaleIn */

@keyframes glowPulse {

/* Animation de pulsation lumineuse */
0% {
box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4);
}

/* Ombre nulle mais transparente */
70% {
box-shadow: 0 0 0 8px rgba(46, 204, 113, 0);
}

/* Extension jusqu’à 8px, disparition */
100% {
box-shadow: 0 0 0 0 rgba(46, 204, 113, 0);
}

/* Retour à zéro */
}

/* Fin glowPulse */

.fade-in {
animation: fadeIn 0.4s ease both;
}

/* Applique l’animation fadeIn */
.slide-up {
animation: slideUp 0.5s ease both;
}

/* Applique slideUp */
.scale-in {
animation: scaleIn 0.35s ease both;
}

/* Applique scaleIn */

.reveal {
/* Classe pour les éléments à révéler au scroll */
opacity: 0;
/* Caché par défaut */
transform: translateY(20px);
/* Décalé vers le bas */
transition: opacity 0.5s ease, transform 0.5s ease;
/* Transition fluide */
}

/* Fin reveal */

.reveal.visible {
/* État visible (ajouté par JS) */
opacity: 1;
/* Apparaît quand la classe .visible est ajoutée (scroll) */
transform: translateY(0);
/* Remonte à sa place */
}

/* Fin reveal.visible */

/* ----- NAVBAR (fond sombre + néon) ----- */
.navbar {
/* Barre de navigation */
position: sticky;
/* Reste collée en haut lors du défilement */
top: 0;
/* Point d’ancrage en haut */
z-index: 100;
/* Au-dessus du contenu */
background: rgba(11, 12, 16, 0.85);
/* Fond noir semi‑transparent */
backdrop-filter: blur(12px);
/* Flou pour effet vitré */
display: flex;
/* Alignement en ligne */
align-items: center;
/* Centrage vertical */
gap: 2rem;
/* Espacement entre les enfants */
padding: 0.75rem 2rem;
/* Espacement intérieur */
border-bottom: 1px solid var(--primary);
/* Bordure inférieure violette */
box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
/* Ombre portée */
}

/* Fin navbar */

.navbar-brand {
/* Conteneur du logo / marque */
display: flex;
/* Flex pour aligner icône + texte */
align-items: center;
gap: 0.6rem;
text-decoration: none;
/* Supprime le soulignement du lien */
}

/* Fin navbar-brand */

.brand-icon {
/* Icône de la marque */
font-size: 1.5rem;
/* Taille de l’icône */
filter: drop-shadow(0 0 2px var(--accent));
/* Lueur verte autour de l’icône */
}

/* Fin brand-icon */

.brand-text {
/* Texte de la marque */
font-weight: 800;
/* Gras très prononcé */
font-size: 1.3rem;
background: linear-gradient(135deg, var(--accent), var(--accent-cyan));
/* Dégradé vert → cyan */
-webkit-background-clip: text;
/* Applique le dégradé au texte */
background-clip: text;
color: transparent;
/* Cache la couleur de texte normale */
letter-spacing: -0.3px;
/* Réduit légèrement l’espacement */
}

/* Fin brand-text */

.navbar-links {
/* Liste des liens de navigation */
display: flex;
/* Affichage en ligne */
gap: 0.5rem;
/* Espacement entre les liens */
flex: 1;
/* Prend tout l’espace restant, pousse les éléments à droite */
}

/* Fin navbar-links */

.nav-link {
/* Lien individuel */
color: var(--text-muted);
/* Texte grisé */
text-decoration: none;
font-size: 0.9rem;
font-weight: 500;
padding: 0.5rem 1rem;
border-radius: 30px;
/* Coins très arrondis */
transition: var(--transition);
/* Animation sur hover */
}

/* Fin nav-link */

.nav-link:hover {
/* Survol d’un lien */
background: var(--primary);
/* Fond violet au survol */
color: white;
/* Texte blanc */
text-shadow: 0 0 4px var(--accent);
/* Lueur verte sur le texte */
}

/* Fin nav-link:hover */

.nav-admin {
/* Lien spécifique pour l’admin */
background: linear-gradient(135deg, var(--accent), var(--accent-magenta));
/* Dégradé vert → magenta */
color: var(--bg-dark) !important;
/* Texte noir (fond sombre) */
font-weight: bold;
}

/* Fin nav-admin */

/* zone utilisateur (avatar, menu) */
.navbar-user {
/* Conteneur de la zone utilisateur */
display: flex;
align-items: center;
gap: 1rem;
}

/* Fin navbar-user */

.user-badge {
/* Badge de rôle (admin, enseignant, étudiant) */
font-size: 0.7rem;
font-weight: 700;
text-transform: uppercase;
/* Majuscules */
padding: 0.2rem 0.8rem;
border-radius: 40px;
/* Forme de capsule */
letter-spacing: 0.5px;
}

/* Fin user-badge */

.user-badge--admin {
/* Badge admin */
background: rgba(255, 68, 204, 0.2);
/* Fond magenta transparent */
color: var(--accent-magenta);
/* Texte magenta */
border: 1px solid var(--accent-magenta);
}

/* Fin user-badge--admin */

.user-badge--enseignant {
/* Badge enseignant */
background: rgba(0, 229, 255, 0.2);
/* Fond cyan transparent */
color: var(--accent-cyan);
border: 1px solid var(--accent-cyan);
}

/* Fin user-badge--enseignant */

.user-badge--etudiant {
/* Badge étudiant */
background: rgba(46, 204, 113, 0.15);
/* Fond vert transparent */
color: var(--accent);
border: 1px solid var(--accent);
}

/* Fin user-badge--etudiant */

.user-menu {
/* Conteneur du menu déroulant utilisateur */
position: relative;
/* Pour positionner le menu déroulant */
}

/* Fin user-menu */

.user-btn {
/* Bouton qui ouvre le menu utilisateur */
background: var(--bg-card);
/* Fond bleu nuit */
border: 1px solid var(--primary);
/* Bordure violette */
color: var(--text-light);
padding: 0.4rem 1rem;
border-radius: 40px;
cursor: pointer;
font-family: var(--font-sans);
font-size: 0.85rem;
font-weight: 500;
transition: var(--transition);
display: flex;
align-items: center;
gap: 0.6rem;
}

/* Fin user-btn */

.user-btn:hover {
/* Survol du bouton utilisateur */
background: var(--primary);
/* Devient violet au survol */
border-color: var(--accent);
/* Bordure devient verte */
box-shadow: 0 0 8px rgba(46, 204, 113, 0.4);
/* Lueur verte */
}

/* Fin user-btn:hover */

.user-avatar-sm {
/* Petit avatar dans la navbar */
width: 30px;
height: 30px;
border-radius: 50%;
/* Avatar rond */
object-fit: cover;
/* Ajuste l’image sans déformation */
border: 2px solid var(--accent);
/* Bordure verte */
}

/* Fin user-avatar-sm */

.user-avatar-placeholder-sm {
/* Placeholder si pas d’avatar */
width: 30px;
height: 30px;
border-radius: 50%;
background: linear-gradient(135deg, var(--primary), var(--accent-magenta));
/* Dégradé violet → magenta */
display: flex;
align-items: center;
justify-content: center;
font-weight: bold;
font-size: 0.75rem;
color: white;
}

/* Fin user-avatar-placeholder-sm */

.user-dropdown {
/* Menu déroulant (caché par défaut) */
display: none;
/* Caché par défaut */
position: absolute;
right: 0;
top: calc(100% + 8px);
/* Juste en dessous du bouton */
background: var(--bg-card);
border: 1px solid var(--primary-light);
border-radius: 16px;
min-width: 190px;
box-shadow: var(--shadow-lg);
overflow: hidden;
z-index: 10;
backdrop-filter: blur(8px);
}

/* Fin user-dropdown */

.user-menu:hover .user-dropdown {
/* Affichage du menu au survol */
display: block;
/* Apparaît au survol */
animation: scaleIn 0.2s ease both;
/* Animation d’apparition */
}

/* Fin user-menu:hover .user-dropdown */

.user-dropdown a {
/* Liens dans le menu déroulant */
display: block;
padding: 0.7rem 1.2rem;
color: var(--text-muted);
text-decoration: none;
font-size: 0.85rem;
transition: var(--transition);
}

/* Fin user-dropdown a */

.user-dropdown a:hover {
/* Survol des liens du menu */
background: var(--primary);
color: white;
}

/* Fin user-dropdown a:hover */

.logout-link {
/* Lien de déconnexion */
border-top: 1px solid var(--primary);
/* Séparateur au-dessus */
color: var(--danger) !important;
/* Texte rouge (danger) */
}

/* Fin logout-link */

/* ----- FLASH MESSAGES (colorés) ----- */
.flash {
/* Conteneur des messages flash */
display: flex;
align-items: center;
justify-content: space-between;
padding: 0.9rem 1.5rem;
font-size: 0.9rem;
animation: slideUp 0.3s ease;
border-radius: 0;
margin-bottom: 0;
}

/* Fin flash */

.flash--success {
/* Message de succès */
background: rgba(0, 230, 118, 0.15);
color: #b9f6ca;
border-left: 4px solid var(--success);
/* Bordure gauche verte */
backdrop-filter: blur(4px);
}

/* Fin flash--success */

.flash--error {
/* Message d’erreur */
background: rgba(255, 77, 77, 0.15);
color: #ffb3b3;
border-left: 4px solid var(--danger);
/* Bordure gauche rouge */
}

/* Fin flash--error */

.flash--info {
/* Message d’information */
background: rgba(0, 229, 255, 0.15);
color: #b3ecff;
border-left: 4px solid var(--accent-cyan);
/* Bordure gauche cyan */
}

/* Fin flash--info */

.flash button {
/* Bouton de fermeture du flash */
background: none;
border: none;
color: inherit;
cursor: pointer;
font-size: 1.2rem;
opacity: 0.6;
transition: var(--transition);
}

/* Fin flash button */

.flash button:hover {
/* Survol du bouton de fermeture */
opacity: 1;
transform: rotate(90deg);
/* Bouton de fermeture tourne au survol */
}

/* Fin flash button:hover */

/* ----- BOUTONS (magiques) ----- */
.btn {
/* Classe de base pour tous les boutons */
display: inline-flex;
align-items: center;
gap: 0.5rem;
font-family: var(--font-sans);
font-weight: 600;
font-size: 0.875rem;
padding: 0.6rem 1.3rem;
border-radius: 40px;
/* Boutons très arrondis */
border: none;
cursor: pointer;
text-decoration: none;
transition: var(--transition);
line-height: 1;
}

/* Fin btn */

.btn--primary {
/* Bouton principal (violet dégradé) */
background: linear-gradient(135deg, var(--primary), var(--primary-light));
/* Dégradé violet */
color: white;
box-shadow: 0 2px 8px rgba(106, 13, 173, 0.5);
}

/* Fin btn--primary */

.btn--primary:hover {
/* Survol du bouton principal */
transform: translateY(-2px);
/* Légère élévation */
box-shadow: 0 6px 16px rgba(106, 13, 173, 0.7);
filter: brightness(1.05);
}

/* Fin btn--primary:hover */

.btn--outline {
/* Bouton avec contour (cyan) */
background: transparent;
border: 1px solid var(--accent-cyan);
/* Bordure cyan */
color: var(--accent-cyan);
}

/* Fin btn--outline */

.btn--outline:hover {
/* Survol du bouton outline */
background: rgba(0, 229, 255, 0.1);
border-color: var(--accent);
/* Bordure devient verte */
color: var(--accent);
transform: translateY(-1px);
}

/* Fin btn--outline:hover */

.btn--danger {
/* Bouton d’action dangereuse (rouge) */
background: linear-gradient(135deg, #ff4d4d, #cc0000);
color: white;
}

/* Fin btn--danger */

.btn--danger:hover {
/* Survol du bouton danger */
transform: translateY(-1px);
box-shadow: 0 0 12px rgba(255, 77, 77, 0.6);
}

/* Fin btn--danger:hover */

.btn--ghost {
/* Bouton fantôme (transparent) */
background: transparent;
color: var(--text-muted);
}

/* Fin btn--ghost */

.btn--ghost:hover {
/* Survol du bouton fantôme */
background: rgba(255, 255, 255, 0.05);
color: white;
}

/* Fin btn--ghost:hover */

.btn--sm {
/* Petit bouton */
padding: 0.35rem 0.9rem;
font-size: 0.75rem;
}

/* Fin btn--sm */

.btn--lg {
/* Grand bouton */
padding: 0.8rem 2rem;
font-size: 1rem;
}

/* Fin btn--lg */

.btn--block {
/* Bouton pleine largeur */
width: 100%;
justify-content: center;
}

/* Fin btn--block */

/* ----- FORMULAIRES (sombre + néon) ----- */
.form-group {
/* Groupe de champ de formulaire */
display: flex;
flex-direction: column;
gap: 0.35rem;
margin-bottom: 1.2rem;
}

/* Fin form-group */

.form-label {
/* Étiquette du champ */
font-size: 0.85rem;
font-weight: 600;
color: var(--accent-cyan);
/* Libellés en cyan */
}

/* Fin form-label */

.form-control {
/* Champ de saisie */
padding: 0.7rem 1rem;
border: 1px solid var(--primary);
/* Bordure violette */
border-radius: 12px;
font-family: var(--font-sans);
font-size: 0.9rem;
background: var(--bg-side);
/* Fond secondaire sombre */
color: var(--text-light);
transition: var(--transition);
width: 100%;
}

/* Fin form-control */

.form-control:focus {
/* Champ au focus */
outline: none;
border-color: var(--accent);
/* Bordure verte au focus */
box-shadow: 0 0 0 3px rgba(46, 204, 113, 0.2), 0 0 0 1px var(--accent);
/* Lueur verte */
}

/* Fin form-control:focus */

.form-control::placeholder {
/* Texte de placeholder */
color: #5a5a7a;
/* Gris‑bleu pour les placeholders */
}

/* Fin form-control::placeholder */

textarea.form-control {
/* Zone de texte */
resize: vertical;
min-height: 100px;
}

/* Fin textarea.form-control */

.form-hint {
/* Indice / aide sous le champ */
font-size: 0.7rem;
color: var(--text-muted);
}

/* Fin form-hint */

.form-error {
/* Message d’erreur de validation */
font-size: 0.7rem;
color: var(--danger);
}

/* Fin form-error */

/* ----- CARTES (vitrées) ----- */
.card {
/* Carte générique */
background: rgba(26, 27, 47, 0.85);
/* Fond bleu nuit transparent */
backdrop-filter: blur(4px);
/* Flou pour effet vitré */
border-radius: var(--radius);
box-shadow: var(--shadow-md);
overflow: hidden;
transition: var(--transition);
border: 1px solid rgba(106, 13, 173, 0.5);
/* Bordure violette semi‑transparente */
}

/* Fin card */

.card:hover {
/* Carte au survol */
transform: translateY(-4px);
box-shadow: var(--shadow-lg);
border-color: var(--accent);
/* Bordure verte au survol */
}

/* Fin card:hover */

.card-header {
/* En‑tête de la carte */
padding: 1.25rem 1.5rem;
border-bottom: 1px solid var(--primary);
display: flex;
justify-content: space-between;
background: rgba(0, 0, 0, 0.2);
}

/* Fin card-header */

.card-title {
/* Titre de la carte */
font-weight: 700;
font-size: 1.1rem;
color: var(--accent);
/* Titre en vert */
}

/* Fin card-title */

.card-body {
/* Corps de la carte */
padding: 1.5rem;
}

/* Fin card-body */

.card-footer {
/* Pied de la carte */
padding: 1rem 1.5rem;
border-top: 1px solid var(--primary);
background: rgba(0, 0, 0, 0.3);
}

/* Fin card-footer */

/* ----- CARTE SUJET (style "cristal") ----- */
.sujet-card {
/* Carte spécifique pour un sujet */
background: rgba(26, 27, 47, 0.9);
backdrop-filter: blur(4px);
border-radius: var(--radius);
box-shadow: var(--shadow-md);
overflow: hidden;
transition: var(--transition);
display: flex;
flex-direction: column;
border: 1px solid var(--primary);
}

/* Fin sujet-card */

.sujet-card:hover {
/* Survol de la carte sujet */
transform: translateY(-5px) scale(1.01);
border-color: var(--accent);
box-shadow: 0 15px 30px rgba(0, 0, 0, 0.6), 0 0 0 1px var(--accent);
}

/* Fin sujet-card:hover */

.sujet-card__header {
/* En‑tête de la carte sujet */
padding: 1.25rem 1.25rem 0.75rem;
}

/* Fin sujet-card__header */

.sujet-card__badges {
/* Conteneur des badges */
display: flex;
gap: 0.6rem;
flex-wrap: wrap;
margin-bottom: 0.75rem;
}

/* Fin sujet-card__badges */

.badge {
/* Badge générique */
font-size: 0.7rem;
font-weight: 700;
text-transform: uppercase;
letter-spacing: 0.3px;
padding: 0.25rem 0.8rem;
border-radius: 40px;
transition: var(--transition);
}

/* Fin badge */

.badge--niveau {
/* Badge pour le niveau */
background: linear-gradient(135deg, #6a0dad, #9b4dff);
color: white;
}

/* Fin badge--niveau */

.badge--session {
/* Badge pour la session */
background: linear-gradient(135deg, var(--accent), #27ae60);
/* Dégradé vert */
color: #1a1b2f;
}

/* Fin badge--session */

.badge--filiere {
/* Badge pour la filière */
background: linear-gradient(135deg, #00e5ff, #0088cc);
/* Dégradé cyan */
color: #0b0c10;
}

/* Fin badge--filiere */

.badge--correction {
/* Badge pour correction */
background: linear-gradient(135deg, #ff44cc, #cc00aa);
/* Dégradé magenta */
color: white;
}

/* Fin badge--correction */

.sujet-card__titre {
/* Titre du sujet */
font-weight: 800;
font-size: 1rem;
color: var(--text-light);
text-decoration: none;
display: block;
margin-bottom: 0.5rem;
line-height: 1.4;
}

/* Fin sujet-card__titre */

.sujet-card__titre:hover {
/* Survol du titre */
color: var(--accent);
text-shadow: 0 0 3px var(--accent);
}

/* Fin sujet-card__titre:hover */

.sujet-card__meta {
/* Métadonnées du sujet (date, auteur, etc.) */
font-size: 0.7rem;
color: var(--text-muted);
display: flex;
gap: 1rem;
flex-wrap: wrap;
}

/* Fin sujet-card__meta */

.sujet-card__footer {
/* Pied de la carte sujet */
padding: 0.75rem 1.25rem;
border-top: 1px solid var(--primary);
display: flex;
align-items: center;
justify-content: space-between;
margin-top: auto;
background: rgba(0, 0, 0, 0.3);
}

/* Fin sujet-card__footer */

.stat-item {
/* Élément de statistique (vues, commentaires) */
font-size: 0.7rem;
color: var(--text-muted);
display: flex;
align-items: center;
gap: 0.25rem;
}

/* Fin stat-item */

.etoiles {
/* Étoiles de notation */
color: var(--accent);
font-size: 0.85rem;
text-shadow: 0 0 2px var(--accent);
}

/* Fin etoiles */

/* ----- HERO (bannière magique) ----- */
.page-hero {
/* Bannière principale de la page */
background: linear-gradient(135deg, #0b0c10 0%, #1a1b2f 100%);
color: white;
padding: 3rem 2rem;
margin-bottom: 2rem;
border-radius: 0 0 32px 32px;
position: relative;
overflow: hidden;
border-bottom: 1px solid var(--accent);
}

/* Fin page-hero */

.page-hero::before {
/* Élément décoratif avant la bannière */
content: '';
position: absolute;
inset: 0;
background: radial-gradient(ellipse at 70% 20%, rgba(46, 204, 113, 0.2), transparent 70%);
/* Lueur verte en haut à droite */
pointer-events: none;
}

/* Fin page-hero::before */

.page-hero__logo-bg {
/* Logo en arrière‑plan flottant */
position: absolute;
right: -20px;
top: 50%;
transform: translateY(-50%);
width: 240px;
opacity: 0.1;
filter: drop-shadow(0 0 8px cyan);
}

/* Fin page-hero__logo-bg */

.page-hero .container {
/* Conteneur interne de la bannière */
position: relative;
z-index: 1;
}

/* Fin page-hero .container */

.page-hero__title {
/* Titre de la bannière */
font-weight: 800;
font-size: 2.2rem;
background: linear-gradient(135deg, #fff, var(--accent), var(--accent-cyan));
/* Dégradé blanc → vert → cyan */
-webkit-background-clip: text;
background-clip: text;
color: transparent;
margin-bottom: 0.5rem;
}

/* Fin page-hero__title */

.page-hero__sub {
/* Sous‑titre de la bannière */
color: #b0b0d0;
font-size: 1rem;
}

/* Fin page-hero__sub */

/* ----- AUTHENTIFICATION (full magic) ----- */
.auth-page {
/* Page entière d’authentification */
min-height: 100vh;
display: flex;
align-items: center;
justify-content: center;
background: radial-gradient(ellipse at 20% 30%, #1a1b2f, #0b0c10);
/* Dégradé radial violet foncé → noir */
position: relative;
}

/* Fin auth-page */

.auth-bg-logo {
/* Logo en fond transparent sur la page auth */
position: absolute;
width: 50%;
max-width: 450px;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);
opacity: 0.08;
filter: blur(2px);
}

/* Fin auth-bg-logo */

.auth-card {
/* Carte du formulaire d’authentification */
background: rgba(26, 27, 47, 0.9);
backdrop-filter: blur(12px);
border-radius: 32px;
padding: 2rem 2rem 2.5rem;
width: 100%;
max-width: 440px;
box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6), 0 0 0 1px var(--primary);
border: 1px solid rgba(46, 204, 113, 0.3);
/* Bordure verte transparente */
animation: scaleIn 0.4s ease both;
}

/* Fin auth-card */

.auth-logo {
/* Logo dans la carte auth */
text-align: center;
margin-bottom: 1.5rem;
}

/* Fin auth-logo */

.auth-logo-img {
/* Image du logo */
width: 75px;
height: 75px;
filter: drop-shadow(0 0 8px var(--accent));
/* Lueur verte autour du logo */
}

/* Fin auth-logo-img */

.auth-logo .brand-text {
/* Texte de la marque dans la carte auth */
font-weight: 800;
font-size: 1.8rem;
background: linear-gradient(135deg, var(--accent), var(--accent-magenta));
/* Dégradé vert → magenta */
-webkit-background-clip: text;
background-clip: text;
color: transparent;
}

/* Fin auth-logo .brand-text */

.auth-title {
/* Titre du formulaire (Connexion / Inscription) */
font-size: 1.6rem;
font-weight: 700;
color: var(--accent);
/* Titre en vert */
}

/* Fin auth-title */

.auth-sub {
/* Sous‑titre du formulaire */
color: var(--text-muted);
font-size: 0.85rem;
margin-bottom: 1.5rem;
}

/* Fin auth-sub */

.auth-footer {
/* Pied du formulaire (liens) */
text-align: center;
margin-top: 1.25rem;
font-size: 0.8rem;
}

/* Fin auth-footer */

.auth-footer a {
/* Liens dans le footer auth */
color: var(--accent-cyan);
/* Liens en cyan */
text-decoration: none;
font-weight: 600;
}

/* Fin auth-footer a */

.auth-footer a:hover {
/* Survol des liens auth */
text-decoration: underline;
text-shadow: 0 0 4px cyan;
}

/* Fin auth-footer a:hover */

/* ----- AVATAR (effet néon) ----- */
.avatar-upload-wrap {
/* Zone de téléversement d’avatar */
display: flex;
align-items: center;
gap: 1.2rem;
margin-bottom: 1.2rem;
}

/* Fin avatar-upload-wrap */

.avatar-preview {
/* Aperçu de l’avatar */
width: 80px;
height: 80px;
border-radius: 50%;
object-fit: cover;
border: 3px solid var(--accent);
/* Bordure verte */
box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.3), 0 0 0 4px var(--accent);
/* Double ombre pour effet néon */
}

/* Fin avatar-preview */

.avatar-placeholder {
/* Placeholder quand pas d’image */
width: 80px;
height: 80px;
border-radius: 50%;
background: linear-gradient(135deg, var(--primary), var(--accent-magenta));
display: flex;
align-items: center;
justify-content: center;
font-weight: bold;
font-size: 2rem;
color: white;
border: 2px solid var(--accent);
}

/* Fin avatar-placeholder */

.avatar-upload-btn {
/* Bouton pour choisir un fichier */
background: var(--bg-card);
border: 1px solid var(--accent-cyan);
color: var(--accent-cyan);
padding: 0.4rem 1rem;
border-radius: 40px;
cursor: pointer;
font-size: 0.8rem;
transition: var(--transition);
}

/* Fin avatar-upload-btn */

.avatar-upload-btn:hover {
/* Survol du bouton upload */
background: var(--primary);
border-color: var(--accent);
color: white;
}

/* Fin avatar-upload-btn:hover */

.avatar-input {
/* Input file caché */
display: none;
}

/* Fin avatar-input */

.avatar-lg {
/* Grand avatar (profil) */
width: 110px;
height: 110px;
border-radius: 50%;
object-fit: cover;
border: 4px solid var(--accent);
box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.3), 0 0 15px var(--accent);
}

/* Fin avatar-lg */

.avatar-lg-placeholder {
/* Grand placeholder */
width: 110px;
height: 110px;
border-radius: 50%;
background: linear-gradient(135deg, var(--primary), var(--accent-magenta));
display: flex;
align-items: center;
justify-content: center;
font-weight: bold;
font-size: 2.5rem;
color: white;
border: 3px solid var(--accent);
}

/* Fin avatar-lg-placeholder */

/* ----- LAYOUT ----- */
.container {
/* Conteneur principal centré */
max-width: 1280px;
/* Largeur maximale */
margin: 0 auto;
/* Centrage horizontal */
padding: 0 1.5rem;
/* Marge intérieure latérale */
}

/* Fin container */

.grid {
/* Grille générique */
display: grid;
gap: 1.5rem;
}

/* Fin grid */

.grid--2 {
grid-template-columns: repeat(2, 1fr);
}

/* 2 colonnes égales */
.grid--3 {
grid-template-columns: repeat(3, 1fr);
}

/* 3 colonnes */
.grid--4 {
grid-template-columns: repeat(4, 1fr);
}

/* 4 colonnes */
.grid--cards {
grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

/* Grille responsive pour les cartes */

main {
flex: 1;
}

/* Le contenu principal prend l’espace restant */
.page-content {
padding: 2rem 0 3rem;
}

/* Espacement vertical du contenu principal */

/* ----- SEARCH BAR ----- */
.search-bar {
/* Barre de recherche et filtres */
display: flex;
gap: 1rem;
background: var(--bg-card);
border-radius: 60px;
/* Très arrondi, comme une capsule */
padding: 0.8rem 1.5rem;
box-shadow: var(--shadow-md);
margin-bottom: 1.5rem;
flex-wrap: wrap;
border: 1px solid var(--primary);
}

/* Fin search-bar */

.search-bar .form-control {
/* Champ de recherche dans la barre */
background: var(--bg-side);
border-color: var(--primary);
}

/* Fin search-bar .form-control */

.filter-select {
/* Menu déroulant de filtre */
padding: 0.6rem 1rem;
border: 1px solid var(--primary);
border-radius: 40px;
background: var(--bg-side);
color: var(--text-light);
cursor: pointer;
transition: var(--transition);
}

/* Fin filter-select */

.filter-select:hover,
.filter-select:focus {
/* Survol / focus des filtres */
border-color: var(--accent);
outline: none;
}

/* Fin filter-select:hover, filter-select:focus */

.results-info {
/* Information sur le nombre de résultats */
font-size: 0.8rem;
color: var(--accent-cyan);
}

/* Fin results-info */

/* ----- STATS CARDS (néon) ----- */
.stats-grid {
/* Grille des cartes de statistiques */
display: grid;
grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
gap: 1.2rem;
margin-bottom: 2rem;
}

/* Fin stats-grid */

.stat-card {
/* Carte de statistique individuelle */
background: rgba(26, 27, 47, 0.8);
backdrop-filter: blur(4px);
border-radius: 24px;
padding: 1.5rem;
border: 1px solid var(--primary);
transition: var(--transition);
}

/* Fin stat-card */

.stat-card:hover {
/* Survol de la carte stat */
transform: translateY(-4px);
border-color: var(--accent);
box-shadow: 0 0 15px rgba(46, 204, 113, 0.3);
}

/* Fin stat-card:hover */

.stat-card__label {
/* Libellé de la statistique */
font-size: 0.7rem;
text-transform: uppercase;
letter-spacing: 1px;
color: var(--accent-cyan);
font-weight: 600;
}

/* Fin stat-card__label */

.stat-card__value {
/* Valeur numérique de la statistique */
font-weight: 800;
font-size: 2.5rem;
line-height: 1;
background: linear-gradient(135deg, var(--accent), var(--accent-magenta));
/* Dégradé vert → magenta */
-webkit-background-clip: text;
background-clip: text;
color: transparent;
}

/* Fin stat-card__value */

/* ----- TABLEAU ----- */
.table-wrap {
/* Conteneur du tableau avec défilement horizontal */
overflow-x: auto;
border-radius: 20px;
border: 1px solid var(--primary);
background: rgba(26, 27, 47, 0.6);
backdrop-filter: blur(4px);
}

/* Fin table-wrap */

table {
/* Tableau */
width: 100%;
border-collapse: collapse;
}

/* Fin table */

thead th {
/* Cellule d’en‑tête */
background: rgba(0, 0, 0, 0.5);
color: var(--accent);
/* En‑têtes de tableau en vert */
font-size: 0.75rem;
font-weight: 700;
text-transform: uppercase;
padding: 0.9rem 1.2rem;
text-align: left;
}

/* Fin thead th */

tbody td {
/* Cellule de corps */
padding: 0.8rem 1.2rem;
border-bottom: 1px solid var(--primary);
font-size: 0.85rem;
}

/* Fin tbody td */

tbody tr:hover {
/* Ligne survolée */
background: rgba(106, 13, 173, 0.2);
/* Survol violet transparent */
}

/* Fin tbody tr:hover */

/* ----- DETAIL SUJET ----- */
.sujet-detail {
/* Conteneur principal de la page détail sujet */
display: grid;
grid-template-columns: 1fr 320px;
/* Contenu principal + sidebar de 320px */
gap: 2rem;
align-items: start;
}

/* Fin sujet-detail */

.sujet-info-grid {
/* Grille d’informations du sujet */
display: grid;
grid-template-columns: 1fr 1fr;
gap: 1rem;
margin: 1.5rem 0;
}

/* Fin sujet-info-grid */

.info-item {
/* Élément d’information individuel */
background: rgba(0, 0, 0, 0.3);
border-radius: 16px;
padding: 0.8rem 1rem;
border-left: 3px solid var(--accent);
/* Bordure gauche verte */
}

/* Fin info-item */

.info-item__label {
/* Libellé de l’info */
font-size: 0.7rem;
text-transform: uppercase;
color: var(--accent-cyan);
}

/* Fin info-item__label */

.info-item__value {
/* Valeur de l’info */
font-weight: 700;
color: var(--text-light);
}

/* Fin info-item__value */

.correction-item {
/* Élément de correction */
display: flex;
justify-content: space-between;
padding: 0.8rem 1rem;
border: 1px solid var(--primary);
border-radius: 20px;
margin-bottom: 0.6rem;
transition: var(--transition);
}

/* Fin correction-item */

.correction-item:hover {
/* Survol d’une correction */
border-color: var(--accent);
background: rgba(46, 204, 113, 0.1);
}

/* Fin correction-item:hover */

.comment-item {
/* Élément de commentaire */
padding: 0.9rem;
border-radius: 20px;
background: rgba(0, 0, 0, 0.3);
margin-bottom: 0.75rem;
border-left: 3px solid transparent;
}

/* Fin comment-item */

.comment-item:hover {
/* Survol d’un commentaire */
border-left-color: var(--accent-magenta);
/* Bordure gauche magenta au survol */
}

/* Fin comment-item:hover */

.comment-author {
/* Auteur du commentaire */
font-weight: 700;
font-size: 0.85rem;
margin-bottom: 0.3rem;
}

/* Fin comment-author */

.comment-date {
/* Date du commentaire */
font-size: 0.7rem;
color: var(--text-muted);
}

/* Fin comment-date */

.comment-text {
/* Texte du commentaire */
font-size: 0.85rem;
line-height: 1.5;
}

/* Fin comment-text */

.star-rating {
/* Conteneur des étoiles de notation */
display: flex;
gap: 0.25rem;
margin: 0.5rem 0;
}

/* Fin star-rating */

.star-rating input {
/* Input radio caché pour la note */
display: none;
}

/* Fin star-rating input */

.star-rating label {
/* Étoile (label) */
font-size: 1.5rem;
color: #444;
cursor: pointer;
transition: var(--transition);
}

/* Fin star-rating label */

.star-rating label:hover {
/* Survol d’une étoile */
transform: scale(1.2);
}

/* Fin star-rating label:hover */

.star-rating input:checked~label,
/* Étoiles sélectionnées */
.star-rating label:hover,
.star-rating label:hover~label {
color: var(--accent);
/* Étoiles vertes quand sélectionnées ou survolées */
text-shadow: 0 0 5px var(--accent);
}

/* Fin règle de notation */

/* ----- PAGINATION ----- */
.pagination {
/* Conteneur de la pagination */
display: flex;
gap: 0.5rem;
justify-content: center;
margin-top: 2rem;
}

/* Fin pagination */

.page-btn {
/* Bouton de page */
width: 40px;
height: 40px;
display: flex;
align-items: center;
justify-content: center;
border-radius: 50%;
border: 1px solid var(--primary);
color: var(--text-muted);
text-decoration: none;
transition: var(--transition);
background: rgba(0, 0, 0, 0.5);
}

/* Fin page-btn */

.page-btn:hover {
/* Survol d’un bouton de page */
border-color: var(--accent);
color: var(--accent);
transform: translateY(-2px);
box-shadow: 0 0 8px var(--accent);
}

/* Fin page-btn:hover */

.page-btn.active {
/* Bouton de la page active */
background: linear-gradient(135deg, var(--primary), var(--accent-magenta));
/* Dégradé violet → magenta */
border-color: var(--accent);
color: white;
}

/* Fin page-btn.active */

/* ----- FOOTER ----- */
.footer {
/* Pied de page */
background: rgba(11, 12, 16, 0.9);
border-top: 1px solid var(--primary);
color: var(--text-muted);
padding: 1.8rem;
text-align: center;
margin-top: auto;
}

/* Fin footer */

.footer-brand {
/* Marque dans le footer */
font-weight: 700;
color: var(--accent);
/* Marque du footer en vert */
}

/* Fin footer-brand */

.footer-copy {
/* Mention de copyright */
font-size: 0.75rem;
}

/* Fin footer-copy */

/* ----- EMPTY STATE ----- */
.empty-state {
/* État vide (aucune donnée) */
text-align: center;
padding: 3rem;
background: rgba(26, 27, 47, 0.6);
border-radius: 32px;
border: 1px dashed var(--accent);
}

/* Fin empty-state */

.empty-state__icon {
/* Icône de l’état vide */
font-size: 3rem;
filter: drop-shadow(0 0 6px cyan);
}

/* Fin empty-state__icon */

.empty-state__title {
/* Titre de l’état vide */
font-weight: 700;
font-size: 1.2rem;
color: var(--primary);
margin-bottom: 0.5rem;
}

/* Fin empty-state__title */

/* ----- RESPONSIVE ----- */
@media (max-width: 900px) {

/* Écrans de largeur ≤ 900px */
.sujet-detail {
grid-template-columns: 1fr;
}

/* Passe en une colonne sur tablette */
.grid--4,
.grid--3 {
grid-template-columns: repeat(2, 1fr);
}

/* 4 et 3 colonnes deviennent 2 colonnes */
}

/* Fin media 900px */

@media (max-width: 650px) {

/* Écrans de largeur ≤ 650px */
.navbar-links {
display: none;
}

/* Cache les liens de navigation sur mobile (menu hamburger attendu) */
.navbar {
padding: 0.7rem 1rem;
}

.page-hero__title {
font-size: 1.6rem;
}

.grid--2,
.grid--3,
.grid--4 {
grid-template-columns: 1fr;
}

/* Toutes les grilles deviennent 1 colonne */
.sujet-info-grid {
grid-template-columns: 1fr;
}

.auth-card {
margin: 1rem;
padding: 1.5rem;
}

.stats-grid {
grid-template-columns: 1fr;
}

.search-bar {
border-radius: 20px;
flex-direction: column;
}
}

/* Fin media 650px */

.hidden {
display: none !important;
}

/* Classe utilitaire pour masquer un élément */