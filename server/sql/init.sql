CREATE DATABASE IF NOT EXISTS marvel_champions;

CREATE USER IF NOT EXISTS 'mdorohyi'@'localhost' IDENTIFIED WITH mysql_native_password BY 'securepass';

GRANT ALL PRIVILEGES ON *.* TO 'mdorohyi'@'localhost';

USE marvel_champions;

CREATE TABLE IF NOT EXISTS deck (
    name VARCHAR(255) NOT NULL PRIMARY KEY,
    attack INT(16) NOT NULL,
    health INT(16) NOT NULL,
    price INT(16) NOT NULL
);

INSERT INTO deck(name, attack, health, price)
VALUES 
	('Black Panther', 3, 3, 3),
	('Black Widow', 2, 4, 3),
	('Captain America', 3, 5, 5),
	('Corvus Glaive', 1, 2, 1),
	('Doctor Strange', 4, 5, 6),
	('Drax', 1, 3, 2),
	('Ebony Maw', 2, 2, 2),
	('Falcon', 2, 3, 3),
	('Gamora', 1, 2, 1),
	('Groot', 3, 4, 4),
	('Heimdall', 2, 2, 2),
	('Iron Man', 5, 6, 6),
	('Loki', 3, 3, 3),
	('Nebula', 1, 3, 2),
	('Rocket', 3, 4, 4),
	('Shuri', 1, 2, 1),
	('Star-Lord', 3, 5, 5),
	('Thanos', 6, 6, 6),
	('Thor', 5, 6, 6),
	('Winter Soldier', 3, 4, 4);
