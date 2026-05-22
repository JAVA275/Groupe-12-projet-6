-- ============================================================
--  Migration : Téléphone + Messagerie interne
--  À exécuter dans phpMyAdmin sur la base gestion_sujets
-- ============================================================

-- 1. Ajouter la colonne téléphone
ALTER TABLE utilisateurs 
ADD COLUMN IF NOT EXISTS telephone VARCHAR(20) DEFAULT NULL AFTER email;

-- 2. Créer la table des messages internes
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SELECT 'Migration terminée avec succès !' AS statut;
