<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public ?string $type = null,
        public ?string $variant = null,
        public ?bool $disable = false,
    ) {
        //
    }

    public function getVariant()
    {
        $classes = '';
        if ($this->variant === 'contained') {
            $classes = 'bg-dark-purple text-white hover:opacity-90';
        } else if ($this->variant === 'outlined') {
            $classes = 'bg-white border-[1px] border-dark-purple text-dark-purple hover:opacity-90';
        } else {
            $classes = "text-dark-purple hover:bg-dark-purple/5";
        }

        return $classes;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
