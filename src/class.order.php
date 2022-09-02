<?php
class burgerOrder
{
    public function getUserByEmail(string $email)
    {
        $db = Db::getInstance();
        $query = "SELECT * FROM users WHERE email = :email";
        return $db->fetchOne($query, __METHOD__, [':email' => $email]);

    }

    public function createUser(string $email, string $user_name)
    {
        $db = Db::getInstance();
        $query = "INSERT INTO users(email, user_name) VALUES (:email, :user_name)";
        $result = $db->exec(
            $query,
            __METHOD__,
            [
                ':email' => $email,
                ':user_name' => $user_name
            ]
        );
        if (!$result) {
            return false;
        }
        return $db->lastInsertId();
    }

    public function addOrder(int $user_id, string $phone, array $data)
    {
        $db = Db::getInstance();
        $query = "INSERT INTO orders(user_id, phone, address, created_at) VALUES (:user_id, :phone, :address, :created_at)";
        $result = $db->exec(
            $query,
            __METHOD__,
            [
                ':user_id' => $user_id,
                ':phone' => $phone,
                ':address' => $data['address'],
                ':created_at' => date ('Y.m.d H:m:s')
            ]
        );

        if (!$result) {
            return false;
        }
        return $db->lastInsertId();
    }

    public function incOrders(int $user_id)
    {
        $db = Db::getInstance();
        $query = "UPDATE users SET orders_count = orders_count+1 WHERE id = $user_id";
        return $db->exec($query, __METHOD__);
    }

}
