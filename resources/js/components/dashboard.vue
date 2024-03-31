<template>

    <navbar></navbar>

    <router-view></router-view>



</template>

<script>

    import navbar from './child/nav.vue'

    export default {
        components : {
            navbar
        },

    }
</script>

<style scoped>

</style>
