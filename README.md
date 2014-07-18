SatisDynamique
==============

Install
-------
```
curl -sS https://getcomposer.org/installer | php
sudo aptitude install nodejs nmp node-less
sudo chown -R USER /usr/local/
npm install -g bower
sudo ln -s /usr/bin/nodejs /usr/bin/node
npm install -g bower
php composer.phar update
sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX ./
sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx ./
```

Testing
-------
Run this command for launching the test suite
```./vendor/bin/phpunit -c phpunit.xml.dist```

In hurry dev

