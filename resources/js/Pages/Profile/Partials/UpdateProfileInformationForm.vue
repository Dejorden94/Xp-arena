<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;
const profilePictureUrl = computed(() => {
    return user.profile_picture ? `/storage/${user.profile_picture}` : 'images/UI/default-profile-pic.png';
});
const form = useForm({
    name: user.name,
    email: user.email,
    username: user.username,
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
    data.append('username', form.username)
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
            <h2>Edit player</h2>
        </header>

        <form @submit.prevent="onSubmit">

            <section class="picture">
                <img :src="profilePictureUrl" alt="It's you!">
                <InputLabel for="profile_picture" value="Current Profile Picture" />
                <input id="profile_picture" name="profile_picture" type="file" accept="image/*" @change="onFileChange" />
                <InputError :message="form.errors.profile_picture" />
            </section>

            <section>
                <InputLabel for="username" value="Nickname" />
                <TextInput class="input" id="username" type="text" v-model="form.username" required autofocus
                    autocomplete="username" />
                <InputError :message="form.errors.username" />
            </section>

            <section>
                <InputLabel for="name" value="Name" />
                <TextInput class="input" id="name" type="text" v-model="form.name" required autofocus autocomplete="name" />
                <InputError :message="form.errors.name" />
            </section>

            <section>
                <InputLabel for="email" value="Email" />
                <TextInput class="input" id="email" type="email" v-model="form.email" required autocomplete="email" />
                <InputError :message="form.errors.email" />
            </section>

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
                <PrimaryButton class="save-button" :disabled="form.processing">Save</PrimaryButton>
                <Transition>
                    <p v-if="form.recentlySuccessful">Aanpassingen opgeslagen</p>
                </Transition>
            </section>
        </form>
    </article>
</template>

<style scoped>
section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}


input[type=file] {
    width: 80%;
    text-align: center;
}

img {
    object-fit: cover;
    width: 50%;
    aspect-ratio: 1/1;
    border-radius: 50%;
}

h2 {
    text-align: center;
}

.input {
    width: 60%;
    margin-bottom: 1rem;
    background: var(--background-lighter);
    border: none;
    padding: 0.5rem;
    border-radius: 5px;
}

.save-button {
    margin-left: auto;
    width: 10rem;
}

.picture {
    display: flex;
    flex-direction: column;
}
</style>