<template>
    <article>
        <h2>Te verifiëren taken</h2>
        <ul>
            <li v-for="(task, index) in unverifiedTasks" :key="task.id">
                <button @click="showTaskDetails(task.id)">Toon Details</button>
                {{ userNames[task.follower_id] || 'Laden...' }}
                {{ task.name }}
                <button @click="acceptTask(task.id, index)">Accepteren</button>
                <button @click="declineTask(task.id, index)">Afwijzen</button>
            </li>
        </ul>
        <section v-if="isDetailsModalOpen" class="modal">
            <h3>Taakdetails</h3>
            <ul>
                <li v-for="(critertion, index) in currentTaskDetails">
                    <p v-if="currentTaskDetails">Criteria: {{ critertion.description }}</p>
                    <p v-if="currentTaskDetails">Is voldaan: {{ critertion.is_met }}</p>
                </li>
            </ul>
            <button @click="isDetailsModalOpen = false">Sluiten</button>
        </section>
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
    data() {
        return {
            userNames: {},
            currentTaskDetails: null,
            isDetailsModalOpen: false,
        }
    },
    mounted() {
        this.fetchUserNames();
    },
    methods: {
        async fetchUserNames() {
            for (let task of this.unverifiedTasks) {
                try {
                    const response = await axios.get(`/users/${task.follower_id}`);
                    this.userNames[task.follower_id] = response.data.user.name;
                } catch (error) {
                    console.error('Fout bij het ophalen van gebruikersnaam:', error);
                }
            }
        },
        async showTaskDetails(taskId) {
            try {
                const response = await axios.get(`/follower-tasks/${taskId}/details`);
                this.currentTaskDetails = response.data;
                this.isDetailsModalOpen = true;
            } catch (error) {
                console.error('Fout bij het ophalen van taakdetails:', error);
            }
        },
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
                    console.log(`Taak afgewezen: ${taskId}`);
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
    background-color: var(--background-super-dark);
    border-radius: 1rem;
    padding: 1rem;
    margin-bottom: 2rem;
    color: var(--font-color-normal);
}

h2 {
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
