<template>
    <article class="menu-mobile">
        <button @click="toggleMenu" :class="['hamburger', { 'active': hamburgerActive }]">
            <span class="hamburger-stroke"></span>
            <span class="hamburger-stroke"></span>
            <span class="hamburger-stroke"></span>

        </button>

        <template v-if="$page.props.auth.user">
            <div v-if="showMenu" class="mobile-menu" id="js__mobile_menu">
                <Link v-show="$page.component !== 'Dashboard'" class="mobile-link" :href="route('dashboard')">
                <font-awesome-icon :icon="['fas', 'gauge']" />
                Dashboard</Link>
                <Link v-show="$page.component !== 'Profile/Edit'" class="mobile-link" :href="route('profile.edit')">
                <font-awesome-icon :icon="['fas', 'user']" />
                Profile</Link>
                <template v-if="currentRoute !== '/about'">
                    <Link class="mobile-link" :href="route('about')"><font-awesome-icon :icon="['fas', 'heart']" />About
                    </Link>
                </template>
                <template v-if="currentRoute !== '/'">
                    <Link class="mobile-link" href="/"><font-awesome-icon :icon="['fas', 'house']" />Home</Link>
                </template>
                <Link class="mobile-link" :href="route('logout')" method="post">
                <font-awesome-icon :icon="['fas', 'right-from-bracket']" />
                Log Out
                </Link>
            </div>
            <button v-show="!isGameQuestDetailsShown && showAddJoin && $page.component === 'Dashboard'"
                :class="{ 'join-add-button': true, 'clicked': this.isButtonClicked }"
                @click="$emit('showJoin'); toggleAddMenu()">
                {{ this.isButtonClicked ? 'x' : '+' }}
            </button>

            <button v-show="!isGameQuestDetailsShown && showTaskCheck"
                :class="{ 'join-add-button': true, 'clicked': isQuestButtonClicked }"
                @click="$emit('showQuestCheck'); toggleQuestMenu()">
                {{ isQuestButtonClicked ? 'x' : '+' }}
            </button>

            <button v-show="isGameQuestDetailsShown"
                :class="{ 'criterion-edit join-add-button': true, 'clicked': isSave }"
                @click="toggleCriterionButton();">
                <span v-show="!isSave"><i class="fa-solid fa-pen"></i></span>
                <spa v-show="isSave"><i class="fa-regular fa-floppy-disk"></i></spa>
            </button>

        </template>

        <template v-else>
            <div v-if="showMenu" class="mobile-menu">
                <Link class="mobile-link" :href="route('login')"><font-awesome-icon
                    :icon="['fas', 'right-to-bracket']" />
                Log in</Link>
                <Link class="mobile-link" v-if="canRegister" :href="route('register')">
                Register</Link>
                <template v-if="currentRoute !== '/about'">
                    <Link class="mobile-link" :href="route('about')"><font-awesome-icon :icon="['fas', 'heart']" />About
                    </Link>
                </template>
                <template v-else>
                    <Link class="mobile-link" href="/"><font-awesome-icon :icon="['fas', 'house']" />Home</Link>
                </template>
            </div>
        </template>
    </article>
</template>

<script>
import { Link } from '@inertiajs/vue3';

import { ref, nextTick } from 'vue';

import { defineProps } from 'vue';

const props = defineProps(['showTaskCheck', 'showAddJoin', 'isGameQuestDetailsShown', 'gameData', 'isButtonClicked', 'isQuestButtonClicked']);

export default {
    data() {
        return {
            // isQuestButtonClicked: false,
            isSave: false,
            showMenu: false,
            hamburgerActive: false,
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
        gameData: Object,
        isButtonClicked: Boolean,
        isQuestButtonClicked: Boolean
    },
    setup() {
        const showMenu = ref(false);

        const toggleMenu = () => {
            showMenu.value = !showMenu.value;
            if (showMenu.value) {
                // Voeg een korte vertraging toe om de DOM-update te laten voltooien
                nextTick().then(() => {
                    const mobileMenu = document.getElementById('js__mobile_menu');
                    if (mobileMenu) {
                        // Zorg ervoor dat de element classList de animate classes bevat
                        mobileMenu.classList.add('animate__animated', 'animate__fadeIn');
                    }
                });
            } else {
                const mobileMenu = document.getElementById('js__mobile_menu');
                if (mobileMenu) {
                    mobileMenu.classList.remove('animate__animated', '');
                }
            }
        };
        return {
            showMenu,
            toggleMenu
        };
    },
    methods: {
        toggleAddMenu() {
            this.$emit('toggleAddButton');
        },
        toggleQuestMenu() {
            this.$emit('toggleQuestAddButton');
        },
        toggleCriterionButton() {
            this.isSave = !this.isSave;
            this.$emit('isEditing');
        },
        toggleMenu() {
            this.hamburgerActive = !this.hamburgerActive;
            this.showMenu = !this.showMenu;
            if (this.showMenu) {
                nextTick().then(() => {
                    const mobileMenu = document.getElementById('js__mobile_menu');
                    if (mobileMenu) {
                        mobileMenu.classList.add('animate__animated', 'animate__fadeIn');
                    }
                });
            } else {
                const mobileMenu = document.getElementById('js__mobile_menu');
                if (mobileMenu) {
                    mobileMenu.classList.remove('animate__animated', 'animate__fadeIn');
                }
            }
        },

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
        cursor: pointer;
    }

    .hamburger-stroke {
        display: block;
        width: 50px;
        height: 3px;
        background-color: white;
        margin: 7px 0;
        transition: transform 0.3s ease, opacity 0.3s ease, background-color 0.3s ease;
    }

    .hamburger.active .hamburger-stroke:nth-child(1) {
        transform: translateY(15px) rotate(45deg);
    }

    .hamburger.active .hamburger-stroke:nth-child(2) {
        opacity: 0;
    }

    .hamburger.active .hamburger-stroke:nth-child(3) {
        transform: translateY(-18px) rotate(-45deg);
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
        background: rgba(27, 23, 32, 0.9);
        z-index: -10;
        /* Behouden op 10 */
    }

    .mobile-link {
        display: flex;
        justify-content: center;
        align-content: center;
        align-items: baseline;
        gap: .5rem;
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