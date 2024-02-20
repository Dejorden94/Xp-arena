<script setup>

</script>

<template>
    <article class="add-game">
        <section class="join-game">
            <h1 @click="$emit('showJoin')">Join Game</h1>
        </section>
        <section class="create-game">
            <h1 @click="$emit('showCreate')">Create game</h1>
        </section>
    </article>
</template>

<style scoped>
h1 {
    font-size: 200%;
    font-weight: 700;
    color: var(--font-color-white);
    cursor: pointer;
}

section {
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 1rem;
}

.add-game {
    display: flex;
    flex-direction: column;
    flex-wrap: nowrap;
    border: none;
    background: none;
    height: 25rem;
    width: 80vw;
    padding: 0;
    position: sticky;
    bottom: 20%;
    z-index: 1;
    /* Zorg ervoor dat het boven de ::before pseudo-element wordt weergegeven */
}

.join-game {
    margin-bottom: 1rem;
    width: 100%;
    height: 50%;
    background: linear-gradient(#FDA829, #FF5C00);
}

.create-game {
    width: 100%;
    height: 50%;
    background: linear-gradient(#4DA9FF, #3770A6);
}
</style>