<?php

it('renders the main public pages', function () {
    $this->withoutVite();

    $routes = [
        route('home'),
        route('auctions.index'),
        route('categories.index'),
        route('search'),
        route('login'),
        route('register'),
        route('password.request'),
    ];

    foreach ($routes as $url) {
        $this->get($url)->assertOk();
    }
});
