SatisDynamique
==============

**In hury dev** [![Build Status](https://travis-ci.org/waldo2188/SatisDynamique.svg?branch=master)](https://travis-ci.org/waldo2188/SatisDynamique)

SatisDynamique is a very light front-end for [Satis](https://github.com/composer/satis).
Satis is a "Simple static Composer repository generator."

SatisDynamique allow you to add some UI for configuring repositories and packages.
With SatisDynamique, any of your coworkers can add or manage Satis repository.

For now, SatisDynamique do :
- Add/Change/Remove package
- Add/Change/Remove repository
- Console utilities for update Satis and build a repository



Install
-------
SatisDynamique need nodejs, npm, node-less, for developpement.
Here is my install script (it will be better in the future)
```
sudo aptitude install nodejs npm node-less
sudo chown -R USER /usr/local/
npm install -g bower
sudo ln -s /usr/bin/nodejs /usr/bin/node
git clone git@github.com:waldo2188/SatisDynamique.git
cd SatisDynamique
curl -sS https://getcomposer.org/installer | php
php composer.phar install
sudo setfacl -R -m u:www-data:rwX -m u:`whoami`:rwX ./
sudo setfacl -dR -m u:www-data:rwx -m u:`whoami`:rwx ./
```

Testing
-------
Run this command for launching the test suite
```./vendor/bin/phpunit -c phpunit.xml.dist```

