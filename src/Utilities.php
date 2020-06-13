<?php

namespace Balsama;

use League\Csv\Writer;
use SplTempFileObject;

class Utilities
{

    public static function writeCsvTableFromData($data, $filename = 'data', $resetKeys = true)
    {
        if (!$resetKeys) {
            throw new \Exception('CSVs that preserve keys is not yet implemented.');
        }
        $headers = [];
        $largest = 0;
        foreach ($data as $regionName => $numbers) {
            $headers[] = $regionName;
            $data[$regionName] = array_values($numbers);

            $count = count($numbers);
            if ($count > $largest) {
                $largest = $count;
            }
        }

        $i = 0;
        while ($i < $largest) {
            foreach ($data as $regionNAme => $numbers) {
                $formatted[$i][] = (isset($numbers[$i])) ? $numbers[$i] : null;
            }
            $i++;
        }

        $csv = Writer::createFromFileObject(new SplTempFileObject());
        ;
        $csv->insertOne($headers);
        $csv->insertAll($formatted);

        $output = $csv->getContent();
        file_put_contents("./csv/$filename.csv", $output);
    }
}
