<script setup>
defineProps({
    label: String,

    modelValue: {
        type: [String, Number, Boolean], // 👈 IMPORTANTE
        default: "",
    },

    type: {
        type: String,
        default: "text",
    },

    placeholder: {
        type: String,
        default: "",
    },

    options: {
        type: Array,
        default: () => [],
    },

    required: Boolean,
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <div class="space-y-1">
        <label class="block text-sm font-medium text-gray-700">
            {{ label }}
        </label>

        <!-- TEXT / NUMBER -->
        <input
            v-if="
                type !== 'select' && type !== 'textarea' && type !== 'checkbox'
            "
            :type="type"
            :required="required"
            :placeholder="placeholder"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-200"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        />

        <!-- TEXTAREA -->
        <textarea
            v-else-if="type === 'textarea'"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-200"
            :placeholder="placeholder"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
        ></textarea>

        <!-- SELECT -->
        <select
            v-else-if="type === 'select'"
            class="w-full border rounded-md px-3 py-2 focus:ring focus:ring-blue-200"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option value="">Selecciona una opcion</option>
            <option v-for="(opt, i) in options" :key="i" :value="opt.value">
                {{ opt.label }}
            </option>
        </select>

        <!-- CHECKBOX -->
        <div v-else-if="type === 'checkbox'" class="flex items-center gap-2">
            <input
                type="checkbox"
                :checked="modelValue"
                @change="$emit('update:modelValue', $event.target.checked)"
                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <span class="text-sm text-gray-600">
                {{ label }}
            </span>
        </div>
    </div>
</template>
