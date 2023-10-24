<template>
    <article>
        <h1>Quest details van {{ questName }}</h1>

        <button @click="showAddCriteriaForm = true">Voeg criteria toe</button>

        <section v-if="showAddCriteriaForm">
            <input v-model="newCriterionName" placeholder="Naam van criterium">
            <button @click="addCriteria">Opslaan</button>
        </section>

        <ul>
            <li v-for="criterion in criteria" :key="criterion.id">{{ criterion.name }}</li>
        </ul>
    </article>
</template>
<script>
export default {
    props: {
        quest: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            criteria: [],
            showAddCriteriaForm: false,
            newCriterionName: ''
        };
    },
    computed: {
        questName() {
            return this.quest && this.quest.name ? this.quest.name : 'Loading...';
        }
    },

    methods: {
        async addCriteria() {
            try {
                const response = await axios.post(`/api/tasks/${this.quest.id}/criteria`, {
                    name: this.newCriterionName
                });
                this.criteria.push(response.data);  // Voeg het nieuwe criterium toe aan de lijst
                this.newCriterionName = '';  // Reset het invoerveld
                this.showAddCriteriaForm = false;  // Verberg het formulier
            } catch (error) {
                console.error("Er is een fout opgetreden bij het toevoegen van het criterium:", error);
            }
        }
    }
}
</script>
<style scoped>
article {
    height: 5rem;
    width: 80vw;
    margin: 0 auto;
    background: var(--background-super-dark);
    border: 2px solid var(--background-lighter);
    border-radius: 1rem;
}
</style>