<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class PageLoader extends Component
{
    public string $page = 'dashboard';

    protected $listeners = ['navigateTo'];

    public function navigateTo($page)
    {
        $this->page = $page;
    }

    public function render()
    {
        return view('livewire.admin.page-loader');
    }
}
