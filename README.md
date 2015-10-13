# gitautodeploy
Auto deploy script for GitHub.com and Bitbucket.org in PHP. Autodeploy based on the webhooks on push events. It supports deployment of multiple projects on the same web server.

Requirements
------------
* PHP >=5.3
* git command enabled for the web server user
* a git repository to deploy (duh!!)


Installation
------------
Place the project in a web accessible folder on your web folder, e.g: $_SERVER['DOCUMENT_ROOT']/deploy/ to make it possible for the webhook to ping the script.

You must install this tool through Composer to get the dependencies:

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php

php composer.phar install
```

Configure the file deploy.php in the root of the project with your own settings.

Edit the .htaccess file if you want to allow other ip address to call the hook.

- - -

DISCLAIMER
--------------------
We take no responsibility for the use of this script, or any negative effects that may result in using it! Please know what you are doing before using it.

Support and Feedback
--------------------
Your feedback is appreciated! If you have specific problems or bugs with this tool, please file an issue on Github.

For general feedback and support requests, contact us at http://www.graphem.ca/contact-us/