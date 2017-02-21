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
        return $this->SVExport('tsv', $collection, function (&$vals, $key, $output) {
            fputcsv($output, $vals, "\t", '"');
        }, $name);
    }

    /**
     * imports users from a csv file provided
     * @param  string $filepath
     */
    public function importCSV($filepath) {
        // Another way to do this is using laravel-excel which support csv
        return $this->SVExport('csv', $collection, function (&$vals, $key, $output) {
            fputcsv($output, $vals, ';', '"');
        }, $name);
    }

    private function SVExport($format, $collection, $formatter, $name = 'file') {
        header('Content-Type: text/' . $format . '; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $name . '.' . $format);

        $data = $collection->toArray();
        $outstream = fopen("php://output", 'w');
        array_walk($data, $formatter, $outstream);
        fclose($outstream);
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
