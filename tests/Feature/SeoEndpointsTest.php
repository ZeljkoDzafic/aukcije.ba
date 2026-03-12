<?php

declare(strict_types=1);

it('renders sitemap xml and robots txt', function () {
    $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    $this->get(route('robots'))
        ->assertOk()
        ->assertSee('Sitemap:', false);
});
