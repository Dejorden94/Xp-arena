<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Home" />

    <div class="home-container">
        <img class="logo logo-mobile" src='images/Logo-Xp-Arena.png' alt="Xp arena logo">
        <div v-if="canLogin" class="menu">
            <article class="menu-desktop">
                <img class="logo" src='images/Logo-Xp-Arena.png' alt="Xp arena logo">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')">
                Dashboard</Link>

                <template v-else>
                    <Link :href="route('login')">
                    Log in</Link>
                    <Link v-if="canRegister" :href="route('register')">
                    Register</Link>
                    <Link href="/">Home</Link>
                </template>
            </article>

            <article class="menu-mobile">
                <Link v-if="$page.props.auth.user" :href="route('dashboard')">
                Dashboard</Link>

                <template v-else>
                    <Link :href="route('login')">
                    Log in</Link>

                    <Link v-if="canRegister" :href="route('register')">
                    Register</Link>
                    <Link :href="route('about')">About</Link>
                </template>
            </article>

            <main class="about-container">
                <article class="about-us">
                    <figure>
                        <img src="images/info-imgs/info-about-graphic.png" alt="">
                    </figure>
                    <section class="text-section">
                        <h2>About us</h2>
                        <p>XP arena is heavily inspired by the book Reality is Broken by Jane McGonigal.
                            <br>
                            <br>
                            Games are very good at motivating people to do seamingly meaningless work. Our mission is to
                            provice
                            a platform that allows the user to add game mechanics to any prosess to make it more inviting,
                            challenging and fun!
                        </p>
                    </section>
                </article>

                <article>

                </article>

                <form class="contact-form" action="">
                    <h3>Contact</h3>
                    <p>Don't hesitate to contact us</p>
                    <label for="name">Name:</label>
                    <input id="name" type="text">
                    <label for="name">E-mail:</label>
                    <input id="name" type="email">
                    <label for="message">Name:</label>
                    <textarea rows="4" cols="20" name="message" form="usrform">
Enter text here...</textarea>
                </form>

            </main>
        </div>
    </div>
</template>

<style>
body {
    background: var(--background-darker);
}

.logo-mobile {
    display: none;
}

.menu-mobile {
    display: none;
}

.menu-desktop {
    padding: 0 2rem;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 2rem;
    height: 20vh;
}

.menu-desktop>img {
    position: absolute;
    left: 0;
    margin-left: 2rem;
}

.about-container {
    padding: 0 2.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    gap: 1rem;
    height: 80vh;
}

.about-us {
    max-width: 60%;
    min-width: 30%;
    border: 2px solid var(--background-lighter);
    text-align: center;
    border-radius: 3rem;
    background: var(--background-lighter);
}

.text-section {
    background: var(--background-super-dark);
    padding: 2rem;
    border-radius: 0 0 3rem 3rem;
}

.contact-form {
    display: flex;
    flex-direction: column;
}

.bg-dots-darker {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media screen and (max-width: 1280px) {
    .logo-mobile {
        display: block;
    }

    .info-container {
        display: flex;
        flex-direction: column;
        margin-top: 4rem;
        gap: 2.4rem;
        height: 140vh;
    }

    .logo {
        width: 40%;
        margin: 0 auto;
    }

    .menu-mobile {
        display: block;
        position: fixed;
        bottom: 0;
        background-color: var(--background-lighter);
        width: 100vw;
        height: 10vh;
    }

    .menu-desktop {
        display: none;
    }
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
