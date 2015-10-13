<?php
/*=====================================================================
=            Automatic Deployment for Bitbucket and Github            =
=====================================================================*/

/*----------  Important Notes  ----------*/

// This tool Assumes that your webserver process has access to the git command and you are authenicated with 
// Github of Bitbucket with a SSH deployment Key

/*----------  Autoload  ----------*/

require('vendor/autoload.php');

/*----------  Configuration Settings  ----------*/

// You need to defined a secret key so that the webhook is only authorize. 
// Url you define should be like this ?key={$secretKey}
$secretKey = '';

// Path of the log files, make sure it is writeble by the server
$logPath = __DIR__.'/logs';

// Define you project config, each array is a project in itself, you can have more than 1 project per webhook
// projectName: need to be the exact name no space of the bitbucket project
// repoLocalDirectory: where the repo should be cloned, recommand outside the wer documment root
// workLocalDirectory: where to deploy the project on the web, usually a web folder
// repoRemote: this is the username/repo-name same as in bitbucket
// branch: the branch to checkout and deploy
// source: need to tell the repo source, git clone and fetch will depend on this.
$projectsConfig = array(
        array(
            'projectName' => 'projectname',
            'repoLocalDirectory' => '/localDirForRepo',
            'workLocalDirectory' => '/localDirForDeploy',
            'repoRemote' => 'username/projectname',
            'branch' => 'production',
            'source' => 'bitbucket.org'
        ),
        array(
            'projectName' => 'projectname',
            'repoLocalDirectory' => '/localDirForRepo',
            'workLocalDirectory' => '/localDirForDeploy',
            'repoRemote' => 'username/projectname',
            'branch' => 'master',
            'source' => 'github.com'
        ),
    );

/*----------  Listener Object  ----------*/

// Start the listerner, waiting for the payload, pass the confi, the path and secret key
$listener = new BucketDeploy\Listener($projectsConfig, $logPath, $secretKey );
// Start listening for incoming payload
$listener->listen();

/*=====  End of Automatic Deployment for Bitbucket and Github  ======*/