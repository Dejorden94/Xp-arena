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
        <div>
            <nav>
                <!-- Primary Navigation Menu -->
                <div>
                    <div class="dashboard-heading">
                        <PlayerInformation class="player-desktop" v-show="showPlayerInfo" />
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

.player-mobile {
    display: none;
}

.player-desktop {
    display: block;
    width: 10rem;
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