<?php

it('renders upgraded admin utility and wallet pages', function () {
    $this->withoutVite();

    $pages = [
        view('pages.admin.categories.index')->render(),
        view('pages.admin.statistics')->render(),
        view('pages.wallet.index')->render(),
    ];

    foreach ($pages as $page) {
        expect($page)->toBeString()->not->toBeEmpty();
    }
});
