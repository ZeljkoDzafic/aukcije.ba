<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

import AuctionTimer from './AuctionTimer.vue';
import axios, { listenToAuctionUpdates, userChannel } from '../js/bootstrap';

const props = defineProps({
    auctionId: {
        type: Number,
        required: true,
    },
    currentPrice: {
        type: Number,
        required: true,
    },
    minimumBid: {
        type: Number,
        required: true,
    },
    endsAt: {
        type: String,
        required: true,
    },
    currency: {
        type: String,
        default: 'BAM',
    },
    canBid: {
        type: Boolean,
        default: true,
    },
    endpoint: {
        type: String,
        default: '',
    },
    userId: {
        type: Number,
        default: null,
    },
});

const liveCurrentPrice = ref(props.currentPrice);
const liveMinimumBid = ref(props.minimumBid);
const liveEndsAt = ref(props.endsAt);
const amount = ref(props.minimumBid);
const proxyEnabled = ref(false);
const proxyMax = ref(props.minimumBid + 50);
const state = ref('idle');
const errorMessage = ref('');
const feedbackMessage = ref('');
const outbidMessage = ref('');
const showConfetti = ref(false);
let stopAuctionUpdates = null;
let privateChannel = null;
let confettiTimeout = null;

const disabled = computed(() => !props.canBid || state.value === 'submitting');
const validationError = computed(() => {
    if (Number(amount.value) < liveMinimumBid.value) {
        return `Minimalni sljedeći bid je ${liveMinimumBid.value.toFixed(2)} ${props.currency}.`;
    }

    if (proxyEnabled.value && Number(proxyMax.value) < liveMinimumBid.value) {
        return `Proxy maksimum mora biti najmanje ${liveMinimumBid.value.toFixed(2)} ${props.currency}.`;
    }

    return '';
});

const submitLabel = computed(() => {
    if (!props.canBid) {
        return 'Aukcija završena';
    }

    return state.value === 'submitting' ? 'Slanje...' : 'Pošalji bid';
});

const handleBidPlaced = (data) => {
    liveCurrentPrice.value = Number(data.current_price ?? liveCurrentPrice.value);
    liveMinimumBid.value = Number(data.next_minimum_bid ?? data.current_price + 5 ?? liveCurrentPrice.value + 1);
    liveEndsAt.value = data.auction_ends_at ?? liveEndsAt.value;
    feedbackMessage.value = 'Nova ponuda je evidentirana u realnom vremenu.';
};

const handleAuctionExtended = (data) => {
    liveEndsAt.value = data.new_ends_at ?? data.ends_at ?? liveEndsAt.value;
    feedbackMessage.value = 'Aukcija je produžena zbog anti-sniping pravila.';
};

const triggerConfetti = () => {
    showConfetti.value = true;
    window.clearTimeout(confettiTimeout);
    confettiTimeout = window.setTimeout(() => {
        showConfetti.value = false;
    }, 2400);
};

const handleAuctionEnded = (data) => {
    if (props.userId && Number(data?.winner?.id) === props.userId) {
        feedbackMessage.value = 'Aukcija je završena i pobijedili ste.';
        triggerConfetti();
        return;
    }

    feedbackMessage.value = 'Aukcija je završena. Dalje licitiranje je zaključano.';
};

const submitBid = async () => {
    if (disabled.value || validationError.value) {
        errorMessage.value = validationError.value;
        return;
    }

    state.value = 'submitting';
    errorMessage.value = '';
    feedbackMessage.value = '';

    if (!props.endpoint) {
        window.setTimeout(() => {
            state.value = 'success';
            feedbackMessage.value = 'Demo feedback: bid je prihvaćen i čeka backend integraciju.';
        }, 600);
        return;
    }

    try {
        const response = await axios.post(props.endpoint, {
            amount: Number(amount.value),
            is_proxy: proxyEnabled.value,
            max_proxy_amount: proxyEnabled.value ? Number(proxyMax.value) : null,
        });

        const payload = response.data?.data ?? {};
        liveCurrentPrice.value = Number(payload.current_price ?? liveCurrentPrice.value);
        liveMinimumBid.value = Number(payload.next_minimum_bid ?? liveMinimumBid.value);
        liveEndsAt.value = payload.auction_ends_at ?? liveEndsAt.value;
        state.value = 'success';
        feedbackMessage.value = 'Ponuda je uspješno poslana.';
    } catch (error) {
        state.value = 'idle';
        errorMessage.value = error.response?.data?.error?.message ?? 'Bid trenutno nije moguće poslati.';
    }
};

onMounted(() => {
    stopAuctionUpdates = listenToAuctionUpdates(props.auctionId, {
        onBid: handleBidPlaced,
        onExtended: handleAuctionExtended,
        onEnded: handleAuctionEnded,
    });

    if (props.userId) {
        privateChannel = userChannel(props.userId);
        privateChannel.listen('OutbidNotification', (event) => {
            outbidMessage.value = event.message ?? 'Nadlicitirani ste na ovoj aukciji.';
        });
        privateChannel.listen('AuctionWon', (event) => {
            feedbackMessage.value = event.message ?? 'Čestitamo, vodeći ste bidder.';
            triggerConfetti();
        });
    }
});

onBeforeUnmount(() => {
    stopAuctionUpdates?.();

    if (privateChannel) {
        privateChannel.stopListening('OutbidNotification');
        privateChannel.stopListening('AuctionWon');
    }

    window.clearTimeout(confettiTimeout);
});
</script>

<template>
    <div class="relative rounded-3xl border border-slate-200 bg-slate-50 p-5">
        <div v-if="showConfetti" class="pointer-events-none absolute inset-x-0 top-0 flex justify-center gap-2 overflow-hidden py-2">
            <span v-for="piece in 16" :key="piece" class="h-3 w-2 animate-bounce rounded-full bg-trust-500 opacity-80" :style="{ animationDelay: `${piece * 40}ms` }"></span>
        </div>
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">Vue bidding console</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ liveCurrentPrice.toFixed(2) }} {{ currency }}</p>
            </div>
            <AuctionTimer :ends-at="liveEndsAt" :server-now="new Date().toISOString()" />
        </div>

        <div class="mt-5 grid gap-4">
            <div v-if="outbidMessage" class="alert-warning">{{ outbidMessage }}</div>
            <div v-if="errorMessage" class="alert-danger">{{ errorMessage }}</div>
            <div v-if="feedbackMessage" class="alert-success">{{ feedbackMessage }}</div>

            <label class="space-y-1 text-sm">
                <span class="font-medium text-slate-700">Bid amount</span>
                <input v-model="amount" type="number" class="input" :min="liveMinimumBid" :disabled="disabled">
            </label>

            <label class="inline-flex items-center gap-3 text-sm text-slate-700">
                <input v-model="proxyEnabled" type="checkbox" class="rounded border-slate-300 text-trust-600 focus:ring-trust-500" :disabled="disabled">
                <span>Proxy bidding</span>
            </label>

            <label v-if="proxyEnabled" class="space-y-1 text-sm">
                <span class="font-medium text-slate-700">Maksimalni proxy bid</span>
                <input v-model="proxyMax" type="number" class="input" :min="liveMinimumBid" :disabled="disabled">
            </label>

            <button type="button" class="btn-primary w-full" :disabled="disabled" @click="submitBid">
                {{ submitLabel }}
            </button>

            <p class="text-sm text-slate-500">Minimalni sljedeći bid: {{ liveMinimumBid.toFixed(2) }} {{ currency }}</p>
        </div>
    </div>
</template>
