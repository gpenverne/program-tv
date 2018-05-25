#!/usr/bin/php
<?php
$localFile = '/dev/shm/tvguide.xml';
shell_exec(__DIR__.'/cron.sh');
$categories = [];
$xml = simplexml_load_file($localFile);
unlink($localFile);
if (!is_file('channels.php')) {
    copy('channels.php.dist', 'channels.php');
}
include "channels.php";
foreach ($xml->channel as $channel) {
    foreach ($channel->children() as $child) {
        if ($child->getName() === 'display-name') {
            foreach ($channel->attributes() as $key => $value) {
                $channels[(string) $value] = (string) $child;
            }
        }
    }
}

$programs = [];
foreach ($xml->programme as $program) {
    $category = trim(strtolower((string)$program->category));
    $d = explode(':', $category);
    if (!$d[0]) {
        continue;
    }
    $category = trim($d[0]);
    if (!isset($categories[$category])) {
        $categories[$category] = [];
    }

    foreach ($program->attributes() as $key => $value) {
        if ((string) $key === 'channel') {
            $channel = (string) $value;
        }
        if ((string) $key === 'start') {
            $d = new \DateTime();
            $year = substr($value, 0, 4);
            $month = substr($value, 4, 2);
            $day = substr($value, 6, 2);
            $hour = substr($value, 8, 2);
            $minute = substr($value, 10, 2);
            $d->setDate($year, $month, $day);
            $d->setTime($hour, $minute);
            $start = $d;
        }
        if ((string) $key === 'stop') {
            $d = new \DateTime();
            $year = substr($value, 0, 4);
            $month = substr($value, 4, 2);
            $day = substr($value, 6, 2);
            $hour = substr($value, 8, 2);
            $minute = substr($value, 10, 2);
            $d->setDate($year, $month, $day);
            $d->setTime($hour, $minute);
            $end = $d;
        }
    }

    $result = new \stdClass();
    $result->start = $start->getTimestamp();
    $result->category = (string) $program->category;
    $result->title = (string) $program->title;
    $result->end = $end->getTimestamp();
    $result->channel = getChannelNumber($channels[$channel]);
    $result->channel_name = $channels[$channel];
    if (null === $result->channel) {
        continue;
    }
    $programs[] = $result;
    $categories[$category][] = $result;
}

$results = new \stdClass();
$results->categories = $categories;
$results->programs = $programs;
$json = json_encode($results);
file_put_contents('/dev/shm/tvguide.json', $json);
