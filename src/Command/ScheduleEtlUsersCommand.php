<?php

namespace App\Command;

require_once __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Cron\CronExpression;
use App\Scheduler\Handler\UsersEtlTransformHandler;
use App\Scheduler\Message\UsersEtlTransform;
use Symfony\Component\Scheduler\Scheduler;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:schedule:etl-users',
    description: 'schedule ELT Users process job by cron expr',
)]
class ScheduleEtlUsersCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('cron-expr', InputArgument::REQUIRED, 'Cron expression')
            ->addOption('dev', null, InputOption::VALUE_NONE, 'Ever minute')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        if ($input->getOption('dev')) {
            $cronExpr = '* * * * *';
            $io->note(sprintf('You are using the dev cron expression: %s', $cronExpr));
        }else{
            $cronExpr = $input->getArgument('cron-expr');
        }

        if(!CronExpression::isValidExpression($cronExpr)){
            $io->error('Invalid cron expression');
            return Command::FAILURE;
        }

        if ($cronExpr) {
            $io->note(sprintf('You will set the schedule by the cron expression: %s', $cronExpr));
        }
        
        $schedule = (new Schedule())
        ->with(
            RecurringMessage::cron($cronExpr,new UsersEtlTransform(1))
        );
    
        $scheduler = new Scheduler(handlers: [
            UsersEtlTransform::class => new UsersEtlTransformHandler($this->entityManager),
        ], schedules: [
            $schedule,
        ]);
        
        // finally, run the scheduler once it's ready
        $scheduler->run();

        $io->success('Cron scheduled at: '.$cronExpr);
        return Command::SUCCESS;
    }
}
