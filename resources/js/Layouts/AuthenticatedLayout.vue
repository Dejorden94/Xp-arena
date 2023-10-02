<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div>
            <nav>
                <!-- Primary Navigation Menu -->
                <div>
                    <div class="dashboard-heading">
                        <!-- Logo -->
                        <ApplicationLogo class="dashboard-logo" />
                        <Link :href="route('dashboard')">
                        </Link>


                        <!-- Navigation Links -->

                        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </NavLink>
                        <NavLink :href="route('about')">
                            Dashboard
                        </NavLink>
                        <NavLink href="/">
                            Home
                        </NavLink>
                        <!-- Settings Dropdown -->
                        <Dropdown>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>



                        <!-- Hamburger -->
                        <div>
                            <button class="dashboard-hamburger"
                                @click="showingNavigationDropdown = !showingNavigationDropdown">
                                <span class="hamburger-stroke"></span>
                                <span class="hamburger-stroke"></span>
                                <span class="hamburger-stroke"></span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div v-if="showingNavigationDropdown">
                    <div>
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div>
                        <div>
                            <div>
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div>{{ $page.props.auth.user.email }}</div>
                        </div>

                        <div>
                            <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header">
                <div>
                    <slot name="header" />
                </div>
            </header>
            <article class="player-info">
                <figure class="profile-pic">
                    <img src="images/UI/default-profile-pic.png" alt="It's you!">
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
                    <p> {{ $page.props.auth.user.experience }} / 1000</p>
                    <figure></figure>
                    <p>exp until next level</p>
                </section>
            </article>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style>
.dashboard-hamburger {
    padding: 1rem;
    width: 6rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.player-info {
    border-radius: 1rem;
    border: 1px solid var(--background-lighter);
    margin: 4rem auto;
    width: 80vw;
    height: 40vh;
    background: var(--background-lighter);
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    grid-template-rows: repeat(8, 1fr);
}

.player-info p {
    color: var(--font-color-gray);
}

.player-info>* {
    padding: 1rem;
}

.profile-pic {
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
    background: var(--background-super-dark);
    border-top-right-radius: 1rem;
    grid-column: 3/9;
    grid-row: 1/6;
}

.username {
    font-size: 150%;
}

.experience-section {
    border-bottom-right-radius: 1rem;
    grid-column: 3/9;
    grid-row: 6/9;
}

.level-section {
    position: relative;
    border-bottom-left-radius: 1rem;
    grid-column: 1/3;
    grid-row: 6/9;
}

.level-section::after {
    content: "";
    position: absolute;
    top: 15%;
    /* Beginpunt van de border */
    bottom: 15%;
    /* Eindpunt van de border */
    right: 0;
    width: 0.2rem;
    /* Breedte van de border */
    background-color: var(--background-super-dark);
    /* Kleur van de border */
}

.level {
    background: linear-gradient(90deg, #FDA829, #FF5C00);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    -webkit-text-fill-color: transparent;
}

.dashboard-heading {
    display: flex;
}

.dashboard-logo {
    width: 10%;
}

@media screen and (max-width: 1280px) {
    .player-info {
        height: 20vh;
    }
}
</style>