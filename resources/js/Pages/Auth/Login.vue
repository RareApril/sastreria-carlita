<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="flex flex-col gap-2 mb-6">
            <!-- BOTÓN GOOGLE -->
            <a
                :href="route('google.redirect')"
                class="w-full bg-white border text-gray-700 py-2 rounded-lg flex items-center justify-center font-semibold shadow-sm hover:bg-gray-50 transition-all"
                style="border-color: #e5e7eb;"
            >
                <svg class="w-5 h-5 mr-2" viewBox="0 0 48 48">
                    <g>
                        <path d="M44.5 20H24v8.5h11.7c-1.4 4-5.2 6.5-9.7 6.5A10.5 10.5 0 1 1 24 13c2.1 0 4 .6 5.6 1.8l6.3-6.3C32.8 6.7 28.7 5 24 5A19 19 0 1 0 43 24c0-1.3-.1-2.3-.3-3.3z" fill="#FFC107"/>
                        <path d="M6.3 14.1l7 5.1A10.5 10.5 0 0 1 24 13a10.5 10.5 0 0 1 5.6 1.8l6.3-6.3C32.8 6.7 28.7 5 24 5c-7.4 0-13.7 4.1-17.7 10.1z" fill="#FF3D00"/>
                        <path d="M24 44c4.6 0 8.7-1.5 11.7-4.1l-5.4-4.5C28 36.6 26.1 37.1 24 37.1c-4.5 0-8.3-2.5-9.7-6.5l-7 5.1C10.3 40.1 16.6 44 24 44z" fill="#4CAF50"/>
                        <path d="M44.5 20H24v8.5h11.7c-.6 1.9-2.2 4.2-4.2 6l6.4 5c3.8-3.3 6-8.1 6-13.5 0-1-.1-2-.2-3z" fill="#1976D2"/>
                    </g>
                </svg>
                Continuar con Google
            </a>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
