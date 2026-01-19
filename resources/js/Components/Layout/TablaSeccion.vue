<script setup>
defineProps({
    data: {
        type: Array,
        default: () => [],
    },
    title: {
        // opcional, para poner un título encima de la tabla
        type: String,
        default: "",
    },
});
</script>

<template>
    <section
        class="max-w-[1100px] mx-auto mt-4 bg-white rounded-2xl shadow border border-gray-300"
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
        <div class="overflow-x-auto p-6">
            <table class="w-full text-sm border rounded-lg">
                <!-- HEAD -->
                <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                    <slot name="head" />
                </thead>

                <!-- BODY -->
                <tbody>
                    <slot name="body" />

                    <!-- EMPTY STATE -->
                    <tr v-if="!data.length">
                        <td
                            :colspan="$slots.head?.()[0].children.length || 1"
                            class="text-center py-6 text-gray-500"
                        >
                            No hay registros disponibles
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</template>
