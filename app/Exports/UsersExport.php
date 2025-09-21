<?php

namespace App\Exports;

use App\Models\User;
use App\Services\Filters\UserFilterService;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromView, WithStyles
{
    private $params;

    protected $filterService;

    public function __construct($params, UserFilterService $filterService)
    {
        $this->params = $params;
        $this->filterService = $filterService;
    }

    public function view(): View
    {
        $query = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'super-admin');
        })->with(['roles', 'branch']);

        if ($this->params) {
            $this->filterService->applyFilters($query, $this->params);
        }

        $users = $query->get();

        return view('CP.user-management.users.export', [
            'users' => $users,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
