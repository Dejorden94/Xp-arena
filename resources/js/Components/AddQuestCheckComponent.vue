<script setup>

</script>

<template>
    <article class="add-quest-check">
        <section @click="$emit('showQuest')" class="choise add-quest">
            <h1>Quest</h1>
            <img src="../../../public/images/icons/quest.png" alt="Klik hier om een quest aan te maken">
        </section>
        <section @click="$emit('showCheck')" class="choise add-checkpoint">
            <h1>Checkpoint</h1>
            <img src="../../../public/images/icons/checkpoint.png" alt="Klik hier om een checkpoint aan te maken">
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

.choise {
    cursor: pointer;
    display: flex;
    flex-direction: column;
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
    position: sticky;
    bottom: 0;
    left: 50%;
    z-index: 1;
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