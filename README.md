# **Marvel Champions** - The card game 🃏

Creating the card game for the race 01 in **UNIT.City Kharkiv**.

The big idea of this challange was to create **Heartstone** clone with **Marvel** theme using only pure `PHP` and `JS`.

## **Project architecture** 📁

For this project I decided to use `Real Time Application` architecture, which provides linking few or all users together to synchronize them.

On server it was necessary to use sockets to implement `WebSocket` connection with client.

On client was only pure client with direct connection to the server using `WebSocket` class.

## **Usage** ⌨️

To start this project on Your computer You should clone this repository and move to the server folder to init database and start the server.

Firstly enter into mysql using root user (`mysql -u root`) and run `init.sql` to initialize database:

```sql
SOURCE sql/init.sql;
```

**Notice**, if You have `Permission denied` message, then You just need to add `sudo` keyword before `mysql` command.

After run the server:

```bash
php server.php
```

**Notice**, if You have an error message about `PHP`, then You just have to install it and `mysql` for `PHP`:

```bash
sudo apt install php
sudo apt install php-mysql
```

And finaly open client folder in Your **VS Code** and open `index.html` with `Live Server`.

**Notice**, if You don't have `Live Server`, then You just have to install it from `VS Code Extentions`.

## **Bugs** 🐛

If You found some bugs then just tell it in the [issues](https://github.com/Savolus/ucode-marathon-js-race-01/issues)
