<template>
    <article class="unverified-task">
        <h2>Te controleren taken</h2>
        <ul>
            <li v-for="task in unverifiedTasks" :key="task.id">
                {{ task.name }} - {{ task.description }} - {{ task.experience }}
                <button @click="verifyTask(task.id)">Goedkeuren</button>
            </li>
        </ul>
    </article>
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

<style scoped>
.unverified-task {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.unverified-task h2 {
    font-size: 20px;
    color: #495057;
}

.unverified-task button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
</style>