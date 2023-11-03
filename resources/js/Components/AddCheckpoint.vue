<template>
    <section>
        <form @submit.prevent="addCheck">
            <input class="task-input" type="text" placeholder="Titel" required>

            <button type="submit">Toevoegen</button>
        </form>
    </section>
</template>

<script>
export default {
    methods: {
        addCheck() {
            console.log("doet het ");
        }
    }
}
</script>

<style scoped>
section {
    position: fixed;
    z-index: 1;
    width: 80%;
    left: 50%;
    bottom: 0;
    transform: translate(-50%, -100%);
}

form {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-top: 1rem;
}

.task-input {
    margin-top: 1rem;
    background: var(--background-lighter);
    border: none;
    padding: 0.5rem;
    border-radius: 5px;
}
</style>