<script setup>
import { ref, reactive, computed, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Props pasados desde el backend
const props = defineProps({
    email: { type: String, required: true }
});

// Configuración básica
const DIGITS = 6;
const otp = ref('');
const otpBoxes = reactive(Array(DIGITS).fill(''));
const focused = ref(false);
const status = ref('idle'); // idle, success, error
const errorMessage = ref('');
const showResend = ref(false);
const resendTimeout = ref(30); // segundos de espera para reenvío

const form = useForm({
    email: props.email,
    otp_code: ''
});

const inputRef = ref(null);

// Reparte el OTP en las cajas
const getBoxes = computed(() => {
    const arr = Array(DIGITS).fill('');
    [...otp.value].forEach((d, i) => arr[i] = d || '');
    return arr;
});

// Foco automático invisible al inicio
nextTick(() => {
    inputRef.value?.focus();
});

// Cuando cambia el OTP
const onInput = (event) => {
    let value = event.target.value.replace(/\D/g, '');
    if (value.length > DIGITS) value = value.slice(0, DIGITS);
    otp.value = value;

    if (value.length === DIGITS) validateOtp();
};

// Validar OTP contra backend
const validateOtp = () => {
    form.otp_code = otp.value;
    form.post(route('admin.otp.verify'), {
        onSuccess: () => {
            status.value = 'success';
            errorMessage.value = '';
        },
        onError: () => {
            status.value = 'error';
            errorMessage.value = form.errors.otp_code || 'Código incorrecto o expirado.';
            // Limpia y vuelve a foco
            otp.value = '';
            nextTick(() => inputRef.value?.focus());
        }
    });
};

// Temporizador para reenviar
const startResendTimer = () => {
    showResend.value = false;
    resendTimeout.value = 30;
    const interval = setInterval(() => {
        resendTimeout.value--;
        if (resendTimeout.value <= 0) {
            showResend.value = true;
            clearInterval(interval);
        }
    }, 1000);
};

// Al mostrar vista, inicia timer
startResendTimer();

// Reenviar OTP al backend
const resendOtp = () => {
    form.post(route('admin.otp.resend'), {
        preserveScroll: true,
        onSuccess: () => {
            errorMessage.value = '';
            status.value = 'idle';
            otp.value = '';
            startResendTimer();
            nextTick(() => inputRef.value?.focus());
        }
    });
};
</script>

<template>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-50">
        <h2 class="mb-3 text-xl font-semibold text-gray-800">Verifica tu identidad</h2>
        <p class="mb-6 text-gray-500">Ingresa el código de 6 dígitos que te enviamos por email.</p>

        <!-- Input invisible -->
        <input
            ref="inputRef"
            type="text"
            inputmode="numeric"
            maxlength="6"
            autocomplete="one-time-code"
            class="absolute opacity-0 h-0 w-0"
            v-model="otp"
            @input="onInput"
        />

        <!-- Cajas OTP animadas -->
        <div class="flex gap-2 mb-5">
            <template v-for="(box, i) in getBoxes" :key="i">
                <div
                    :class="[
                        'w-12 h-14 flex items-center justify-center rounded-lg border-2 text-xl font-mono transition-all',
                        status === 'error' ? 'border-red-500 text-red-500 animate-shake' :
                        status === 'success' ? 'border-green-500 text-green-600 animate-success' :
                        otp.length === i ? 'border-blue-500' : 'border-gray-300'
                    ]"
                    @click="inputRef?.focus()"
                >
                    {{ box }}
                </div>
            </template>
        </div>

        <!-- Mensaje de error/success -->
        <div v-if="errorMessage" class="mb-3 text-red-600 font-semibold">{{ errorMessage }}</div>
        <div v-if="status === 'success'" class="mb-3 text-green-600 font-semibold">¡Verificado correctamente!</div>

        <!-- Botón reenviar OTP -->
        <button
            @click="resendOtp"
            class="mb-6 px-6 py-2 rounded bg-gray-200 text-gray-700 font-medium disabled:opacity-50 transition-all"
            :disabled="!showResend"
        >
            <span v-if="!showResend">Reenviar en {{ resendTimeout }}s</span>
            <span v-else>Reenviar código</span>
        </button>
    </div>
</template>

<style scoped>
@keyframes shake {
    0%   { transform: translateX(0); }
    20%  { transform: translateX(-8px); }
    40%  { transform: translateX(8px); }
    60%  { transform: translateX(-8px); }
    80%  { transform: translateX(8px); }
    100% { transform: translateX(0); }
}
.animate-shake { animation: shake 0.5s; }

@keyframes success {
    from { background-color: #e3ffe0; }
    to   { background-color: #fff; }
}
.animate-success { animation: success 1.7s; }
</style>
