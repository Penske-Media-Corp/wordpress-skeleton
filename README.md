# WordPress Skeleton

Skeleton and instructions for setting up a WordPress installation that automatically deploys code from the master branch of a theme.

Directory structure:

```
/var/www/sites/example.com
+-- /wordpress
+-- /wp-content
+-- index.php
+-- wp-config.php
+-- update.php
```

0. [Set up a MySQL database and user](https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql)
```mysql
CREATE DATABASE `newdb` CHARACTER SET utf8 COLLATE utf8_general_ci;
CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON newdb.* TO 'newuser'@'localhost';
FLUSH PRIVILEGES;
```

1. Download WordPress to `/var/www/sites/example.com/wordpress`
```bash
cd /var/www/sites/example.com
wget http://wordpress.org/latest.tar.gz
tar xfz latest.tar.gz
```

2. Copy or download `index.php`
  * from `https://github.com/Penske-Media-Corp/wordpress-skeleton/blob/master/index.php`
  * to `/var/www/sites/example.com/index.php`

3. Copy or download `wp-config.php`
  * from `https://github.com/Penske-Media-Corp/wordpress-skeleton/blob/master/wp-config.php`
  * to `/var/www/sites/example.com/wp-config.php`

4. [Complete the WordPress install](http://codex.wordpress.org/Installing_WordPress)
  * Use the `wp-config.php` file from this WordPress Skeleton project:  `/var/www/sites/example.com/wp-config.php`. Leave it where it is (in the web root). Don't use the `wp-config.php` in the `/wordpress` directory.
  * _Do not_ edit or override the following constants:
    * WP_SITEURL, WP_HOME, WP_CONTENT_DIR, WP_CONTENT_URL, DISALLOW_FILE_EDIT

5. Make sure file permissions are correct
  * Figure out which user PHP runs as (generally `www-data`)
  * Give that user read and write access to the entire repository.

```
$ chown -R ssh_user:www-data /path/to/repository/
$ chmod -R g+s /path/to/repository/
```

6. Create a deployment key

The easiest way is to create a new SSH key as the PHP user (generally `www-data`). This creates a new key and puts it in the correct location for the `www-data` user:

```
$ cd /path/to/repository
$ sudo -u www-data ssh-keygen -t rsa
```

Then add the key as a deployment key to the BitBucket repository.

7. Do an intial `git pull` with the PHP user to make sure the remote server is added to the PHP user's `known_hosts`. This prevents the "Host key verification failed" error.

```
$ cd /path/to/repository
$ sudo -u www-data git pull
```

# TODO
* [Maybe use composer?](http://roots.io/using-composer-with-wordpress/)
* [Maybe change the project name?](https://github.com/markjaquith/WordPress-Skeleton)
* Maybe include WordPress as a submodule?

# Props
* [Git pull from a php script, not so simple.](http://jondavidjohn.com/git-pull-from-a-php-script-not-so-simple/) to set up