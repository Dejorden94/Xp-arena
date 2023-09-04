// UnverifiedTasks.vue

<template>
    <article>
        <h2>Te verifiëren taken</h2>
        <ul>
            <li v-for="(task, index) in unverifiedTasks" :key="task.id">
                {{ task.name }}
                <button @click="acceptTask(task.id, index)">Accepteren</button>
                <button @click="declineTask(task.id, index)">Afwijzen</button>
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
        async acceptTask(taskId, index) {
            try {
                // Doe een API-aanroep om de taak te verwijderen
                const response = await axios.delete(`/follower-tasks/${taskId}`);

                if (response.status === 200) {
                    // Taak succesvol verwijderd, bijwerken van de weergave
                    this.unverifiedTasks.splice(index, 1);
                    console.log(`Taak geaccepteerd en verwijderd: ${taskId}`);
                } else {
                    console.error('Fout bij het verwijderen van de taak');
                }
            } catch (error) {
                console.error('Fout bij het verwijderen van de taak:', error);
            }
        },
        async declineTask(taskId, index) {
            try {
                // Doe een API-aanroep om de taak af te wijzen
                const response = await axios.put(`/follower-tasks/${taskId}`);

                if (response.status === 200) {
                    // Taak succesvol afgewezen, bijwerken van de weergave
                    this.unverifiedTasks.splice(index, 1);
                    console.log(`Taak afgewezen en verwijderd: ${taskId}`);
                } else {
                    console.error('Fout bij het afwijzen van de taak');
                }
            } catch (error) {
                console.error('Fout bij het afwijzen van de taak:', error);
            }
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
</style>
