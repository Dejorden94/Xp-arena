<template>
    <article v-if="gameData.isUserOwner"> <!--Compnent wanneer owner de game laad-->
        <button @click="handleButtonClick">Verberg</button>
        <section>
            <h1>{{ gameData.name }}</h1>
            <h2>{{ gameData.pin_code }}</h2>
        </section>
    </article>

    <article v-if="!gameData.isUserOwner"> <!--Component wanneer NIET de owner de game laad-->
        <button @click="handleButtonClick">Verberg</button>
        <section>
            <h1>{{ gameData.name }}</h1>
            <h2>{{ gameData.description }}</h2>
            <p>{{ gameData.owner }}</p>
        </section>
    </article>
</template>
  
<script>
import AddQuest from '@/Components/AddQuest.vue';
export default {
    props: {
    gameData: {
        type: Object,
        required: true
    }
},
    methods: {  
        fetchTasks() {
            if (this.gameData.id) {
                axios.get(`/api/games/${this.gameData.id}/tasks`)
                    .then(response => {
                        this.gameData.tasks = response.data.tasks;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        },
        deleteTask(taskId) {
            axios.delete(`/api/games/${this.gameData.id}/tasks/${taskId}`)
                .then(response => {
                    // Verwijder de taak uit de array
                    const index = this.gameData.tasks.findIndex(task => task.id === taskId);
                    if (index !== -1) {
                        this.gameData.tasks.splice(index, 1);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        },
        handleButtonClick() {
            this.$emit('hide');
            this.$emit('handleGame');
            this.$emit('setUserDetailsTrue');
        }
    },
    created() {
        this.fetchTasks();
        if (this.gameData.isUserOwner === true) {
            this.$emit("setUserDetailsFalse");
        }
    },
    components: { AddQuest }
};
</script>

<style scoped>
article {
    width: 80%;
    padding: 1rem;
    background-color: var(--background-super-dark);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    border: 2px solid var(--background-lighter);
    margin: 0 auto 1rem auto;
}
</style>

