<?php

namespace App\src\Util;

class User {
    public $id;
    public $firstName;
    public $lastName;
    public $maidenName;
    public $age;
    public $gender;
    public $email;
    public $phone;
    public $username;
    public $password;
    public $birthDate;
    public $image;
    public $bloodGroup;
    public $height;
    public $weight;
    public $eyeColor;
    public $hair;
    public $ip;
    public $address;
    public $macAddress;
    public $university;
    public $bank;
    public $company;
    public $ein;
    public $ssn;
    public $userAgent;
    public $crypto;
    public $role;

    public function __construct($data) {
        $this->id = $data['id'];
        $this->firstName = $data['firstName'];
        $this->lastName = $data['lastName'];
        $this->maidenName = $data['maidenName'];
        $this->age = $data['age'];
        $this->gender = $data['gender'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->birthDate = $data['birthDate'];
        $this->image = $data['image'];
        $this->bloodGroup = $data['bloodGroup'];
        $this->height = $data['height'];
        $this->weight = $data['weight'];
        $this->eyeColor = $data['eyeColor'];
        $this->hair = $data['hair'];
        $this->ip = $data['ip'];
        $this->address = $data['address'];
        $this->macAddress = $data['macAddress'];
        $this->university = $data['university'];
        $this->bank = $data['bank'];
        $this->company = $data['company'];
        $this->ein = $data['ein'];
        $this->ssn = $data['ssn'];
        $this->userAgent = $data['userAgent'];
        $this->crypto = $data['crypto'];
        $this->role = $data['role'];
    }
    
    
    public static function transform_users($users){
        $users = array_filter($users, function($user) {
            return self::isValidEmail($user->email);
        });
        return $users;
    }

    private static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }


    public static function get_raw_csv($users) : string {
        $csvContent = '';
        $headers = [
            'ID', 'First Name', 'Last Name', 'Maiden Name', 'Age', 'Gender', 'Email', 'Phone', 'Username', 'Password', 
            'Birth Date', 'Image', 'Blood Group', 'Height', 'Weight', 'Eye Color', 'Hair Color', 'Hair Type', 'IP', 
            'Address', 'City', 'State', 'State Code', 'Postal Code', 'Latitude', 'Longitude', 'Country', 'MAC Address', 
            'University', 'Bank Card Expire', 'Bank Card Number', 'Bank Card Type', 'Bank Currency', 'Bank IBAN', 
            'Company Department', 'Company Name', 'Company Title', 'Company Address', 'Company City', 'Company State', 
            'Company State Code', 'Company Postal Code', 'Company Latitude', 'Company Longitude', 'Company Country', 
            'EIN', 'SSN', 'User Agent', 'Crypto Coin', 'Crypto Wallet', 'Crypto Network', 'Role'
        ];
        $csvContent .= implode(',', $headers) . "\n";
        foreach ($users as $user) {
            $row = [
                $user->id,
                $user->firstName,
                $user->lastName,
                $user->maidenName,
                $user->age,
                $user->gender,
                $user->email,
                $user->phone,
                $user->username,
                $user->password,
                $user->birthDate,
                $user->image,
                $user->bloodGroup,
                $user->height,
                $user->weight,
                $user->eyeColor,
                $user->hair['color'],
                $user->hair['type'],
                $user->ip,
                $user->address['address'],
                $user->address['city'],
                $user->address['state'],
                $user->address['stateCode'],
                $user->address['postalCode'],
                $user->address['coordinates']['lat'],
                $user->address['coordinates']['lng'],
                $user->address['country'],
                $user->macAddress,
                $user->university,
                $user->bank['cardExpire'],
                $user->bank['cardNumber'],
                $user->bank['cardType'],
                $user->bank['currency'],
                $user->bank['iban'],
                $user->company['department'],
                $user->company['name'],
                $user->company['title'],
                $user->company['address']['address'],
                $user->company['address']['city'],
                $user->company['address']['state'],
                $user->company['address']['stateCode'],
                $user->company['address']['postalCode'],
                $user->company['address']['coordinates']['lat'],
                $user->company['address']['coordinates']['lng'],
                $user->company['address']['country'],
                $user->ein,
                $user->ssn,
                $user->userAgent,
                $user->crypto['coin'],
                $user->crypto['wallet'],
                $user->crypto['network'],
                $user->role
            ];
            $csvContent .= implode(',', $row) . "\n";
        }
        return $csvContent;
    }
    
    static function getAgeGroup($age)
    {
        if ($age >= 0 && $age <= 10) return '0-10';
        if ($age >= 11 && $age <= 20) return '11-20';
        if ($age >= 21 && $age <= 30) return '21-30';
        if ($age >= 31 && $age <= 40) return '31-40';
        if ($age >= 41 && $age <= 50) return '41-50';
        if ($age >= 51 && $age <= 60) return '51-60';
        if ($age >= 61 && $age <= 90) return '61-90';
        return '91+';
    }

    public static function get_users_summary($users) {
        $csvContent = '';
        
        // Total
        $totalRecords = count($users);
        $csvContent .= "registre,$totalRecords\n\n"; 

        // Recuento de generos 
        $genderCounts = array_count_values(array_column($users, 'gender'));
        $csvContent .= "Gender,Total\n";
        foreach ($genderCounts as $gender => $count) {
            $csvContent .= "$gender,$count\n";
        }
        $csvContent .= "\n";
    
        // Recuento de genero por grupo etareo
        $ageGroups = [
            '0-10' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            '11-20' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            '21-30' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            '31-40' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            '41-50' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            '51-60' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            '61-70' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            '71-80' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            '81-90' => ['Male' => 0, 'Female' => 0, 'Other' => 0],
            '91+' => ['Male' => 0, 'Female' => 0, 'Other' => 0]
        ];
    
        foreach ($users as $user) {
            $age = $user->age;
            $gender = $user->gender;
    
            $ageGroup = '91+';
            if ($age <= 10) $ageGroup = '0-10';
            else if ($age <= 20) $ageGroup = '11-20';
            else if ($age <= 30) $ageGroup = '21-30';
            else if ($age <= 40) $ageGroup = '31-40';
            else if ($age <= 50) $ageGroup = '41-50';
            else if ($age <= 60) $ageGroup = '51-60';
            else if ($age <= 70) $ageGroup = '61-70';
            else if ($age <= 80) $ageGroup = '71-80';
            else if ($age <= 90) $ageGroup = '81-90';
    
            if (isset($ageGroups[$ageGroup])) {
                if ($gender === 'Male') $ageGroups[$ageGroup]['Male']++;
                else if ($gender === 'Female') $ageGroups[$ageGroup]['Female']++;
                else $ageGroups[$ageGroup]['Other']++;
            }
        }
    
        $csvContent .= "age,Male,Female,Other\n";
        foreach ($ageGroups as $ageGroup => $counts) {
            $csvContent .= "$ageGroup,{$counts['Male']},{$counts['Female']},{$counts['Other']}\n";
        }
        $csvContent .= "\n"; 
    
        // Sección 4: Recuento de genero por ciudad
        $cityGenderCounts = [];
        foreach ($users as $user) {
            $city = $user->address['city'] ?? 'Unknown';
            $gender = $user->gender;

            if (!isset($cityGenderCounts[$city])) {
                $cityGenderCounts[$city] = ['Male' => 0, 'Female' => 0, 'Other' => 0];
            }

            if ($gender === 'Male') $cityGenderCounts[$city]['Male']++;
            else if ($gender === 'Female') $cityGenderCounts[$city]['Female']++;
            else $cityGenderCounts[$city]['Other']++;
        }
        
        $csvContent .= "City,Male,Female,Other\n";
        foreach ($cityGenderCounts as $city => $counts) {
            $csvContent .= "$city,{$counts['Male']},{$counts['Female']},{$counts['Other']}\n";
        }
        $csvContent .= "\n";
    
        // Recuento de SO segun UserAgent
        $osCounts = [];
        foreach ($users as $user) {
            $os = self::getOperatingSystemFromUserAgent($user->userAgent);
            if ($os) {
                if (!isset($osCounts[$os])) $osCounts[$os] = 0;
                $osCounts[$os]++;
            }
        }
        
        $csvContent .= "OS,Count\n";
        foreach ($osCounts as $os => $count) {
            $csvContent .= "$os,$count\n";
        }
    
        return $csvContent;
    }
    
    private static function getOperatingSystemFromUserAgent($userAgent) {
        if (strpos($userAgent, 'Windows NT 10.0') !== false) return 'Windows 10';
        if (strpos($userAgent, 'Windows NT 6.3') !== false) return 'Windows 8.1';
        if (strpos($userAgent, 'Windows NT 6.2') !== false) return 'Windows 8';
        if (strpos($userAgent, 'Windows NT 6.1') !== false) return 'Windows 7';
        if (strpos($userAgent, 'Macintosh; Intel Mac OS X') !== false) return 'Mac OS';
        if (strpos($userAgent, 'X11; Ubuntu; Linux x86_64') !== false) return 'Ubuntu';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iPhone') !== false) return 'iOS';
    
        return 'Unknown';
    }
    
}
?>
