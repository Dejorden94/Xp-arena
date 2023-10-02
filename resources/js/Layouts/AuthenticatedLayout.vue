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
                        <!-- Settings Dropdown -->
                        <Dropdown>
                            <template #trigger>
                                <span>
                                    <button>
                                        {{ $page.props.auth.user.name }}
                                        <!-- SVG for dropdown arrow -->
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </DropdownLink>
                            </template>
                        </Dropdown>



                        <!-- Hamburger -->
                        <div>
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown">
                                <!-- SVG for hamburger icon -->
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
                <img class="profile-pic" src="public/images/UI/default-profile-pic.png" alt="It's you!">
                <p class="real-name">Real name</p>
                <h2 class="username">Username</h2>
                <p class="e-mail">e-mail</p>
                <section class="level-section">
                    <p>level</p>
                    <h2>0</h2>
                </section>
                <section class="experience-section">
                    <p>experience points</p>
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
.player-info {
    width: 100vw;
    height: 40vh;
    background: red;
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    grid-template-rows: repeat(8, 1fr)
}

.profile-pic {
    background: blue;
    grid-column: 1/2;
    grid-row: 1/6;
}

.level-section {
    background: yellow;
    grid-column: 1/2;
    grid-row: 6/9;
}

.dashboard-heading {
    display: flex;
}

.dashboard-logo {
    width: 10%;
}
</style>