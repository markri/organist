<?php
/**
 * Netvlies Internetdiensten
 *
 * @author M. de Krijger <mdekrijger@netvlies.nl>
 * @copyright For the full copyright and license information, please view the LICENSE file
 */

namespace Netvlies\Bundle\PublishBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ConsoleCommand extends ContainerAwareCommand
{

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('organist:console')
            ->addArgument('action', null, 'start|stop|restart', null)
            ->setDescription('This command controls the console daemon. Be sure to use the right user to run this command on')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = $input->getArgument('action');
        $this->output = $output;

        switch($status){
            case 'start':
                $this->start();
                break;
            case 'stop':
                $this->stop();
                break;
            case 'restart':
                $this->restart();
                break;
            default:
                throw new \InvalidArgumentException("No such argument. Please provide either start, stop or restart\n");
        }
    }


    /**
     *
     */
    private function start()
    {
        $port = $this->getContainer()->getParameter('netvlies_publish.console_port');
        $dbHost = $this->getContainer()->getParameter('database_host');
        $dbName = $this->getContainer()->getParameter('database_name');
        $dbUser = $this->getContainer()->getParameter('database_user');
        $dbPassword = $this->getContainer()->getParameter('database_password');

        $command = sprintf('
            npm_package_config_port=%s \
            npm_package_config_dbhost=%s \
            npm_package_config_dbname=%s \
            npm_package_config_dbuser=%s \
            npm_package_config_dbpassword=%s \
            npm_package_config_table=CommandLog \
            npm_package_config_idField=id  \
            npm_package_config_commandField=command \
            npm_package_config_logField=log \
            bin/forever start --minUptime 1000 --spinSleepTime 1000 node_modules/organist-term/server.js\
        ', $port, $dbHost, $dbName, $dbUser, $dbPassword);

        $process = new Process($command);
        $exitCode = $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->output->writeln($buffer);
            }
        });

        if ($exitCode === 0) {
            $this->output->writeln('Succesfully started console');
        } else {
            $this->output->writeln('Error starting console!');
        }
    }

    /**
     *
     */
    private function stop()
    {
        $process = new Process('bin/forever stop node_modules/organist-term/server.js');
        $exitCode = $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->output->writeln($buffer);
            }
        });

        if ($exitCode === 0) {
            $this->output->writeln('Succesfully stopped console');
        } else {
            $this->output->writeln('Error stopping console!');
        }
    }

    /**
     *
     */
    private function restart()
    {
        $this->stop();
        $this->start();
    }
}
