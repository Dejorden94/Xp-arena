<script setup>

</script>

<template>
    <article class="add-quest-check">
        <section class="add-quest">
            <h1 @click="$emit('showQuest')">Quest</h1>
        </section>
        <section class="add-checkpoint">
            <h1 @click="$emit('showCreate')">Checkpoint</h1>
        </section>
    </article>
</template>

<style scoped>
h1 {
    font-size: 150%;
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

.add-quest-check {
    display: flex;
    flex-direction: row;
    gap: 1rem;
    border: none;
    background: none;
    height: 25rem;
    width: 80vw;
    padding: 0;
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translate(-50%, 0);
    z-index: 1;
    /* Zorg ervoor dat het boven de ::before pseudo-element wordt weergegeven */
}

.add-quest {
    margin-bottom: 1rem;
    width: 50%;
    height: 50%;
    background: linear-gradient(#FDA829, #FF5C00);
}

.add-checkpoint {
    width: 50%;
    height: 50%;
    background: linear-gradient(#4DA9FF, #3770A6);
}
</style>