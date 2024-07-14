<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableCell extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $type,
        public array $column
    ) {
        //
    }

    public function getStyles()
    {
        $styles = [];
        if (isset($this->column['width'])) {
            $styles[] = 'width: ' . $this->column['width'] . 'px;';
        }

        if (isset($this->column['maxWidth'])) {
            $styles[] = 'max-width: ' . $this->column['maxWidth'] . 'px;';
        }

        if (isset($this->column['minWidth'])) {
            $styles[] = 'min-width: ' . $this->column['minWidth'] . 'px;';
        }

        if (isset($this->column['align'])) {
            $styles[] = 'text-align: ' . $this->column['align'] . ';';
        } else {
            $styles[] = 'text-align: left;';
        }

        return implode('', $styles);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.table-cell');
    }
}
