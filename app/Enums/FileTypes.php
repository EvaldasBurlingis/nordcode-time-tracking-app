<?php

namespace App\Enums;

enum FileTypes: string
{
    case csv = 'csv';
    case xlsx = 'xlsx';
    case pdf = 'pdf';
}
