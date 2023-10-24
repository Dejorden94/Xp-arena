<template>
    <article>
        <h1>Quest details van {{ quest.name }}</h1>
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
    created() {
        console.log("GameQuestDetails component is geïnitialiseerd!");
    }
}
</script>
<style scoped>
article {
    height: 5rem;
    width: 80vw;
    margin: 0 auto;
    background: red;
}
</style>