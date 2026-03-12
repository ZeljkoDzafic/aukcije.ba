import { createApp, h } from 'vue';

import AuctionTimer from './AuctionTimer.vue';
import BiddingConsole from './BiddingConsole.vue';

const mountBiddingConsole = () => {
    const element = document.getElementById('bidding-console-root');

    if (!element) {
        return;
    }

    createApp({
        render() {
            return h(BiddingConsole, {
                auctionId: Number(element.dataset.auctionId),
                currentPrice: Number(element.dataset.currentPrice),
                minimumBid: Number(element.dataset.minimumBid),
                endsAt: element.dataset.endsAt,
                currency: element.dataset.currency ?? 'BAM',
                canBid: element.dataset.canBid === 'true',
                endpoint: element.dataset.endpoint ?? '',
                userId: element.dataset.userId ? Number(element.dataset.userId) : null,
            });
        },
    }).component('AuctionTimer', AuctionTimer).mount(element);
};

mountBiddingConsole();
