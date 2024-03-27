<template>
    <section>
        <form @submit.prevent="addTask" ref="taskForm">
            <input class="task-input" v-model="newTaskName" type="text" placeholder="Titel" required>
            <textarea class="task-input" v-model="newTaskDescription" placeholder="Beschrijving" required></textarea>
            <input type="file" @change="handleFileUpload" id="quest_image">

            <button type="submit" @click="$emit('refreshTasks')">Toevoegen</button>
            <span v-if="errorMessage" class="error-message">{{ errorMessage }}</span>
        </form>
    </section>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        gameData: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            questImage: null,
            errorMessage: ''
        }
    },
    methods: {
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file || !file.type.startsWith('image/')) {
                this.errorMessage = 'Gelieve een afbeeldingsbestand te selecteren.';
                // Reset het inputveld voor bestanden
                this.$refs.taskForm.reset();
                return;
            }
            this.questImage = file;
            this.errorMessage = '';
        },
        async addTask() {
            if (!this.questImage) {
                this.errorMessage = 'Gelieve een afbeelding te selecteren.';
                return;
            }
            let formData = new FormData();
            formData.append('name', this.newTaskName);
            formData.append('description', this.newTaskDescription);
            formData.append('quest_image', this.questImage);
            try {
                const questResponse = await axios.post(`/games/${this.gameData.id}/add-task`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                    }
                })
                console.log(questResponse);
            } catch (error) {
                console.log("Fout bij het maken van de quest: ", error);
            }
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

.error-message {
    color: red;
    margin-top: 0.5rem;
}
</style>
