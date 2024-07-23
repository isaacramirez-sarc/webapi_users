<?php 

namespace App\Controller;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Entity\TaskSchedule;
use App\Form\TaskScheduleType;
use App\Repository\TaskScheduleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

#[Route('/task/schedule')]
class TaskScheduleController extends AbstractController
{
    
    #[Route('/{id}/init', name: 'app_task_schedule_init', methods: ['GET', 'POST'])]
    public function init(Request $request, TaskSchedule $taskSchedule, EntityManagerInterface $entityManager, TaskScheduleRepository $taskScheduleRepository): Response
    {
        if (!$taskSchedule) {
            throw $this->createNotFoundException('Task Schedule not found');
        }
        $cron = $taskSchedule->getTimeExecution();
        if(!isset($cron) || empty($cron)){
            throw $this->createNotFoundException('Cron Expression not found');
        }
        if(!$taskSchedule->getStatus() != 1){
           // throw $this->createNotFoundException('Actually, Task is initialized');
        }
        

        try {
            $dir = __DIR__.'/ScheduleEtlUsersCommand.php';
            $dirM = str_replace("Controller","Command",$dir);
            $process = new Process(['php', $dirM, 'app:init-users-etl', '-dev', "'$cron'"]);

            $process->run();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process); 
            }

            $output = $process->getOutput(); 
            $this->addFlash('success', 'El comando se ejecutó correctamente: ' . $output); 
            echo "OK: ". $output;
        } catch (ProcessFailedException $e) {
            $this->addFlash('error', 'Error al ejecutar el comando: ' . $e->getMessage()); 
            echo  $e->getMessage();
        }


        $taskSchedule->setStatus(1);
        $taskSchedule->setLastExecutionTime(new DateTime('now'));
        $entityManager->persist($taskSchedule);
        $entityManager->flush();
        return $this->render('task_schedule/index.html.twig', [
            'task_schedules' => $taskScheduleRepository->findAll(),
        ]);
    }


    #[Route('/', name: 'app_task_schedule_index', methods: ['GET'])]
    public function index(TaskScheduleRepository $taskScheduleRepository): Response
    {
        return $this->render('task_schedule/index.html.twig', [
            'task_schedules' => $taskScheduleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_task_schedule_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $taskSchedule = new TaskSchedule();
        $form = $this->createForm(TaskScheduleType::class, $taskSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cron = $taskSchedule->getTimeExecution();
            if(!isset($cron) || empty($cron)){
                $taskSchedule->setCanInit(0);
            }
            $entityManager->persist($taskSchedule);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_schedule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_schedule/new.html.twig', [
            'task_schedule' => $taskSchedule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_schedule_show', methods: ['GET'])]
    public function show(TaskSchedule $taskSchedule): Response
    {
        return $this->render('task_schedule/show.html.twig', [
            'task_schedule' => $taskSchedule,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_task_schedule_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TaskSchedule $taskSchedule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskScheduleType::class, $taskSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_schedule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task_schedule/edit.html.twig', [
            'task_schedule' => $taskSchedule,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_schedule_delete', methods: ['POST'])]
    public function delete(Request $request, TaskSchedule $taskSchedule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskSchedule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($taskSchedule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_schedule_index', [], Response::HTTP_SEE_OTHER);
    }
}
