<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class Popup extends Component
{
    public function mount()
    {
        $this->dispatch('open-modal', id: 'edit-user');
    }

    public function render()
    {
        return view('livewire.dashboard.popup');
    }
}
