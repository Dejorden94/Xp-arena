// UnverifiedTasks.vue

<template>
    <article>
        <h2>Te verifiëren taken</h2>
        <ul>
            <li v-for="task in unverifiedTasks" :key="task.id" @click="verifyTask(task.id)">
                {{ task.name }}
            </li>
        </ul>
    </article>
</template>

<script>
export default {
    props: {
        unverifiedTasks: {
            type: Array,
            required: true,
        },
    },
    methods: {
        verifyTask(taskId) {
            // Plaats hier de logica om een taak te verifiëren
            // Dit kan een AJAX-verzoek naar de server bevatten om de taak te markeren als geverifieerd
            // Nadat de taak is geverifieerd, kun je deze uit de lijst met ongeverifieerde taken verwijderen
            // bijvoorbeeld door het op te filteren uit de array.
        },
    },
};
</script>

<style scoped>
/* Stijlen voor het UnverifiedTasks-component */
article {
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 1rem;
    margin-bottom: 2rem;
}

h2 {
    color: #333;
    margin-bottom: 1rem;
}

ul {
    padding-left: 1rem;
}

li {
    margin-bottom: 0.5rem;
    cursor: pointer;
}

li:hover {
    color: #007BFF;
}
</style>
