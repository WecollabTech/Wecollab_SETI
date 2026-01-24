<!-- CardInput.vue -->
<template>
    <div
        :class="[
            'bg-white rounded-xl border border-gray-200 shadow-sm p-4 transition hover:shadow-md',
            $attrs.class,
        ]"
    >
        <slot />
    </div>
</template>

<script setup>
defineOptions({
    inheritAttrs: false,
});
</script>
