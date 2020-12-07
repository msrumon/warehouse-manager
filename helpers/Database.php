<?php

class Database
{
    private $sqlUp = <<< SQL
        -- Table: `users`
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INTEGER NOT NULL PRIMARY KEY,
            `email` TEXT NOT NULL UNIQUE,
            `password` TEXT NOT NULL,
            `role` TEXT NOT NULL
        );

        -- Table: `suppliers`
        CREATE TABLE IF NOT EXISTS `suppliers` (
            `id` INTEGER NOT NULL PRIMARY KEY,
            `name` TEXT NOT NULL,
            `address` TEXT NOT NULL
        );

        -- Table: `products`
        CREATE TABLE IF NOT EXISTS `products` (
            `id` INTEGER NOT NULL PRIMARY KEY,
            `title` TEXT NOT NULL,
            `description` TEXT NOT NULL,
            `price` NUMERIC NOT NULL,
            `image` TEXT NOT NULL,
            `supplier_id` INTEGER NOT NULL,
            FOREIGN KEY (`supplier_id`)
                REFERENCES `suppliers` (`id`)
                ON UPDATE CASCADE
                ON DELETE RESTRICT
        );
    SQL;

    private $sqlDown = <<< SQL
        DROP TABLE IF EXISTS `products`;
        DROP TABLE IF EXISTS `suppliers`;
        DROP TABLE IF EXISTS `users`;
    SQL;

    private $connection;

    public function __construct()
    {
        $this->connection = new PDO('sqlite:database.sqlite');
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Returns PDO connection object
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Creates tables
     */
    public function createTables()
    {
        $this->connection->exec($this->sqlUp);
    }

    /**
     * Drops tables
     */
    public function dropTables()
    {
        $this->connection->exec($this->sqlDown);
    }
}

return new Database();
