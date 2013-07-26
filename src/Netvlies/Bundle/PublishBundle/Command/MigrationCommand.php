<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markri
 * Date: 7/26/13
 * Time: 9:43 AM
 * To change this template use File | Settings | File Templates.
 */

namespace Netvlies\Bundle\PublishBundle\Command;

use Doctrine\ORM\EntityManager;
use Netvlies\Bundle\MigrationBundle\Entity\Deploymentlog;
use Netvlies\Bundle\PublishBundle\Entity\CommandLog;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Netvlies\Bundle\MigrationBundle\Entity\Application as oldApp;
use Netvlies\Bundle\PublishBundle\Entity\Application as newApp;
use Netvlies\Bundle\MigrationBundle\Entity\Environment as oldEnv;
use Netvlies\Bundle\PublishBundle\Entity\Environment as newEnv;
use Netvlies\Bundle\MigrationBundle\Entity\Target as oldTarget;
use Netvlies\Bundle\PublishBundle\Entity\Target as newTarget;
use Netvlies\Bundle\MigrationBundle\Entity\Userfiles as oldFile;
use Netvlies\Bundle\PublishBundle\Entity\UserFile as newFile;



class MigrationCommand extends ContainerAwareCommand
{

    /**
     * @var EntityManager $emNew
     */
    protected $emNew;

    /**
     * @var EntityManager $emOld
     */
    protected $emOld;

    /**
     * @var OutputInterface $output
     */
    protected $output;


    protected function configure()
    {
        $this
            ->setName('publish:migrate')
            ->setDescription('Migrates old database to new')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->emNew = $this->getContainer()->get('doctrine')->getManager();
        $this->emOld = $this->getContainer()->get('doctrine')->getManager('db2');
        $this->output = $output;

        $this->importApps();
        $this->importEnvs();
        $this->importTargets();
        $this->importLogs();
    }


    protected function importLogs()
    {
        $doctrine = $this->emOld->getConnection();
        $logCount = $doctrine->fetchColumn(sprintf('SELECT count(%s) as count FROM %s', '*', 'DeploymentLog'));
        $batchCount = 500;

        $this->output->writeln(sprintf('Importing logs in %s batches', ceil($logCount/$batchCount)));

        for($i=0;$i<ceil($logCount/$batchCount);$i++){

            $this->output->writeln(sprintf('Importing logs for batch %s', $i+1));
            $logs = $this->emOld->createQuery('
                SELECT l FROM Netvlies\Bundle\MigrationBundle\Entity\Deploymentlog l
            ')
            ->setMaxResults($batchCount)
            ->setFirstResult($i*$batchCount)
            ->getResult();

            foreach($logs as $oldLog){
                /**
                 * @var Deploymentlog $oldLog
                 */
                $this->emOld->detach($oldLog);

                $newLogs = $this->emNew->createQuery('
                    SELECT l FROM Netvlies\Bundle\PublishBundle\Entity\CommandLog l
                    WHERE l.datetimeEnd = :end
                    AND l.datetimeStart = :start
                ')
                ->setParameter('end', $oldLog->getDatetimeend())
                ->setParameter('start', $oldLog->getDatetimestart())
                ->getResult();

                if(count($newLogs)>1){
                    $this->output->writeln(sprintf('Log is ambigious for oldlog %s', $oldLog->getId()));
                    continue;
                }
                elseif(count($newLogs)==1){
                    // Already imported
                    $newLog = array_pop($newLogs);
                }
                else{
                    $newLog = new CommandLog();
                }


                $oldTarget = $this->emOld->getRepository('NetvliesMigrationBundle:Target')->findOneById($oldLog->getTargetid());
                $newTarget = null;
                $newApplication = null;

                if(!empty($oldTarget)){


                    $app = $this->emNew->getRepository('NetvliesPublishBundle:Application')->findOneByScmUrl($oldTarget->getApplication()->getGitrepossh());
                    $env = $this->emNew->getRepository('NetvliesPublishBundle:Environment')->findOneByKeyName($oldTarget->getEnvironment()->getKeyname());

                    if(!empty($app) && !empty($env)){

                        $newTargets = $this->emNew->createQuery('
                            SELECT t FROM Netvlies\Bundle\PublishBundle\Entity\Target t
                            WHERE t.application = :app
                            AND t.environment = :env
                        ')
                        ->setParameter('app', $app)
                        ->setParameter('env', $env)
                        ->getResult();

                        if(count($newTargets) == 1){
                            $newTarget = array_pop($newTargets);
                            $newApplication = $app;
                        }
                        else{
                            $this->output->writeln(sprintf('Target for log entry %s is ambigious or cant be found', $oldLog->getId()));
                            continue;
                        }
                    }
                    else{
                        $this->output->writeln(sprintf('app and env cant be determined for old target connected to log %s', $oldLog->getId()));
                        continue;
                    }
                }
                else{
                    $this->output->writeln(sprintf('Cant find old target, so skipping import for %s', $oldLog->getId()));
                    continue;
                }




                $newLog->setType($oldLog->getType());
                $newLog->setApplication($newApplication);
                $newLog->setTarget($newTarget);
                $newLog->setCommand($oldLog->getCommand());
                $newLog->setCommandLabel('Unkown capistrano command');
                $newLog->setDatetimeEnd($oldLog->getDatetimeend());
                $newLog->setDatetimeStart($oldLog->getDatetimestart());
                $newLog->setExitCode($oldLog->getExitcode());
                $newLog->setHost($oldLog->getHost());
                $newLog->setLog($oldLog->getLog());
                $newLog->setUser($oldLog->getUser());

                $this->emNew->persist($newLog);
                $this->emNew->flush();
            }

            $this->emOld->clear();
            $this->emNew->clear();

        }
    }


    protected function importTargets()
    {
        $targets = $this->emOld->getRepository('NetvliesMigrationBundle:Target')->findAll();

        $this->output->writeln('Importing targets');


        foreach ($targets as $oldTarget) {
            /**
             * @var oldTarget $oldTarget
             */
            $app = $this->emNew->getRepository('NetvliesPublishBundle:Application')->findOneByScmUrl($oldTarget->getApplication()->getGitrepossh());
            $env = $this->emNew->getRepository('NetvliesPublishBundle:Environment')->findOneByKeyName($oldTarget->getEnvironment()->getKeyname());


            if(empty($app) || empty($env)){
                $this->output->writeln(sprintf('Found app (%s) and env (%s). Data not complete for target id %s. Did you import apps and envs?', !empty($app)?'true':'false', !empty($env)?'true':'false', $oldTarget->getId()));
                continue;
            }

            $newTargets = $this->emNew->createQuery('
                SELECT t FROM Netvlies\Bundle\PublishBundle\Entity\Target t
                WHERE t.application = :app
                AND t.environment = :env
            ')
            ->setParameter('app', $app)
            ->setParameter('env', $env)
            ->getResult();


            if (empty($newTargets)) {
                $newTarget = new newTarget();
            }
            elseif(count($newTargets) > 1 ){
                $this->output->writeln(sprintf('Apparently new target is ambiguous for target id %s', $oldTarget->getId()));
                continue;
            }
            else{
                $newTarget = array_pop($newTargets);
            }

            $newTarget->setApplication($app);
            $newTarget->setEnvironment($env);
            $newTarget->setApproot($oldTarget->getApproot());
            $newTarget->setCaproot($oldTarget->getCaproot());
            $newTarget->setInactive(false);
            $newTarget->setLabel($oldTarget->getLabel());
            $newTarget->setLastDeployedBranch($oldTarget->getCurrentbranch());
            $newTarget->setLastDeployedRevision($oldTarget->getCurrentrevision());
            $newTarget->setMysqldb($oldTarget->getMysqldb());
            $newTarget->setMysqlpw($oldTarget->getMysqlpw());
            $newTarget->setMysqluser($oldTarget->getMysqluser());
            $newTarget->setPrimaryDomain($oldTarget->getPrimarydomain());
            $newTarget->setUsername($oldTarget->getUsername());
            $newTarget->setWebroot($oldTarget->getWebroot());

            $this->emNew->persist($newTarget);
            $this->emNew->flush();
        }
    }

    protected function importEnvs()
    {
        $envs = $this->emOld->getRepository('NetvliesMigrationBundle:Environment')->findAll();

        $this->output->writeln('Importing environments');


        foreach ($envs as $oldEnv) {
            /**
             * @var oldEnv $oldEnv
             */
            $newEnv = $this->emNew->getRepository('NetvliesPublishBundle:Environment')->findOneByKeyName($oldEnv->getKeyname());

            if (empty($newEnv)) {
                $newEnv = new newEnv();
            }

            $newEnv->setKeyName($oldEnv->getKeyname());
            $newEnv->setType($oldEnv->getType());
            $newEnv->setHostname($oldEnv->getHostname());

            $this->emNew->persist($newEnv);
            $this->emNew->flush();
        }
    }

    protected function importApps()
    {
        $apps = $this->emOld->getRepository('NetvliesMigrationBundle:Application')->findAll();

        $this->output->writeln('Importing applications');


        foreach ($apps as $oldApp) {
            /**
             * @var oldApp $oldApp
             */
            $newApp = $this->emNew->getRepository('NetvliesPublishBundle:Application')->findOneByScmUrl($oldApp->getGitrepossh());

            if (empty($newApp)) {
                $newApp = new newApp();
            }

            $newApp->setApplicationType(strtolower($oldApp->getType()->getName()));
            $newApp->setKeyName($oldApp->getRepokey());
            $newApp->setCustomer($oldApp->getCustomer());
            $newApp->setName(ucfirst($oldApp->getName()));
            $newApp->setScmService('git');
            $newApp->setScmUrl($oldApp->getGitrepossh());

            $this->emNew->persist($newApp);
            $this->emNew->flush();

            $oldFiles = $this->emOld->getRepository('NetvliesMigrationBundle:Userfiles')->findByApplication($oldApp);
            $newFiles =  $this->emNew->getRepository('NetvliesPublishBundle:UserFile')->findByApplication($newApp);

            foreach($oldFiles as $oldFile){
                /**
                 * @var oldFile $oldFile
                 */
                $insert = true;

                foreach($newFiles as $newFile){
                    /**
                     * @var newFile $newFile
                     */
                    if($oldFile->getType()==$newFile->getType() && $oldFile->getPath() == $newFile->getPath()){
                        $insert = false;
                        break;
                    }
                }

                if($insert){
                    $insertFile = new newFile();
                    $insertFile->setApplication($newApp);
                    $insertFile->setPath($oldFile->getPath());
                    $insertFile->setType($oldFile->getPath());

                    $this->emNew->persist($insertFile);
                    $this->emNew->flush();
                }

            }
        }
    }


}