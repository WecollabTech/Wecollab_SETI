<template>
    <div class="text-center">
        <h2 class="text-3xl font-bold mb-3 text-[#422782]">
            {{ title }}
        </h2>
        <div class="h-1 w-20 bg-blue-600 rounded-full mx-auto mb-4"></div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: true,
    },
});
</script>
