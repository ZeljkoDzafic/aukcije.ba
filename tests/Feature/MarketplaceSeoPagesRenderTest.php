<?php

it('renders marketplace seo pages with expected metadata', function () {
    $this->withoutVite();

    $auctionList = $this->get(route('auctions.index'));
    $auctionList->assertOk()->assertSee('Aktivne aukcije');

    $auctionDetail = $this->get(route('auctions.show', ['auction' => 1]));
    $auctionDetail->assertOk()->assertSee('application/ld+json', false);

    $categoryIndex = $this->get(route('categories.index'));
    $categoryIndex->assertOk()->assertSee('Kategorije');

    $categoryShow = $this->get(route('categories.show', ['category' => 'elektronika']));
    $categoryShow->assertOk()->assertSee('Elektronika');
});
