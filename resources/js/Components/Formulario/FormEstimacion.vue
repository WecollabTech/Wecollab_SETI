<script setup>
defineProps({
    label: String,

    // Cambiar modelValue para aceptar objeto también
    modelValue: {
        type: [String, Number, Boolean, Array, Object], // 🔹 Agregamos Object
        default: () => ({
            tipoImplementacion: "",
            fecha: "",
            complejidad: "",
            integraciones: [],
            comentarios: "",
        }),
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

        <!-- TEXT / NUMBER / DATE -->
        <input
            v-if="
                type !== 'select' &&
                type !== 'textarea' &&
                type !== 'checkbox' &&
                type !== 'radio'
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
            <option value="">Seleccione una opcion</option>
            <option v-for="(opt, i) in options" :key="i" :value="opt.value">
                {{ opt.label }}
            </option>
        </select>

        <!-- CHECKBOX -->
        <div v-else-if="type === 'checkbox'" class="flex flex-wrap gap-3">
            <label
                v-for="(opt, i) in options"
                :key="i"
                class="flex items-center gap-2 cursor-pointer"
            >
                <input
                    type="checkbox"
                    :value="opt.value"
                    :checked="
                        Array.isArray(modelValue)
                            ? modelValue.includes(opt.value)
                            : modelValue
                    "
                    @change="
                        (e) => {
                            let newVal = Array.isArray(modelValue)
                                ? [...modelValue]
                                : [];
                            if (e.target.checked) newVal.push(opt.value);
                            else newVal.splice(newVal.indexOf(opt.value), 1);
                            $emit('update:modelValue', newVal);
                        }
                    "
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-600">{{ opt.label }}</span>
            </label>
        </div>

        <!-- RADIO -->
        <div v-else-if="type === 'radio'" class="flex flex-wrap gap-5">
            <label
                v-for="(opt, i) in options"
                :key="i"
                class="flex items-center gap-2 cursor-pointer"
            >
                <input
                    type="radio"
                    :name="label"
                    :value="opt.value"
                    :checked="modelValue === opt.value"
                    @change="$emit('update:modelValue', opt.value)"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span class="text-sm text-gray-600">{{ opt.label }}</span>
            </label>
        </div>
    </div>
</template>
<style>
.radio-card {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-weight: 600;
}

.radio-card input {
    display: none;
}

.radio-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    position: relative;
}

.radio-dot::after {
    content: "";
    width: 10px;
    height: 10px;
    border-radius: 50%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: transparent;
}

/* COLORES */
.radio-baja input:checked + .radio-dot {
    border-color: #22c55e;
}
.radio-baja input:checked + .radio-dot::after {
    background: #22c55e;
}

.radio-media input:checked + .radio-dot {
    border-color: #facc15;
}
.radio-media input:checked + .radio-dot::after {
    background: #facc15;
}

.radio-alta input:checked + .radio-dot {
    border-color: #ef4444;
}
.radio-alta input:checked + .radio-dot::after {
    background: #ef4444;
}
</style>
