<script>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default {
    components: {
        Link
    },
    setup() {
        const user = usePage().props.auth.user;

        const profilePictureUrl = computed(() => {
            return user.profile_picture ? `/storage/${user.profile_picture}` : 'images/UI/default-profile-pic.png';
        });
        return {
            user,
            profilePictureUrl
        };
    },
    methods: {
        test() {
            return this.user;
        },
        calculateExperienceForNextLevel(currentLevel, currentExperience) {
            // Bereken de totale ervaring nodig voor het volgende level
            const nextLevelExperience = Math.pow(currentLevel + 1, 3) * 1000;

            // Bereken het verschil tussen de huidige ervaring en de ervaring nodig voor het volgende level
            return Math.max(0, nextLevelExperience - currentExperience);
        }
    }
};
</script>

<template>
    <article class="player-info">
        <figure class="profile-pic">
            <Link :href="route('profile.edit')">
            <img :src="profilePictureUrl" alt="It's you!">
            </Link>
        </figure>
        <section class="player-info-section">
            <p class="real-name"> {{ $page.props.auth.user.name }}</p>
            <h2 class="username"> {{ $page.props.auth.user.username }}</h2>
            <p class="e-mail"> {{ $page.props.auth.user.email }}</p>
        </section>
        <section class="level-section">
            <p>level</p>
            <h2 class="level"> {{ $page.props.auth.user.level }}</h2>
        </section>
        <section class="experience-section">
            <p> {{ $page.props.auth.user.experience }} / {{
                calculateExperienceForNextLevel($page.props.auth.user.level, $page.props.auth.user.experience) }}</p>
            <figure></figure>
            <p>exp until next level</p>
        </section>
    </article>
</template>

<style>
.player-info {
    border-radius: 1rem;
    border: 1px solid var(--background-lighter);
    margin: 4rem auto;
    max-width: 90%;
    background: var(--background-lighter);
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    grid-template-rows: repeat(8, 1fr);
}

.player-info p {
    color: var(--font-color-gray);
}

.profile-pic {
    padding: 1rem;
    display: flex;
    justify-content: center;
    width: 100%;
    height: 100%;
    grid-column: 1/3;
    grid-row: 1/6;
    border-top-left-radius: 1rem;
}

.profile-pic>img {
    height: 100%;
}

.player-info-section {
    padding: 1rem;
    background: var(--background-super-dark);
    border-top-right-radius: 1rem;
    grid-column: 3/9;
    grid-row: 1/6;
}

.username {
    font-size: 150%;
}

.experience-section {
    padding: 0 1rem;
    border-bottom-right-radius: 1rem;
    grid-column: 3/9;
    grid-row: 6/9;
}

.level-section {
    padding: 0 1rem;
    position: relative;
    border-bottom-left-radius: 1rem;
    grid-column: 1/3;
    grid-row: 6/9;
}

.level-section::after {
    content: "";
    position: absolute;
    top: 15%;
    bottom: 15%;
    right: 0;
    width: 0.2rem;
    background-color: var(--background-super-dark);
}

.level {
    background: linear-gradient(90deg, #FDA829, #FF5C00);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    -webkit-text-fill-color: transparent;
}
</style>