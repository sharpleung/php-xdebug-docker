#!/bin/sh
service ssh start
service mysql start
mysqladmin -u root password root
echo $FLAG > /flag
export FLAG=not_flag
FLAG=not_flag
mysql -uroot -proot -e "create user 'g2mtu'@'localhost' identified with mysql_native_password by '123456';"
mysql -uroot -proot -e "grant all privileges on *.* to 'g2mtu'@'localhost';"
mysql -uroot -proot -e "use mysql;update user set host='%' where user='g2mtu';"
mysql -uroot -proot -e "FLUSH PRIVILEGES;"
mysql -e "CREATE DATABASE IF NOT EXISTS rockxinhu CHARACTER SET utf8 COLLATE utf8_general_ci;use rockxinhu;source /rockxinhu.sql;" -uroot -proot
ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load
service apache2 start
chmod -R 770 /var/www/html
chown -R root:www-data /var/www/html
rm -f /rockxinhu.sql
rm -rf /tmp/html
tail -F /dev/null
