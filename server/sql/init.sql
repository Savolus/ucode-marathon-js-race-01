CREATE DATABASE IF NOT EXISTS marvel_champions;

CREATE USER IF NOT EXISTS 'mdorohyi'@'localhost' IDENTIFIED WITH mysql_native_password BY 'securepass';

GRANT ALL PRIVILEGES ON *.* TO 'mdorohyi'@'localhost';

USE marvel_champions;

CREATE TABLE IF NOT EXISTS users (
    login VARCHAR(255) NOT NULL UNIQUE PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS waiters (
    login VARCHAR(255) NOT NULL UNIQUE PRIMARY KEY,
    FOREIGN KEY(login) REFERENCES users(login) ON DELETE CASCADE
);
