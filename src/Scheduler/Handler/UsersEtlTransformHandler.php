<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\UsersEtlTransform;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use phpseclib3\Net\SFTP;
use App\src\Util\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\HeaderProcess;
use App\Entity\Summary;
use App\Entity\Detail;

#[AsMessageHandler]
class UsersEtlTransformHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(UsersEtlTransform $message)
    {
        $apiUrl = $_SERVER['API_GET_USERS_URL'];
        $raw_json = file_get_contents($apiUrl);

        $data = json_decode($raw_json, true);

        $users = array_map(function($userData) {
            return new User($userData);
        }, $data['users']);

        $users = User::transform_users($users);
        $raw_csv_etl = User::get_raw_csv($users);
        $raw_csv_summary = User::get_users_summary($users);
        
        //Save to DB
        $this->saveDataToDatabase($users, $raw_csv_summary, $raw_csv_etl);

        $sftp = new SFTP($_SERVER['SFTP_HOST']);
        if (!$sftp->login($_SERVER['SFTP_USR'], $_SERVER['SFTP_PWD'])) {
            die('Error de autenticación');
        }

        $fecha_actual = date('Ymd');
        $raw_json_filename = "data_$fecha_actual.json";
        $raw_csv_etl_filename = "ETL_$fecha_actual.csv";
        $raw_csv_summary_filename = "summary_$fecha_actual.csv";
        if ($sftp->put($raw_json_filename, json_encode($raw_json))) {
            echo ("Json raw data saved $raw_json_filename\n");
        } else {
            echo "Error saving raw data!\n";
        }
        if ($sftp->put($raw_csv_etl_filename, $raw_csv_etl)) {
            echo "ETL csv raw data saved $raw_csv_etl_filename\n";
        } else {
            echo "Error saving ETL csv raw data!\n";
        }
        if ($sftp->put($raw_csv_summary_filename, $raw_csv_summary)) {
            echo "Summary csv raw data saved $raw_csv_summary_filename\n";
        } else {
            echo "Error saving Summary csv raw data!\n";
        }
    }

    private function saveDataToDatabase($users, $raw_csv_summary, $raw_csv_etl)
    {
        $headerProcess = new HeaderProcess();
        $headerProcess->setExecutionDate(new \DateTime());

        $this->entityManager->persist($headerProcess);
        $this->entityManager->flush();

        // Guardar los detalles (ETL)
        foreach ($users as $user) {
            $detail = new Detail();
            $detail->setUserId($user->id);
            $detail->setData(json_encode($user)); 
            $detail->setHeaderProcess($headerProcess);

            $this->entityManager->persist($detail);
        }

        // Procesar y guardar los datos del resumen
        $summary = new Summary();
        $summary->setData($raw_csv_summary);
        $summary->setHeaderProcess($headerProcess);
        $this->entityManager->persist($summary);
        $this->entityManager->flush();
    }

}