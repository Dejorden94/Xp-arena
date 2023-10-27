<template>
    <article class="menu-mobile">
        <button @click="toggleMenu" class="hamburger">
            <span class="hamburger-stroke"></span>
            <span class="hamburger-stroke"></span>
            <span class="hamburger-stroke"></span>
        </button>

        <template v-if="$page.props.auth.user">
            <div v-if="showMenu" class="mobile-menu">
                <Link v-show="$page.component !== 'Dashboard'" class="mobile-link" :href="route('dashboard')">
                Dashboard</Link>
                <Link v-show="$page.component !== 'Profile/Edit'" class="mobile-link" :href="route('profile.edit')">
                Profile</Link>
                <template v-if="currentRoute !== '/about'">
                    <Link class="mobile-link" :href="route('about')">About</Link>
                </template>
                <template v-if="currentRoute !== '/'">
                    <Link class="mobile-link" href="/">Home</Link>
                </template>
            </div>

            <button v-show="!isGameQuestDetailsShown && showAddJoin && $page.component === 'Dashboard'"
                :class="{ 'join-add-button': true, 'clicked': isButtonClicked }"
                @click="$emit('showJoin'); toggleAddMenu()">
                {{ isButtonClicked ? 'x' : '+' }}
            </button>

            <button v-show="!isGameQuestDetailsShown && showTaskCheck"
                :class="{ 'join-add-button': true, 'clicked': isQuestButtonClicked }"
                @click="$emit('showQuestCheck'); toggleQuestMenu()">
                {{ isQuestButtonClicked ? 'x' : '+' }}
            </button>

            <button v-show="isGameQuestDetailsShown" :class="{ 'criterion-edit join-add-button': true, 'clicked': isSave }"
                @click="toggleCriterionButton()">
                <span v-show="!isSave"><i class="fa-solid fa-pen"></i></span>
                <spa v-show="isSave"><i class="fa-regular fa-floppy-disk"></i></spa>
            </button>


        </template>

        <template v-else>
            <div v-if="showMenu" class="mobile-menu">
                <Link class="mobile-link" :href="route('login')">
                Log in</Link>
                <Link class="mobile-link" v-if="canRegister" :href="route('register')">
                Register</Link>
                <template v-if="currentRoute !== '/about'">
                    <Link class="mobile-link" :href="route('about')">About</Link>
                </template>
                <template v-else>
                    <Link class="mobile-link" href="/">Home</Link>
                </template>
            </div>
        </template>
    </article>
</template>

<script>
import { Link } from '@inertiajs/vue3';

import { ref } from 'vue';

import { defineProps } from 'vue';

const props = defineProps(['showTaskCheck', 'showAddJoin', 'isGameQuestDetailsShown']);

export default {
    data() {
        return {
            isButtonClicked: false,
            isQuestButtonClicked: false,
            isSave: false
        }
    },
    components: {
        Link
    },
    props: {
        canRegister: Boolean,
        currentRoute: String,
        showTaskCheck: Boolean,
        showAddJoin: Boolean,
        isGameQuestDetailsShown: Boolean,
    },
    setup() {
        const showMenu = ref(false);

        const toggleMenu = () => {
            showMenu.value = !showMenu.value;
        };

        return {
            showMenu,
            toggleMenu
        };
    },
    methods: {
        toggleAddMenu() {
            this.isButtonClicked = !this.isButtonClicked;
        },
        toggleQuestMenu() {
            this.isQuestButtonClicked = !this.isQuestButtonClicked;
        },
        toggleCriterionButton() {
            this.isSave = !this.isSave;
        }

    }
}
</script>

<style>
.menu-mobile {
    display: none;
}

.join-add-button {
    display: none;
}

@media screen and (max-width: 1280px) {
    .join-add-button {
        border: none;
        background: linear-gradient(90deg, #FDA829, #FF5C00);
        display: block;
        font-weight: 100;
        font-size: 500%;
        width: 10rem;
        height: 10rem;
        border-radius: 50%;
        position: absolute;
        bottom: 2rem;
        left: 50%;
        margin-left: -5rem;
    }

    .join-add-button.clicked {
        background: linear-gradient(90deg, #4DA9FF, #3770A6);

    }

    .criterion-edit {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .fa-pen,
    .fa-floppy-disk {
        font-size: 5rem;
    }

    .quest-check-button {
        border: none;
        background: linear-gradient(90deg, #FDA829, #FF5C00);
        display: block;
        font-weight: 100;
        font-size: 500%;
        width: 10rem;
        height: 10rem;
        border-radius: 50%;
        position: absolute;
        bottom: 2rem;
        left: 50%;
        margin-left: -5rem;
    }

    .quest-check-button.clicked {
        background: linear-gradient(90deg, #4DA9FF, #3770A6);

    }

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

    .menu-mobile {
        z-index: 100;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        position: fixed;
        bottom: 0;
        background-color: var(--background-lighter);
        width: 100vw;
        height: 10vh;
    }

    .hamburger {
        width: 7.5rem;
        height: 100%;
        border: none;
        background: none;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .mobile-menu {
        bottom: 0;
        position: fixed;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        flex-direction: column;
        width: 100vw;
        height: 100vh;
        background: rgba(27, 23, 32, 0.7);
        z-index: -10;
        /* Behouden op 10 */
    }

    .mobile-link {
        opacity: 1;
        margin-bottom: 4rem;
        font-size: 2rem;
        font-weight: bolder;
        text-transform: uppercase;
    }

    .mobile-link:last-child {
        margin-bottom: 20rem;
    }

    .menu-desktop {
        display: none;
    }
}
</style>