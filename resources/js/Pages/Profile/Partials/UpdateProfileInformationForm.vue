<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

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
});

const onSubmit = () => {
    const data = new FormData();
    data.append('name', form.name);
    data.append('email', form.email);
    data.append('profile_picture', form.profile_picture);

    // Log de inhoud van de FormData
    for (let [key, value] of data.entries()) {
        console.log(key, value);
    }


    form.patch(route('profile.update'), {
        onSuccess: () => {
            form.reset();
        },
        data,
    });
};


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

    const reader = new FileReader();
    reader.readAsDataURL(file);

    reader.onload = () => {
        form.profile_picture = reader.result;
    };
};
</script>

<template>
    <section>
        <header>
            <h2>Profile Information</h2>
            <p>Update your account's profile information and email address.</p>
        </header>

        <form @submit.prevent="onSubmit" enctype="multipart/form-data">
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
    </section>
</template>

<style scoped>
.input {
    color: black;
    font-size: 100%;
}
</style>