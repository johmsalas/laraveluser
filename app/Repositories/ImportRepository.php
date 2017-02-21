<?php

namespace App\Repositories;

use Excel;
use App\User;
use Validator;

class ImportRepository
{
    /**
     * imports users from a xls file provided
     * @param  string $filepath
     */
    public function importXLS($filepath) {
        return $this->excelImport($filepath);
    }

    /**
     * imports users from a xlsx file provided
     * @param  string $filepath
     */
    public function importXLSX($filepath) {
        return $this->excelImport($filepath);
    }

    /**
     * imports users from a tsv file provided
     * @param  string $filepath
     */
    public function importTSV($filepath) {
        // Another way to do this is using laravel-excel which support tsv
        return $this->SVImport($filepath, "\t");
    }

    /**
     * imports users from a csv file provided
     * @param  string $filepath
     */
    public function importCSV($filepath) {
        // Another way to do this is using laravel-excel which support csv
        return $this->SVImport($filepath, ',');
    }

    private function SVImport($filepath, $separator = ',') {
        $importRepository = $this;
        $rejectedUsers = collect();
        $output = [];
        try {
            $users = [];
            $file = fopen($filepath, 'r');
            while (!feof($file)) {
                $users[] = fgetcsv($file, 0, $separator);
            }
            fclose($file);
            $users = collect($users)->map(function($user) {
                return [
                    'name' => empty($user[0]) ? '' : $user[0],
                    'email' => empty($user[1]) ? '' : $user[1],
                    'phone' => empty($user[2]) ? '' : $user[2],
                ];
            });
            $importRepository->importUsers($users)->map(function($user) use ($rejectedUsers) {
                $rejectedUsers->push($user);
            });
        } catch (Exception $e) {

        }
        return $rejectedUsers;
    }

    /**
     * Validate and import the users
     * @var Collect<User> $users
     */
    private function importUsers($users) {
        $importRepository = $this;
        $rejectedUsers = collect();
        // Get emails that will be imported
        $emails = $users->pluck('email');
        // Check those emails that are already in the database.
        $repeatedEmails = (count($emails) > 0) ?
            User::whereIn('email', $emails)->get()->pluck('email')->toArray() : [];
        // Filter invalid users
        $users = $users->filter(function($user) use (
            $repeatedEmails,
            $importRepository,
            $rejectedUsers
        ) {
            $valid = $importRepository->isValidUser($user, $repeatedEmails);
            if (!$valid) {
                $rejectedUsers->push($user);
            }
            $repeatedEmails[] = $user['email'];
            return $valid;
        });
        User::insert($users->toArray());
        return $rejectedUsers;
    }

    /**
     * Import the users given an Excel file
     * @param string $filepath
     */
    private function excelImport($filepath) {
        $importRepository = $this;
        $rejectedUsers = collect();
        Excel::load($filepath, function($reader) use ($importRepository, $rejectedUsers) {
            $reader->each(function($sheet) use ($importRepository, $rejectedUsers) {
                $users = collect($sheet->all())->map(function($user) {
                    return [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                    ];
                });
                $importRepository->importUsers($users)->map(function($user) use ($rejectedUsers) {
                    $rejectedUsers->push($user);
                });
            });
        });
        return $rejectedUsers;
    }

    /**
     * Checks if the files provided for a user are completed and valid
     * @param  User  $user
     * @param  array  $existingEmails
     * @return boolean
     */
    private function isValidUser($user, $existingEmails = []) {
        $validator = Validator::make($user, [
            'name' => 'required|max:150',
            'email' => 'required|email',
        ]);

        return (!$validator->fails() && !in_array($user['email'], $existingEmails));
    }
}
