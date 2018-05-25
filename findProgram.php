<?php

function cleanString($string, $keepspace = false)
{
    $string = str_replace(['du ', 'les ', 'des ', 'la ', 'le ', 'un ', 'une ', 'de la ', 'quelque chose', 'truc', ',', '.'], '', $string);
    if (!$keepspace) {
        $string = str_replace(' ', '', strtolower($string));
        $string = str_replace('s', '', $string);
    }

    return $string;
}


function findProgram($anything)
{
    $type = trim(str_replace('à la', '', $anything));
    if (strpos('%'.$type, 'dessin animé')) {
        $type = 'animation';
    }
    if (!is_file('/dev/shm/tvguide.json')) {
        include __DIR__.'/makeJson.php';
    }

    $selectedPrograms = [];
    $data = json_decode(file_get_contents('/dev/shm/tvguide.json'));
    $categories = $data->categories;
    $programs = $data->programs;

    $cleanedType = cleanString($type);
    foreach ($categories as $category => $programs) {
        if (strpos('%'.cleanString($category), $cleanedType)) {
            $selectedPrograms = array_merge($selectedPrograms, $programs);
        } else {
            foreach ($programs as $program) {
                if (cleanString($program->channel_name) === $cleanedType) {
                    $selectedPrograms[] = $program;
                }
                if (cleanString($program->channel_name) === $cleanedType) {
                    $selectedPrograms[] = $program;
                }
                if (strpos('%'.cleanString($program->title), $cleanedType)) {
                    $selectedPrograms[] = $program;
                }
            }
        }
    }

    shuffle($selectedPrograms);
    $timeMin = time() - 60 * 5;
    $timeMax = time() + 60 * 5;
    foreach ($selectedPrograms as $program) {
        if ($program->start > $timeMax || $program->end < $timeMin) {
            continue;
        }
        if (!is_file('callback.php')) {
            copy('callback.php.dist', 'callback.php');
        }
        include 'callback.php';

        return 'Ok, j\'ai mis "'.$program->title.'" sur '.$program->channel_name;
    }

    return false;
}
