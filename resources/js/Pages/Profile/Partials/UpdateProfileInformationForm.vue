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
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>

            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>


        <form @submit.prevent="onSubmit" class="mt-6 space-y-6">
            <div>
                <InputLabel for="profile_picture" value="Profile Picture" />

                <input id="profile_picture" type="file" class="mt-1 block w-full" accept="image/*" @change="onFileChange" />

                <InputError class="mt-2" :message="form.errors.profile_picture" />
            </div>
        </form>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div>

                <InputLabel for="name" value="Name" />

                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                    autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                    autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800">
                    Your email address is unverified.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Click here to re-send the verification email.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'" class="mt-2 font-medium text-sm text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition enter-from-class="opacity-0" leave-to-class="opacity-0" class="transition ease-in-out">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>
