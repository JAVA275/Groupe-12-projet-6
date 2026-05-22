<?php
// src/models/User.php
require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT id, nom, prenom, email, telephone, role, avatar, date_creation, derniere_connexion, is_active
             FROM utilisateurs WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['telephone'] ?? null,
            password_hash($data['mot_de_passe'], PASSWORD_BCRYPT, ['cost' => 12]),
            $data['role'] ?? 'etudiant',
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $fields = [];
        $values = [];
        foreach (['nom','prenom','email','telephone','avatar','is_active'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = ?";
                $values[] = $data[$field];
            }
        }
        if (empty($fields)) return false;
        $values[] = $id;
        $stmt = $this->db->prepare("UPDATE utilisateurs SET " . implode(', ', $fields) . " WHERE id = ?");
        return $stmt->execute($values);
    }

    public function updatePassword(int $id, string $newPassword): bool {
        $stmt = $this->db->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
        return $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]), $id]);
    }

    public function updateLastLogin(int $id): void {
        $stmt = $this->db->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id = ? AND role != 'admin'");
        return $stmt->execute([$id]);
    }

    public function getAll(string $role = null): array {
        if ($role) {
            $stmt = $this->db->prepare(
                "SELECT id, nom, prenom, email, telephone, role, is_active, date_creation
                 FROM utilisateurs WHERE role = ? ORDER BY nom"
            );
            $stmt->execute([$role]);
        } else {
            $stmt = $this->db->query(
                "SELECT id, nom, prenom, email, telephone, role, is_active, date_creation
                 FROM utilisateurs ORDER BY role, nom"
            );
        }
        return $stmt->fetchAll();
    }

    public function emailExists(string $email, int $excludeId = 0): bool {
        $stmt = $this->db->prepare("SELECT id FROM utilisateurs WHERE email = ? AND id != ?");
        $stmt->execute([$email, $excludeId]);
        return (bool)$stmt->fetch();
    }

    public function getStats(): array {
        $stmt = $this->db->query("SELECT role, COUNT(*) as total FROM utilisateurs GROUP BY role");
        $result = ['admin' => 0, 'enseignant' => 0, 'etudiant' => 0];
        foreach ($stmt->fetchAll() as $row) {
            $result[$row['role']] = (int)$row['total'];
        }
        return $result;
    }

    public function createPasswordReset(string $email): ?string {
        $stmt = $this->db->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if (!$stmt->fetch()) return null;

        $token  = bin2hex(random_bytes(32));
        $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $stmt   = $this->db->prepare("INSERT INTO password_resets (email, token, expire_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expire]);
        return $token;
    }

    public function verifyResetToken(string $token): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expire_at > NOW()"
        );
        $stmt->execute([$token]);
        return $stmt->fetch() ?: null;
    }

    public function consumeResetToken(string $token, string $newPassword): bool {
        $reset = $this->verifyResetToken($token);
        if (!$reset) return false;

        $stmt = $this->db->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$reset['email']]);
        $user = $stmt->fetch();
        if (!$user) return false;

        $this->updatePassword($user['id'], $newPassword);
        $this->db->prepare("UPDATE password_resets SET used = 1 WHERE token = ?")->execute([$token]);
        return true;
    }
}
