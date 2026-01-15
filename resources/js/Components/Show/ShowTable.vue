<script setup>
defineProps({
    title: String,
    headers: Array,
    rows: Array,
});
</script>

<template>
    <div class="bg-white rounded-xl shadow mb-6">
        <div class="border-b px-5 py-3 font-semibold text-gray-700">
            {{ title }}
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="(h, i) in headers"
                            :key="i"
                            class="px-4 py-3 text-left font-medium text-gray-600"
                        >
                            {{ h }}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="rows.length === 0" class="border-t">
                        <td
                            :colspan="headers.length"
                            class="px-4 py-4 text-center text-gray-500"
                        >
                            No hay registros
                        </td>
                    </tr>

                    <tr
                        v-for="(row, i) in rows"
                        :key="i"
                        class="border-t hover:bg-gray-50"
                    >
                        <td v-for="(cell, c) in row" :key="c" class="px-4 py-3">
                            {{ cell }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
