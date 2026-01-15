<script setup>
defineProps({
    paso: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <div class="flex justify-center mb-6">
        <div class="flex items-center gap-4">
            <div v-for="n in 4" :key="n" class="flex items-center">
                <div
                    :class="[
                        'w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold',
                        paso >= n
                            ? 'bg-blue-600 text-white'
                            : 'bg-gray-300 text-gray-600',
                    ]"
                >
                    {{ n }}
                </div>

                <div
                    v-if="n < 4"
                    class="w-10 h-1"
                    :class="paso > n ? 'bg-blue-600' : 'bg-gray-300'"
                />
            </div>
        </div>
    </div>
</template>
