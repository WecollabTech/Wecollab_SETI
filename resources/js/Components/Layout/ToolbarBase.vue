<script setup>
defineProps({
    createText: { type: String, default: "Nuevo" },
    createHref: { type: String, default: "#" },
    showCreate: { type: Boolean, default: true },
});
</script>

<template>
    <div class="flex justify-between items-center gap-4">
        <!-- LEFT SLOT -->
        <div class="flex items-center gap-3">
            <slot name="left" />
        </div>

        <!-- RIGHT SLOT O BOTÓN -->
        <div class="flex items-center gap-2">
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
