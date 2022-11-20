<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdminUserFavoriteCard extends Component
{
    public $userFavorite;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($userFavorite)
    {
        $this->userFavorite = $userFavorite;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin-user-favorite-card');
    }
}
