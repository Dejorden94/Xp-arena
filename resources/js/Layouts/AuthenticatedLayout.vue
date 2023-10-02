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
                    <div>
                        <div>
                            <!-- Logo -->
                            <div>
                                <Link :href="route('dashboard')">
                                <ApplicationLogo />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div>
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>
                            </div>
                        </div>

                        <div>
                            <!-- Settings Dropdown -->
                            <div>
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
                            </div>
                        </div>

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

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<style></style>