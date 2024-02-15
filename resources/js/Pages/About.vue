<script setup>
import { Head, Link } from '@inertiajs/vue3';

import MobileMenu from '/resources/js/Components/MobileMenu.vue';

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
        <img class="logo-mobile" src='images/Logo-Xp-Arena.png' alt="Xp arena logo">
        <img class="logo" src='images/Logo-Xp-Arena.png' alt="Xp arena logo">
        <div v-if="canLogin" class="menu">
            <article class="menu-desktop">
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

        </div>
        <MobileMenu :canRegister="canRegister" :currentRoute="$page.url" />
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

            <article class="video">
                <iframe src="https://www.youtube.com/embed/MuDLw1zIc94?si=a6pd_xxAzsmJg8lU" title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen></iframe>
            </article>

            <form class="contact-form" action="">
                <h2>Contact</h2>
                <p>Don't hesitate to contact us</p>
                <section>
                    <div class="input-container">
                        <label for="name">Name:</label>
                        <input id="name" type="text">
                    </div>
                    <div class="input-container">
                        <label for="name">E-mail:</label>
                        <input id="name" type="email">
                    </div>
                    <div class="input-container">
                        <label for="message">Message:</label>
                        <textarea rows="4" cols="40" name="message" placeholder="Type your message..."></textarea>
                    </div>
                    <input class="send-button contact" type="submit" value="Send">
                </section>
            </form>

        </main>

    </div>
</template>

<style>
body {
    background: var(--background-darker);
    position: relative;
}

.home-container {
    display: flex;
    justify-content: space-between;
    gap: 5rem;
}

.logo {
    width: 20rem;
    position: absolute;
    left: 50%;
    right: 50%;
}

.input-container {
    text-align: center;
    width: 80%;
    display: flex;
    margin-top: 1rem;
    gap: 1rem;
}

.input-container>input {
    width: 100;
}

label {
    width: 10rem;
}

.menu-desktop {
    width: 20vw;
    margin-top: 10rem;
    padding: 0 2rem;
    flex-direction: column;
    display: flex;
    justify-content: flex-start;
    gap: 2rem;
    position: fixed;
    background-color: var(--background-darker);

    * {
        font-size: 130%;
        font-weight: bolder;
        text-transform: uppercase;
    }
}

.logo-mobile {
    display: none;
}

.about-container {
    margin-top: 10rem;
    padding: 0 5rem;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 80vw;
}

.about-us {
    width: 100%;
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

.video {
    margin: 2rem 0;
    width: 100%;
    height: 40rem;
}

.video>iframe {
    border-radius: 3rem;
    width: 100%;
    height: 100%;
}

.contact-form {
    padding: 2.5rem;
    margin-bottom: 4rem;
    background: var(--background-super-dark);
    width: 100%;
    display: flex;
    gap: 1rem;
    flex-direction: column;
    border: 2px solid var(--background-lighter);
    border-radius: 3rem;
}

.contact-form>section {
    width: 100%;
    display: flex;
    align-items: flex-end;
    flex-direction: column;
}

.contact {
    width: 10rem;
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
    }

    /* .logo {
        width: 40%;
        margin: 0 auto;
    } */

    .menu-desktop {
        display: none;
    }

    .about-us {
        width: 100%;
    }

    .video {
        width: 100%;
    }

    .contact-form {
        width: 100%;
        margin-bottom: 8rem;
    }

    .contact-form>section {
        align-items: flex-start;
    }

    label {
        width: 5remrem;
    }

    .contact {
        margin-left: auto;
    }
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
