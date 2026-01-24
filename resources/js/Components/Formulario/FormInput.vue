<script setup>
defineProps({
    label: String,
    modelValue: [String, Number, Boolean],
    type: { type: String, default: "text" },
    placeholder: String,
    options: { type: Array, default: () => [] },
    error: String,
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <div class="space-y-1">
        <label v-if="type !== 'checkbox'" class="block text-sm font-medium">
            {{ label }}
        </label>

        <input
            v-if="
                type !== 'select' && type !== 'textarea' && type !== 'checkbox'
            "
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            class="w-full px-3 py-2 rounded-md border focus:ring"
            :class="
                error
                    ? 'border-red-500 focus:ring-red-200'
                    : 'border-gray-300 focus:ring-blue-200'
            "
        />

        <textarea
            v-else-if="type === 'textarea'"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            class="w-full px-3 py-2 rounded-md border focus:ring resize-none"
            :class="
                error
                    ? 'border-red-500 focus:ring-red-200'
                    : 'border-gray-300 focus:ring-blue-200'
            "
        ></textarea>

        <select
            v-else-if="type === 'select'"
            :value="modelValue"
            @change="$emit('update:modelValue', $event.target.value)"
            class="w-full px-3 py-2 rounded-md border focus:ring"
            :class="
                error
                    ? 'border-red-500 focus:ring-red-200'
                    : 'border-gray-300 focus:ring-blue-200'
            "
        >
            <option value="">Selecciona una opción</option>
            <option v-for="(opt, i) in options" :key="i" :value="opt.value">
                {{ opt.label }}
            </option>
        </select>

        <p v-if="error" class="text-red-600 text-sm">
            {{ error }}
        </p>
    </div>
</template>
