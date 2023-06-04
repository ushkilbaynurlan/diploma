<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketsExport implements FromCollection ,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = Ticket::get();
        foreach($data as $k => $tickets)
        {
            $category = Ticket::category($tickets->category);
            unset($tickets->id,$tickets->attachments,$tickets->note,$tickets->created_by,$tickets->created_at,$tickets->updated_at);
            $data[$k]['category'] = $category;
        }
        return $data;
    }

    public function headings(): array
    {
        return [
            "Ticket ID",
            "Name",
            "Email",
            "Category",
            "Subject",
            "Status",
            "Description",
        ];
    }
}



