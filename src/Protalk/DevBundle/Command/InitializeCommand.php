<?php
namespace Protalk\DevBundle\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper as YamlDumper;
use Symfony\Component\Yaml\Yaml;

/**
 * Simple command to help devs get started
 *
 * @author Kathryn Reeve
 */
class InitializeCommand extends ConsoleCommand
{
    /**
     * Configure the command parameters
     */
    protected function configure()
    {
        $this
            ->setName('protalk:setup')
            ->setDescription('Primary ProTalk set up to get started')
            ->addOption('dbuser', null, InputOption::VALUE_OPTIONAL, 'The Local Database User name', "")
            ->addOption('dbpassword', null, InputOption::VALUE_OPTIONAL, 'The Local Database User password', "")
        ;
    }
    /**
     * This is the primary setup command for the command
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getHelperSet()->get('dialog');
        $welcomemessage = <<<EOT
   
<info>Welcome Developer to ProTalk</info>
-------------------------------------

This interactive command is used to help you get
    the ProTalk site up and running.

EOT;
        $output->write($welcomemessage);
        $database_user = $input->getOption('dbuser');
        $database_password = $input->getOption('dbpassword');

        if (empty($database_user) || empty($database_password)) {
            $output->writeln('');
            $output->writeln("To Start we will need the Database username and password that you wish to use");
            $repeatRequest = function($prompt, $repeatPrompt, $default=null) use ($output, $dialog) {
                $returnValue = '';
                while(empty($returnValue)) {
                    $returnValue = $dialog->ask($output, $prompt, $default);
                    if (empty($returnValue)) {
                        $output->writeln('');
                        $output->writeln($repeatPrompt);
                    }
                }
                return $returnValue;
            };

            if (empty($database_user)) {
                $database_user = $repeatRequest(
                    'Please enter the username that you wish to use to connect to MySQL: ',
                    'We need to know the database username'
                );
            }

            if (empty($database_password)) {
                $database_password = $repeatRequest(
                    'Please enter the password with which you wish to use to connect to MySQL: ',
                    'We need to know the database password',
                    '<no password>'
                );
                if ($database_password == '<no password>') {
                    $database_password = "";
                }
            }
        }
        
        $output->writeln('');
        $output->writeln('');
        $output->writeln("Ok, We have the database username and password");

        $paramters = Yaml::parse('app/config/parameters.yml');
        $paramters['parameters']['database_user']        = $database_user;
        $paramters['parameters']['database_password']    = $database_password;

        file_put_contents('app/config/parameters.yml', Yaml::dump($paramters));
        
        $output->writeln("  These have been saved to app/config/parameters.yml for you");

        $app = $this->getApplication();

        $cmd = $app->find('doctrine:database:create');
        $cmd->run($input, $output);
    }
}