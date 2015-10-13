<?php 
namespace BucketDeploy;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
/**
* Project object which will be used to setup a project and deploy
*
*/
class Project
{
    protected $projectName;
    protected $localRepoPath;
    protected $remoteRepoPath;
    protected $workRepoPath;
    protected $branch;
    protected $source;
    protected $localFolder;
    protected $folderMode;
    protected $folderValidated;
    protected $repoCloned;
    protected $log;

    function __construct($project, Logger $log)
    {        
        $this->setProject($project);
        $this->folderMode = 0755;
        $this->folderValidated = false;
        $this->repoCloned = false;
        $this->log = $log;
    }

    /**
     * Set the project configs
     *
     * @param $project array
     * @return null
     */

    protected function setProject($project)
    {
        $this->projectName = $project['projectName'];
        $this->localRepoPath = $project['repoLocalDirectory'];
        $this->remoteRepoPath = $project['repoRemote'];
        $this->workRepoPath = $project['workLocalDirectory'];
        $this->branch = $project['branch'];
        $this->source = $project['source'];
        $this->localFolder = $this->localRepoPath.'/'.$this->projectName.'.git';
    }

    /**
     * Deploy the project, set of function to be run
     *
     * @param null
     * @return null
     */

    public function deploy()
    {
        $this->checkLocalFolder($this->localFolder);
        $this->checkLocalFolder($this->workRepoPath);
        $this->getRepo();
        $this->checkoutRepo();
    }

    /**
     * Check if a local folder exist, create if non existent.
     *
     * @param $dir 
     * @return bool or die if error
     */

    protected function checkLocalFolder($dir)
    {
        if ( is_dir($dir) ) {
            return true;
        }
        if ( !mkdir($dir, $this->folderMode, true )) {
            $this->log->addWarning('Error Creating directory: '.$dir);
            $this->folderValidated = false;
            die();
        }
        $this->folderValidated = true;
        return true;        
    }

    /**
     * Get remote repository
     *
     * @param null
     * @return null
     */

    protected function getRepo()
    {
        // Check if repo is there, fetch it
        if(is_file($this->localFolder.'/HEAD')){
            $this->fetchRepo();
            return false;
        }

        // Clone repo when it is not found locally        
        exec('cd '.$this->localFolder.' && git clone --mirror git@'.$this->source.':'.$this->remoteRepoPath.'.git .');
        $this->repoCloned = true;
        $this->log->addWarning('Cloning '.$this->remoteRepoPath.' as not found locally');
        return true;
    }
    
    /**
     * Fetch remote repository
     *
     * @param null
     * @return null
     */

    protected function fetchRepo()
    {
        exec('cd ' . $this->localFolder . ' && git fetch');
        $this->log->addWarning('Fetching '.$this->remoteRepoPath);
    }

    /**
     * Checkout repo to the working directory for final deployment step
     *
     * @param null
     * @return null
     */

    protected function checkoutRepo()
    {
        exec('cd '.$this->localFolder.' && GIT_WORK_TREE='.$this->workRepoPath.' git checkout -f '.$this->branch);
        $this->log->addWarning('Fetching '.$this->remoteRepoPath.' branch: '.$this->branch.' to '.$this->workRepoPath);        
    }

}