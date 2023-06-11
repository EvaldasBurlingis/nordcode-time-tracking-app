<?php

namespace Modules\Task\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TasksExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct(
        private string $dateFrom,
        private string $dateTo,
    )
    {
    }

    public function collection(): Collection
    {
        $collection = collect(Auth::user()->tasks()->dateBetween($this->dateFrom, $this->dateTo)
            ->select(['title', 'comment', 'date', 'time_spent'])->get()->toArray());

        $collection->push([
            'title' => 'Total',
            'comment' => '',
            'date' => '',
            'time_spent' => $collection->sum('time_spent'),
        ]);

        return $collection;
    }

    public function map($row): array
    {
        return [
            $row['title'],
            $row['comment'],
            $row['date'],
            $row['time_spent'],
        ];
    }

    public function headings(): array
    {
        return [
            'Title',
            'Comment',
            'Date',
            'Time Spent(minutes)',
        ];
    }
}
