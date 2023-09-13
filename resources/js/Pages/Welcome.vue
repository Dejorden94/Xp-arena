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
        <div v-if="canLogin" class="sm:fixed sm:top-0 sm:right-0 text-right">
            <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
            Dashboard</Link>

            <template v-else>
                <img class="logo" src='images/Logo-Xp-Arena.png' alt="Xp arena logo">
                <Link :href="route('login')"
                    class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                Log in</Link>

                <Link v-if="canRegister" :href="route('register')"
                    class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">
                Register</Link>
            </template>

            <article class="info-container">
                <section class="info-section welcome-info">
                    <figure class="smartphone-laptop-info">
                        <img src="images/info-imgs/info-laptop-smartphone.png"
                            alt="Uitleg over Xp Arena getoond op een laptop en mobiele telefoon.">
                    </figure>
                    <div class="text-container">
                        <h2>Welcome to Xp arena</h2>
                        <p>A tool to gamify any process</p>
                    </div>
                </section>
                <section class="info-section">
                    <figure class="gamification">
                        <div class="badge-container">
                            <img class="badge-img" src="images/info-imgs/Gamification-points.png"
                                alt="Een coin met een waarde van 1.">
                            <figcaption>Points</figcaption>
                        </div>
                        <div class="badge-container">
                            <img class="badge-img" src="images/info-imgs/Gamification-badges.png" alt="Een rood lintje.">
                            <figcaption>Badges</figcaption>
                        </div>
                        <div class="badge-container">
                            <img class="badge-img" src="images/info-imgs/Gamification-leaderboard.png"
                                alt="Een afbeelding van een trofee">
                            <figcaption>Leaderboard</figcaption>
                        </div>
                        <div class="badge-container">
                            <img class="badge-img" src="images/info-imgs/Gamification-progress.png"
                                alt="Een progressie klokje.">
                            <figcaption>Progress</figcaption>
                        </div>
                        <div class="badge-container">
                            <img class="badge-img" src="images/info-imgs/Gamification-avatar.png"
                                alt="Een avatar afbeelding van Batman.">
                            <figcaption>Avatar</figcaption>
                        </div>
                    </figure>
                    <div class="text-container">
                        <h2>Gamification</h2>
                        <p>XP arena uses game mechanics like points, badges, leaderboard, progress overview and adjustable
                            avatar.</p>
                    </div>
                </section>
                <section class="info-section">
                    <figure class="easy-info">
                        <img src="images/info-imgs/info-phone-mockup-closeup.png"
                            alt="Gebruiker houdt een telefoon vast en heeft Xp arena open staan.">
                    </figure>
                    <div class="text-container">
                        <h2>Easy & free</h2>
                        <p>Getting started is easy and free. You can use your Google account, or signup with another e-mail
                            address.</p>
                    </div>
                </section>
                <section class="start-now">
                    <a :href="route('register')">START NOW</a>
                </section>
            </article>
        </div>
    </div>
</template>

<style>
.home-container {
    height: 100vh;
    background: var(--background-darker);
}

.logo {
    width: 10%;
}

.info-container {
    padding: 0 2.5rem;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 1rem;
    width: 100vw;
    height: 100vh;
}

.smartphone-laptop-info {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    background: var(--background-lighter);
    border-radius: 2rem 2rem 0 0;
    height: 100%;
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
    /*Voor testen*/
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
    height: 20vh;
    grid-column: 1/4;
    background-image: url("images/info-imgs/levelup-bg1.png");
    background-size: 30%;
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
    .info-container {
        display: flex;
        justify-content: center;
        flex-direction: column;
    }
}

@media (prefers-color-scheme: dark) {
    .dark\:bg-dots-lighter {
        background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
    }
}
</style>
