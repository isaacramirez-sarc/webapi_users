<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-users-etl',
    description: 'Initialize ELT Users process',
)]
class InitUsersEtlCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('flush', InputArgument::OPTIONAL, 'Flush the db');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $flush = $input->getArgument('flush');
       
        if (function_exists('com_create_guid') === true) {
            // Si está disponible, genera un GUID
            $guid = trim(com_create_guid(), '{}');
        } else {
            // Si no está disponible, genera un GUID de manera manual
            $guid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        }
        
        $nombre_archivo = 'hola mundo_' . $guid . '.txt';
        $contenido = 'hola de nuevo';
        
        // Usa file_put_contents() para escribir el contenido en el archivo
        // Si el archivo no existe, se creará automáticamente
        file_put_contents($nombre_archivo, $contenido);
        
        $io->success('Your job has been finished');
        return Command::SUCCESS;
    }
}
