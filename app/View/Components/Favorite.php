<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Favorite extends Component
{

    public $laptop;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($laptop)
    {
        $this->laptop = $laptop;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.favorite');
    }
}
