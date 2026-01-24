<script setup>
defineProps({
    data: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <section
        class="w-full max-w-none 2xl:max-w-[1400px] mx-auto bg-white rounded-2xl shadow-2xl border border-gray-300"
    >
        <!-- TÍTULO OPCIONAL -->
        <div
            v-if="title"
            class="px-6 py-4 border-b font-semibold text-lg text-gray-800"
        >
            {{ title }}
        </div>

        <!-- TOOLBAR SLOT OPCIONAL -->
        <div v-if="$slots.toolbar" class="px-6 py-4 border-b">
            <slot name="toolbar" />
        </div>

        <!-- TABLA -->
        <div class="overflow-x-auto relative p-6">
            <table class="w-full text-sm border rounded-lg table-auto">
                <!-- HEAD -->
                <thead
                    class="bg-gray-100 text-xs uppercase text-gray-600 sticky top-0 z-10"
                >
                    <slot name="head" />
                </thead>

                <!-- BODY -->
                <tbody>
                    <slot name="body" />

                    <!-- EMPTY STATE -->
                    <tr v-if="!data.length" class="bg-gray-50">
                        <td
                            :colspan="$slots.head?.()[0].children.length || 1"
                            class="text-center py-6 text-gray-400 flex justify-center items-center gap-2"
                        >
                            <span class="text-2xl">📭</span>
                            No hay registros disponibles
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
