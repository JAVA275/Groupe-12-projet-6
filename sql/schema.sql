-- ============================================================
--  Plateforme Gestion Sujets & Corrections - Faculté des Sciences
--  Schema SQL - MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS gestion_sujets CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_sujets;

-- Table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'enseignant', 'etudiant') NOT NULL DEFAULT 'etudiant',
    avatar VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion DATETIME DEFAULT NULL
) ENGINE=InnoDB;

-- Table filieres
CREATE TABLE filieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL
) ENGINE=InnoDB;

-- Table matieres
CREATE TABLE matieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    code VARCHAR(20) DEFAULT NULL,
    filiere_id INT DEFAULT NULL,
    FOREIGN KEY (filiere_id) REFERENCES filieres(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table sujets
CREATE TABLE sujets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    matiere_id INT DEFAULT NULL,
    filiere_id INT DEFAULT NULL,
    niveau ENUM('Licence 1','Licence 2','Licence 3','Master 1','Master 2') NOT NULL,
    session ENUM('Normale','Rattrapage') NOT NULL DEFAULT 'Normale',
    annee_academique VARCHAR(10) NOT NULL,
    description TEXT DEFAULT NULL,
    fichier VARCHAR(255) NOT NULL,
    auteur_id INT NOT NULL,
    nb_telechargements INT DEFAULT 0,
    nb_vues INT DEFAULT 0,
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE SET NULL,
    FOREIGN KEY (filiere_id) REFERENCES filieres(id) ON DELETE SET NULL,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table corrections
CREATE TABLE corrections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sujet_id INT NOT NULL,
    auteur_id INT NOT NULL,
    fichier VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sujet_id) REFERENCES sujets(id) ON DELETE CASCADE,
    FOREIGN KEY (auteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table commentaires
CREATE TABLE commentaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    sujet_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (sujet_id) REFERENCES sujets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table evaluations (notes)
CREATE TABLE evaluations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    sujet_id INT NOT NULL,
    note TINYINT NOT NULL CHECK (note BETWEEN 1 AND 5),
    date_evaluation DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_eval (utilisateur_id, sujet_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (sujet_id) REFERENCES sujets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Table téléchargements (historique)
CREATE TABLE telechargements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    sujet_id INT DEFAULT NULL,
    correction_id INT DEFAULT NULL,
    date_telechargement DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (sujet_id) REFERENCES sujets(id) ON DELETE SET NULL,
    FOREIGN KEY (correction_id) REFERENCES corrections(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Table tokens de réinitialisation de mot de passe
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(191) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expire_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
--  Données initiales
-- ============================================================

-- Filières
INSERT INTO filieres (nom, code) VALUES
('Mathématiques-Informatique', 'MI'),
('Physique-Chimie', 'PC'),
('Sciences de la Vie et de la Terre', 'SVT'),
('Informatique', 'INFO'),
('Mathématiques', 'MATH');

-- Matières exemples
INSERT INTO matieres (nom, code, filiere_id) VALUES
('Algorithmique et Structures de Données', 'ASD', 4),
('Programmation Orientée Objet', 'POO', 4),
('Base de Données', 'BD', 4),
('Analyse', 'ANA', 5),
('Algèbre', 'ALG', 5),
('Probabilités et Statistiques', 'PROBA', 1),
('Physique Générale', 'PHY', 2),
('Chimie Organique', 'CHO', 2),
('Biologie Cellulaire', 'BIO', 3),
('Réseaux Informatiques', 'RES', 4);

-- Compte administrateur principal (mot de passe: Admin@2024)
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('SAMUEL', 'Rakiel', 'rakielsamuel9@gmail.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHdma3eid', 'admin');

-- ============================================================
--  Mise à jour : vues uniques par utilisateur
-- ============================================================
CREATE TABLE IF NOT EXISTS vues_uniques (
    utilisateur_id INT NOT NULL,
    sujet_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (utilisateur_id, sujet_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (sujet_id) REFERENCES sujets(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
--  Migration : téléphone + messagerie interne
-- ============================================================

-- Ajouter colonne telephone à utilisateurs
ALTER TABLE utilisateurs ADD COLUMN IF NOT EXISTS telephone VARCHAR(20) DEFAULT NULL AFTER email;

-- Table messages internes
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    sujet VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    lu TINYINT(1) DEFAULT 0,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;
