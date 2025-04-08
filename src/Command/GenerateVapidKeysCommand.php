<?php
namespace App\Command;

use Minishlink\WebPush\VAPID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateVapidKeysCommand extends Command
{
    protected static $defaultName = 'app:generate-vapid-keys';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /* php bin/console app:generate-vapid-keys */
        $vapidKeys = VAPID::createVapidKeys();
        $output->writeln('Public Key: '.$vapidKeys['publicKey']);
        $output->writeln('Private Key: '.$vapidKeys['privateKey']);

        return Command::SUCCESS;
    }
}
