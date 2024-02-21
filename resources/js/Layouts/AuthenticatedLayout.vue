<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import PlayerInformation from '@/Components/PlayerInformation.vue';
import MobileMenu from '@/Components/MobileMenu.vue';
import { Link } from '@inertiajs/vue3';

import { ref } from 'vue';

const props = defineProps(['showTaskCheck', 'showAddJoin', 'showPlayerInfo', 'isGameQuestDetailsShown', 'showCheckpoint', 'gameData', 'isButtonClicked', 'isQuestButtonClicked']);


const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="dashboard-container">
            <nav>
                <!-- Primary Navigation Menu -->
                <div class="dashboard-heading">
                    <PlayerInformation class="player-desktop" v-show="showPlayerInfo" />
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

            <MobileMenu @showJoin="$emit('showJoin')" @showQuestCheck="$emit('showQuestCheck')"
                @showTaskCheck="$emit('showTaskCheck')" @isEditing="$emit('isEditing')" @toggleAddButton="toggleAddButton()"
                @toggleQuestAddButton="toggleQuestAddButton()" :showTaskCheck="showTaskCheck" :showAddJoin="showAddJoin"
                :isGameQuestDetailsShown="isGameQuestDetailsShown" :isButtonClicked="isButtonClicked"
                :isQuestButtonClicked="isQuestButtonClicked" />


            <PlayerInformation class="player-mobile" v-show="showPlayerInfo" />

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        toggleAddButton() {
            this.$emit('toggleAddButton');
        },
        toggleQuestAddButton() {
            this.$emit('toggleQuestAddButton');
        }
    }
}

</script>

<style>
main {
    width: 80vw;
}

nav {
    width: 20vw;
}

.dashboard-container {
    display: flex;
}

.dashboard-hamburger {
    padding: 1rem;
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

.player-mobile {
    display: none;
}

.player-desktop {
    display: grid;
    height: 25rem;
    width: 100%;

}

.dashboard-heading {
    width: 25%;
    height: 100vh;
    position: fixed;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    gap: 2rem;
    background-color: var(--background-super-dark);
}

.dashboard-heading>a {
    text-transform: uppercase;
    font-weight: 600;
    font-size: 130%;
}

.dashboard-heading>a,
.dashboard-heading>button {
    margin-left: 4rem;
}

.dashboard-heading>a:first-of-type {
    margin-top: 10rem;
}

.level {
    background: linear-gradient(90deg, #FDA829, #FF5C00);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    -webkit-text-fill-color: transparent;
}

@media screen and (max-width: 1280px) {
    main {
        width: auto;
    }

    .dashboard-container {
        display: block;
    }

    .dashboard-heading {
        display: none;
    }

    .dashboard-hamburger {
        display: flex;
    }

    .player-mobile {
        display: grid;
    }

    .player-desktop {
        display: none;
    }
}
</style>