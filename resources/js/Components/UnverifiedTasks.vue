<template>
    <div>
        <h2>Te controleren taken</h2>
        <ul>
            <li v-for="task in unverifiedTasks" :key="task.id">
                {{ task.name }} - {{ task.description }} - {{ task.experience }}
                <button @click="verifyTask(task.id)">Goedkeuren</button>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            unverifiedTasks: [],
        }
    },
    created() {
        axios.get('/tasks/unverified')
            .then(response => {
                console.log(response.data);
                this.unverifiedTasks = response.data;
            })
            .catch(error => {
                console.error(error);
            });
    },
    methods: {
        verifyTask(taskId) {
            axios.put(`/tasks/${taskId}/verify`)
                .then(response => {
                    // Verwijder de taak uit de lijst
                    const index = this.unverifiedTasks.findIndex((task) => task.id === taskId);
                    if (index !== -1) {
                        this.unverifiedTasks.splice(index, 1);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
};
</script>
