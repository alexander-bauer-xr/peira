<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Meta extends Component
{
    public string $resolvedTitle;
    public string $resolvedDescription;
    public string $resolvedImage;

    public function __construct(
        public array|string|null $title = null,
        public array|string|null $description = null,
        public array|string|null $image = null,
    ) {
        $this->resolvedTitle = $this->localize($this->title) ?? 'Peira';
        $this->resolvedDescription = $this->localize($this->description) ?? 'Künstlerische Plattform & Projekte';
        $this->resolvedImage = $this->localize($this->image) ?? asset('/img/header.png');
    }

    protected function localize(array|string|null $value): ?string
    {
        if (is_array($value)) {
            return $value[app()->getLocale()] ?? reset($value);
        }

        return $value;
    }

    public function render()
    {
        return view('components.meta', [
            'title' => $this->resolvedTitle,
            'description' => $this->resolvedDescription,
            'image' => $this->resolvedImage,
        ]);
    }
}