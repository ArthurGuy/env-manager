set -e

echo 'Downloading Composer'
curl -sS https://getcomposer.org/installer | php

echo 'Running Composer install'
php composer.phar install


echo 'npm install - Installing node packages'
npm install --production


echo 'gulp build - Building assets'
gulp
