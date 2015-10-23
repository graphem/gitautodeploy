<?php 
namespace BucketDeploy;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use BucketDeploy\Project;

/**
* Listener for the payload of webhooks
*/
class Listener
{
    protected $payload;
    protected $projectsConfig;
    protected $projects = array();
    protected $log;
    protected $logPath;
    protected $secretKey;

    public function __construct($projectsConfig, $logPath, $secretKey)
    {
        $this->setProjects($projectsConfig);
        $this->logPath = $logPath;
        $this->secretKey = $secretKey;
        $this->log = new Logger('Payload Listener');
        $this->setFolder($this->logPath);
        $this->log->pushHandler(new StreamHandler($this->logPath.'/deploy.log', Logger::WARNING));
    }

    public function listen()
    {
        if (!$this->verifyKey() || !$this->getPayload()) {
            die();
        }
        $this->findProjectPayload();
    }

    /**
     *
     * Verify if folder exist and create if needed
     * @return null
     */

    public function setFolder($dir)
    {
        if ( is_dir($dir) ) {
            return true;
        }
        mkdir($dir);
    }

    /**
     *
     * Hash the project with a key so that they are easy to index when comparing
     * @return null
     */
    

    protected function setProjects($projectsConfig)
    {
        foreach ($projectsConfig as $config) {
            $hash = $this->hashValue($config['repoRemote']);
            $this->projects[$hash] = $config;
        }
    }


    /**
     *
     * Verify the user key, die if not valid
     * @return true if the key pass
     */

    protected function verifyKey()
    {
        if (!isset($_REQUEST['key']) &&
            ($_REQUEST['key'] != $this->secretKey)
        ) {
            $this->log->addError('Invalid secret key provided');
            return false;
        }

        return true;
    }

    /**
     *
     * Method to process the payload initially make sure it is present
     *
     */
    
    protected function getPayload()
    {
        $data = file_get_contents('php://input');
        //Print playload
        //$this->log->addWarning('Payload: '.print_r($data,true));
        if (empty($data)) {
            $this->log->addWarning('No payload found');
            return false;
        }

        $this->payload = $data;
        return true;
    }
    

     /**
     *
     * Hash a value
     * @return the hash value
     * 
     */

    protected function hashValue($value)
    {
        $hash = md5($value);
        return $hash;
    }

    /**
     *
     * Method to validate if the payload contain defined projects
     *
     */
   
    /*    
        TODO:
        - Need a function to detect if the payload is from bitbucket or github
        - Need to check if the correct
        - Error handling    
     */
    
    protected function findProjectPayload()
    {
        // Scan the payload for the project defined
        $this->payload = json_decode($this->payload);
        //print_r($this->payload->repository->full_name);
        $payloadHashKey = $this->hashValue($this->payload->repository->full_name);
        if (!array_key_exists($payloadHashKey, $this->projects)) {
            $this->log->addWarning('No project found in payload');
            die();
        }
        $readyProject = new Project($this->projects[$payloadHashKey], $this->log);
        $readyProject->deploy();
    }
}
