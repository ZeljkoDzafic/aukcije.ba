<?php

declare(strict_types=1);

it('renders new seller and admin detail pages', function () {
    $this->withoutVite();

    $pages = [
        view('pages.seller.orders.show')->render(),
        view('pages.admin.users.show')->render(),
        view('pages.admin.auctions.show')->render(),
        view('pages.admin.disputes.show')->render(),
    ];

    foreach ($pages as $page) {
        expect($page)->toContain('text-slate-900');
    }
});
