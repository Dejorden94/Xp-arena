<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    username: ''
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>

        <Head title="Register" />

        <form class="register-form" @submit.prevent="submit">
            <h2>Create account</h2>
            <p>Cool! First we need some information for your account.</p>
            <div>
                <div class="input-field">
                    <InputLabel for="name" value="Name:" />

                    <TextInput id="name" type="text" class="input" v-model="form.name" required autofocus
                        autocomplete="name" />
                </div>
                <div class="input-field">
                    <InputLabel for="username" value="Username:" />

                    <TextInput id="username" type="text" class="input" v-model="form.username" required
                        autocomplete="username" />

                    <InputError class="mt-2" :message="form.errors.username" />
                </div>


                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="input-field">
                <InputLabel for="email" value="Email:" />

                <TextInput id="email" type="email" class="input" v-model="form.email" required autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="input-field">
                <InputLabel for="password" value="Password:" />

                <TextInput id="password" type="password" class="input" v-model="form.password" required
                    autocomplete="new-password" />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="input-field">
                <InputLabel for="password_confirmation" value="Repeat:" />

                <TextInput id="password_confirmation" type="password" class="input" v-model="form.password_confirmation"
                    required autocomplete="new-password" />

                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <div class="send-section">
                <Link :href="route('login')" class="have-account">
                Already registered?
                </Link>

                <PrimaryButton class="send-button" :disabled="form.processing">
                    Send
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
p {
    text-align: center;
}

h2 {
    text-align: center;
}

.register-form {
    display: flex;
    flex-direction: column;
    justify-content: space-evenly;
}

.send-button {
    text-transform: uppercase;
    font-size: 100%;
    padding: 0.5rem 1.5rem;
    margin-top: 1rem;
    border: 0.2rem solid var(--color-orange);
    border-radius: 1.5rem;
}

.input-field {
    display: flex;
    margin-bottom: 1rem;
}

.input-field:first-of-type {
    margin-top: 2rem;
}

.input-field:nth-of-type(2) {
    margin-bottom: 2rem;
}

.input {
    background: var(--background-lighter);
    border: none;
    width: 60%;
    height: 1.8rem;
    margin-left: auto;
}

.send-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.have-account {
    font-size: 80%;
    text-decoration: underline;
}
</style>