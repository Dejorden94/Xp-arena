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
                        <Link :href="route('dashboard')">
                        </Link>


                        <!-- Navigation Links -->

                        <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                            Dashboard
                        </NavLink>
                        <NavLink :href="route('about')">
                            About
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
                        <button class="dashboard-hamburger" @click="showingNavigationDropdown = !showingNavigationDropdown">
                            <span class="hamburger-stroke"></span>
                            <span class="hamburger-stroke"></span>
                            <span class="hamburger-stroke"></span>
                        </button>
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

            <MobileMenu />

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
    width: 6rem;
    height: 6rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.dashboard-heading {
    display: flex;
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
    .player-info {
        height: 20vh;
    }
}
</style>