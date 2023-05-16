<template>
    <article>
        <h1>{{ gameData.name }}</h1>
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
    }
};
</script>
