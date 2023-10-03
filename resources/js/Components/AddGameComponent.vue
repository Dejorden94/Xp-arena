<script setup>

</script>

<template>
    <article class="add-game">
        <section class="join-game">
            <h1 @click="$emit('showJoin')">Join Game</h1>
        </section>
        <section class="create-game">
            <h1 @click="$emit('showCreate')">Create-game</h1>
        </section>
    </article>
</template>

<style scoped>
h1 {
    color: var(--font-color-white);
    cursor: pointer;
}

.add-game {
    padding: 0;
}

.join-game {
    width: 100%;
    height: 50%;
    background: linear-gradient(#FDA829, #FF5C00)
}

.create-game {
    width: 100%;
    height: 50%;
    background: linear-gradient(#4DA9FF, #3770A6)
}
</style>