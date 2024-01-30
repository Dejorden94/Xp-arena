<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const form = useForm({
    name: user.name,
    email: user.email,
    profile_picture: null,
});

const onFileChange = (event) => {
    const file = event.target.files[0];
    if (!file) {
        return;
    }
    if (!['image/jpeg', 'image/png', 'image/gif'].includes(file.type)) {
        form.errors.profile_picture = 'Only JPEG, PNG, and GIF images are allowed.';
        return;
    }
    if (file.size > 1024 * 1024 * 2) {
        form.errors.profile_picture = 'The image must be less than 2MB.';
        return;
    }
    form.profile_picture = file;
};

const onSubmit = () => {
    const data = new FormData();
    data.append('name', form.name);
    data.append('email', form.email);
    if (form.profile_picture) {
        data.append('profile_picture', form.profile_picture);
    }

    form.post(route('profile.update'), {
        onSuccess: () => {
            form.reset('profile_picture');
        },
        onError: (errors) => console.log(errors),
        data,
    });
};
</script>

<template>
    <article class="player-edit-section">
        <header>
            <h2>Profile Information</h2>
            <p>Update your account's profile information and email address.</p>
        </header>

        <form @submit.prevent="onSubmit">
            <section>
                <InputLabel for="profile_picture" value="Profile Picture" />
                <input id="profile_picture" name="profile_picture" type="file" accept="image/*" @change="onFileChange" />
                <InputError :message="form.errors.profile_picture" />
            </section>

            <section>
                <InputLabel for="name" value="Name" />
                <TextInput class="input" id="name" type="text" v-model="form.name" required autofocus autocomplete="name" />
                <InputError :message="form.errors.name" />
            </section>

            <div>
                <InputLabel for="email" value="Email" />
                <TextInput class="input" id="email" type="email" v-model="form.email" required autocomplete="username" />
                <InputError :message="form.errors.email" />
            </div>

            <section v-if="mustVerifyEmail && user.email_verified_at === null">
                <p>Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button">
                    Click here to re-send the verification email.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'">
                    A new verification link has been sent to your email address.
                </div>
            </section>

            <section>
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>
                <Transition>
                    <p v-if="form.recentlySuccessful">Saved.</p>
                </Transition>
            </section>
        </form>
    </article>
</template>

<style scoped>
.input {
    color: black;
    font-size: 100%;
}

input[type=file] {
    width: 100%;
}
</style>