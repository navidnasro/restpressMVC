<?php

namespace restpressMVC\core\storage;

use restpressMVC\bootstraps\Environment;

class Storage
{
    private static array $readData = [];

    public static function getJsonContent(string $path)
    {
        if (isset(self::$readData[$path]))
            return self::$readData[$path];

        $content = file_get_contents($path);
        $content = json_decode($content,true);
        self::$readData[$path] = $content;

        return $content;
    }

    public static function getJsonDataWhere(string $path,string $key)
    {
        $content = self::getJsonContent($path);
        return $content[$key] ? $content[$key] : false;
    }

    public static function log(array $entry): void
    {
        $date = date('Y-m-d');

        if (!is_dir(Environment::Storage.'/logs'))
            mkdir(Environment::Storage.'/logs', 0755, true);

        $logPath = Environment::Storage.'/logs/log-'.$date.'.json';

        $id = get_option('logs-id-increment-number');
        $id = $id ? $id : 0;

        $entry['date'] = date('l, d/F/Y H:i:s');
        $entry['id'] = ++$id;

        update_option('logs-id-increment-number',$id);

        $logs = [];
        if (file_exists($logPath))
            $logs = self::getJsonContent($logPath);

        $logs[] = $entry;

        file_put_contents($logPath, json_encode($logs));
    }

    public static function deleteLogByTimeline(string $timeline): bool
    {
        $logFiles = glob(Environment::Storage.'/logs/log-*.json');
        $currentTime = strtotime('today'); // Current timestamp

        // Define the time range for each timeline
        $timeRanges = [
            'all' => 0,
            'last_day' => strtotime('-1 day', $currentTime),
            'last_month' => strtotime('-30 days', $currentTime),
            'last_3_months' => strtotime('-90 days', $currentTime),
            'last_6_months' => strtotime('-180 days', $currentTime),
            'last_year' => strtotime('-365 days', $currentTime),
        ];

        if (!isset($timeRanges[$timeline]))
            return false;

        $cutoffTime = $timeRanges[$timeline];

        foreach ($logFiles as $logFile)
        {
            // Extract date from file name (e.g., log-2024-10-20.json)
            if (preg_match('/log-(\d{4}-\d{2}-\d{2})\.json/',basename($logFile),$matches))
            {
                $logDate = $matches[1];
                $logTimestamp = strtotime($logDate);

                // Check if log file matches the timeline
                if ($timeline === 'all' || ($logTimestamp >= $cutoffTime && $logTimestamp != $currentTime))
                {
                    return unlink($logFile);
                }
            }
        }

        return false;
    }
}