<template>
    <article>
        <button @click="hideGameDetails">Verberg</button>
        <section>
            <h1>{{ gameData.name }}</h1>
        </section>
        <!-- Toon andere gamegegevens hier -->
    </article>
</template>
  
<script>
export default {
    props: {
        gameData: {
            type: Object,
            required: true
        }
    },
    methods: {
        hideGameDetails() {
            this.$emit('hide');
        }
    }
};
</script>

<style scoped>
article {
    width: 100vw;
    display: flex;
    justify-content: center;

}
</style>