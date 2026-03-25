<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class BaseLayout extends Component
{
    public $title;

    public function __construct(?string $title = null)
    {
        $this->title = $title ?? config('app.name', 'Laravel');
    }

    public function render(): View
    {
        return view('layouts.base');
    }
}
