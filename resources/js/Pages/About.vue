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

            <article class="info-container">
                <h1>Hallo?</h1>
            </article>
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

.info-container {
    padding: 0 2.5rem;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 1rem;
    width: 100vw;
    height: 80vh;
}

figcaption {
    font-weight: bold;
    font-size: 85%;
}

.smartphone-laptop-info {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    background: var(--background-lighter);
    border-radius: 2rem 2rem 0 0;
    height: 100%;
    width: 100%;
}

.smartphone-laptop-info>img {
    height: 90%;
}

.info-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--background-super-dark);
    border: .2rem solid var(--background-lighter);
    border-radius: 3rem;
}

.gamification {
    display: flex;
    flex-direction: row;

}

.badge-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.badge-img {
    margin: -1rem;
    width: 100%;
}

.easy-info {
    height: 100%;
}

.easy-info>img {
    border-radius: 3rem 3rem 0 0;
}

.text-container {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    flex-direction: column;
    height: 40%;
}

.start-now {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    grid-column: 1/4;
    grid-row: 2/4;
    border-radius: 3rem;
    background-image: url("/images/info-imgs/levelup-bg1.png");
    background-size: 100%;
}

.start-now>a {
    display: block;
    font-size: 2rem;
    background: var(--background-lighter);
    font-weight: 700;
    text-align: center;
    width: 20rem;
    height: 4rem;
    line-height: 4rem;
    border-radius: 3rem;
    border: 2px solid var(--color-orange);
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

    .badge-container {
        margin-bottom: 2rem;
    }

    figcaption {
        font-size: 60%;
    }

    .text-container {
        padding: 2rem;
    }

    .start-now {
        background-size: cover;
        padding: 4rem;
        border-radius: 2rem;
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

    .text-right {
        margin-bottom: 30rem;
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
