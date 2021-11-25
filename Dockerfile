FROM gqleung/php72

RUN rm -rf /etc/apt/sources.list
ADD ConfigurationFile/apt/sources.list /etc/apt/sources.list

RUN apt-get update&& apt-get -y install mysql-server
RUN apt -y install apache2 

RUN apt install -y openssh-server
RUN rm -f /etc/ssh/sshd_config
ADD ConfigurationFile/sshd/sshd_config  /etc/ssh/sshd_config
RUN echo 'root:xdebugadmin'|chpasswd

RUN apt-get install software-properties-common -y
RUN echo -e "\n" | apt-add-repository ppa:ondrej/php
RUN apt update 
RUN apt-get install php7.2-curl -y

RUN rm -rf /etc/apache2/apache2.conf
RUN rm -rf /etc/apache2/mods-enabled/dir.conf
RUN rm -rf /etc/php/7.2/apache2/php.ini
ADD ConfigurationFile/apache/apache2.conf /etc/apache2/apache2.conf
ADD ConfigurationFile/apache/dir.conf /etc/apache2/mods-enabled/dir.conf
ADD ConfigurationFile/php/php.ini /etc/php/7.2/apache2/php.ini
RUN apt -y install vim
RUN apt-get -y install php7.2-mysql&& apt-get -y install php7.2-gd && apt -y install php7.2-mbstring

COPY ./files /tmp/
RUN rm -rf /var/www/html/*
RUN cp -rf /tmp/html/ /var/www/ && \
    chown -R root:root /var/www/html && \
    chmod -R 777 /var/www/html

RUN chown -R mysql:mysql /var/lib/mysql
RUN rm -rf /var/run/mysqld/mysqld.sock.lock

RUN rm -rf /etc/mysql/mysql.conf.d/mysqld.cnf
ADD ConfigurationFile/mysql/mysqld.cnf /etc/mysql/mysql.conf.d/mysqld.cnf

ADD ConfigurationFile/mysql/rockxinhu.sql /rockxinhu.sql
ADD ConfigurationFile/shell/run.sh /usr/local/sbin/run.sh
RUN chown -R root:root /usr/local/sbin/run.sh
RUN chmod 755 /usr/local/sbin/run.sh
RUN apt -y install wget && apt -y install php7.2-xml
RUN wget  -P /tmp  http://pear.php.net/go-pear.phar
RUN  echo '/usr/bin/php /tmp/go-pear.phar "$@"' > /usr/bin/pear && \
    chmod +x /usr/bin/pear 
RUN apt -y install php-pear php7.2-dev
RUN pecl update-channels
RUN pecl install xdebug
WORKDIR /var/www/html

VOLUME ["/var/lib/mysql"] 
RUN mkdir -p /var/run/mysqld   #change
RUN usermod -d /var/lib/mysql/ mysql
RUN find /var/lib/mysql -type f -exec touch {} \;        #change
RUN chown -R mysql:mysql /var/lib/mysql /var/run/mysqld  #change


ENTRYPOINT ["/usr/local/sbin/run.sh"]
