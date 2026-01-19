<script setup>
defineProps({
    createText: { type: String, default: "Nuevo" },
    createHref: { type: String, default: "#" },
    showCreate: { type: Boolean, default: true },
});
</script>

<template>
    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2"
    >
        <div class="flex flex-1 flex-col sm:flex-row gap-2">
            <slot name="left" />
        </div>

        <div class="flex items-center gap-2 mt-2 sm:mt-0">
            <slot name="right">
                <template v-if="showCreate">
                    <a
                        :href="createHref"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition"
                    >
                        {{ createText }}
                    </a>
                </template>
            </slot>
        </div>
    </div>
</template>
