<?php
namespace Backend\Services;

require_once __DIR__ . '/../DB/dbConnection.php';

use Backend\DB\DbConnection;
use Exception;

class UsersService {
    public function create($payload) {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $hashPassword = password_hash($payload["password"], PASSWORD_DEFAULT);
        $DB_CON = new DbConnection;
        $query = "INSERT INTO users
                    (initials, initials_stand_for, first_name, last_name, honorifics, nic, email, mobile_number, password, role, created_by, created_at, updated_by, updated_at)
                    VALUES (
                        :initials,
                        :initials_stand_for,
                        :first_name,
                        :last_name,
                        :honorifics,
                        :nic,
                        :email,
                        :mobile_number,
                        :password,
                        :role,
                        :created_by,
                        :created_at,
                        :updated_by,
                        :updated_at
                    )";
        $property = [
            'initials' => $payload['initials'],
            'initials_stand_for' => $payload['initials_stand_for'],
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'honorifics' => $payload['honorifics'],
            'nic' => $payload['nic'],
            'email' => $payload['email'],
            'mobile_number' => $payload['mobile_number'],
            'password' => $hashPassword,
            'role' => $payload['role'],
            'created_by' => $payload['created_by'],
            'created_at' => $created_at,
            'updated_by' => $payload['updated_by'],
            'updated_at' => $updated_at,
        ];
        $result = $DB_CON->execute($query, $property);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }
        return 'User insert successfully';
    }

    public function update($payload) {
        $updated_at = date('Y-m-d H:i:s');
        $DB_CON = new DbConnection;

        $query = "UPDATE users
                    SET
                        initials = :initials,
                        initials_stand_for = :initials_stand_for,
                        first_name = :first_name,
                        last_name = :last_name,
                        honorifics = :honorifics,
                        nic = :nic,
                        email = :email,
                        mobile_number = :mobile_number,
                        role = :role,
                        updated_by = :updated_by,
                        updated_at = :updated_at";

        $property = [
            'id' => $payload['id'],
            'initials' => $payload['initials'],
            'initials_stand_for' => $payload['initials_stand_for'],
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'honorifics' => $payload['honorifics'],
            'nic' => $payload['nic'],
            'email' => $payload['email'],
            'mobile_number' => $payload['mobile_number'],
            'role' => $payload['role'],
            'updated_by' => $payload['updated_by'],
            'updated_at' => $updated_at,
        ];

        if (!empty($payload['password'])) {
            $query .= ", password = :password";
            $property['password'] = password_hash($payload['password'], PASSWORD_DEFAULT);
        }

        $query .= " WHERE id = :id";

        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'User updated successfully';
    }

    public function delete($id) {
        $DB_CON = new DbConnection;
        $query = "DELETE FROM users WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'User deleted successfully';
    }

    public function resetPassword($payload) {
        $DB_CON = new DbConnection;

        $actor = $this->getById($payload['actor_user_id']);
        if (!$actor) {
            throw new Exception('Authorized user account not found.');
        }

        if (($actor['role'] ?? '') !== 'admin') {
            throw new Exception('Only admin users can reset passwords.');
        }

        if (!password_verify($payload['current_password'], $actor['password'] ?? '')) {
            throw new Exception('Current login password is incorrect.');
        }

        $targetUser = $this->getById($payload['target_user_id']);
        if (!$targetUser) {
            throw new Exception('Target user account not found.');
        }

        $query = "UPDATE users
                    SET password = :password,
                        updated_by = :updated_by,
                        updated_at = :updated_at
                    WHERE id = :id";

        $result = $DB_CON->execute($query, [
            'id' => $payload['target_user_id'],
            'password' => password_hash($payload['new_password'], PASSWORD_DEFAULT),
            'updated_by' => $payload['updated_by'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'User password reset successfully';
    }

    public function getAll() {
        $DB_CON = new DbConnection;
        $query = "SELECT id, initials, initials_stand_for, first_name, last_name,
                         honorifics, nic, email, mobile_number, role,
                         created_by, updated_by, created_at, updated_at
                  FROM users
                  ORDER BY id ASC";
        $DB_CON->selectData($query);
        $result = $DB_CON->fetchAll();
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }
        return $result;
    }

    public function getByEmail($email) {
        $DB_CON = new DbConnection;
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $property = [
            'email' => $email
        ];
        $DB_CON->selectDataByProperty($query, $property);
        $result = $DB_CON->fetchAll();
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }
        return $result;
    }

    public function getById($id) {
        $DB_CON = new DbConnection;
        $query = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $property = [
            'id' => $id
        ];
        $DB_CON->selectDataByProperty($query, $property);
        $result = $DB_CON->fetchAll();
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return $result[0] ?? null;
    }
}
?>
