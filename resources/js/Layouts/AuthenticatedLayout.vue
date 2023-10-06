<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import PlayerInformation from '@/Components/PlayerInformation.vue';
import MobileMenu from '@/Components/MobileMenu.vue';
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

                        <!-- Navigation Links -->
                        <NavLink href="/">
                            Home
                        </NavLink>
                        <NavLink :href="route('dashboard')">
                            Dashboard
                        </NavLink>
                        <NavLink :href="route('profile.edit')">
                            Profile
                        </NavLink>
                        <NavLink :href="route('logout')" method="post" as="button">Log out</NavLink>
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
                        <div class="dropdown-menu">
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

            <MobileMenu @showJoin="$emit('showJoin')" />

            <PlayerInformation />

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style>
.dashboard-hamburger {
    /* padding: 1rem; */
    display: none;
    width: 6rem;
    height: 6rem;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.dropdown-menu {
    display: flex;
    justify-content: flex-end;
    flex-direction: column;
}

.dropdown-menu>button {
    width: 10rem;
}

.dropdown-link {
    background: none;
    color: #eee;
}

.dashboard-heading {
    display: flex;
    justify-content: space-around;
    align-items: center;
}

.dashboard-logo {
    width: 10%;
}

.level {
    background: linear-gradient(90deg, #FDA829, #FF5C00);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    -webkit-text-fill-color: transparent;
}

@media screen and (max-width: 1280px) {
    .dashboard-hamburger {
        display: flex;
    }

    .player-info {
        height: 20vh;
    }
}
</style>