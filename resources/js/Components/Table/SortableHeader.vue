<script setup>
const props = defineProps({
    label: {
        type: String,
        required: true,
    },
    column: {
        type: String,
        required: true,
    },
    sortBy: {
        type: String,
        required: true,
    },
    sortDirection: {
        type: String,
        required: true,
        validator: (value) => ["asc", "desc"].includes(value),
    },
    onClick: {
        type: Function,
        required: true,
    },
});
</script>

<template>
    <th
        class="py-3 px-4 text-left cursor-pointer hover:bg-gray-100 transition group border-b-2 border-gray-200"
        @click="onClick(column)"
        :title="`Ordenar por ${label}`"
    >
        <div class="flex items-center gap-2">
            <span
                class="font-semibold text-gray-700 text-sm uppercase tracking-wider"
                >{{ label }}</span
            >
            <div class="flex flex-col items-center -space-y-1">
                <svg
                    :class="{
                        'text-indigo-600 fill-indigo-600':
                            sortBy === column && sortDirection === 'asc',
                        'text-gray-400':
                            sortBy !== column || sortDirection !== 'asc',
                    }"
                    class="h-3 w-3 transition-transform"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 15l7-7 7 7"
                    />
                </svg>
                <svg
                    :class="{
                        'text-indigo-600 fill-indigo-600':
                            sortBy === column && sortDirection === 'desc',
                        'text-gray-400':
                            sortBy !== column || sortDirection !== 'desc',
                    }"
                    class="h-3 w-3 transition-transform"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
            </div>
        </div>
    </th>
</template>

<style scoped>
th:has(.text-indigo-600) {
    @apply bg-indigo-50/50;
}
</style>
