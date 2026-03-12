<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';

const props = defineProps({
    endsAt: {
        type: String,
        required: true,
    },
    serverNow: {
        type: String,
        default: null,
    },
});

const now = ref(props.serverNow ? new Date(props.serverNow).getTime() : Date.now());
let intervalId;

const remainingMs = computed(() => new Date(props.endsAt).getTime() - now.value);

const toneClass = computed(() => {
    if (remainingMs.value <= 5 * 60 * 1000) {
        return remainingMs.value <= 2 * 60 * 1000 ? 'countdown-urgent animate-pulse' : 'countdown-urgent';
    }

    if (remainingMs.value <= 60 * 60 * 1000) {
        return 'countdown-warning';
    }

    return 'countdown-normal';
});

const formattedTime = computed(() => {
    if (remainingMs.value <= 0) {
        return 'Završeno';
    }

    const totalSeconds = Math.floor(remainingMs.value / 1000);
    const days = Math.floor(totalSeconds / 86400);
    const hours = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);

    return `${days}d ${hours}h ${minutes}m`;
});

onMounted(() => {
    now.value = props.serverNow ? new Date(props.serverNow).getTime() : Date.now();

    intervalId = window.setInterval(() => {
        now.value = Date.now();
    }, 1000);
});

onBeforeUnmount(() => {
    window.clearInterval(intervalId);
});
</script>

<template>
    <p :class="toneClass">{{ formattedTime }}</p>
</template>
