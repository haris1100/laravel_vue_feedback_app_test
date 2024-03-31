<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <!-- Logout button on the left -->


            <!-- Brand/logo (optional) -->
            <a class="navbar-brand" href="#"></a>

            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <router-link class="text-white" :to="{ path: '/app/dashboard/feedback' }">Feedback</router-link>
                    </li>
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" href="#">About</a>-->
<!--                    </li>-->
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" href="#">Services</a>-->
<!--                    </li>-->
<!--                    <li class="nav-item">-->
<!--                        <a class="nav-link" href="#">Contact</a>-->
<!--                    </li>-->
                </ul>
            </div>
            <button id="logout_btn" class="btn btn-outline-danger text-warning" @click="logout"><img src="https://cdn-icons-png.flaticon.com/512/10313/10313098.png" width="20" alt=""> Logout</button>
        </div>
    </nav>

</template>

<script>
    import common from "../../template_setups/common/common";
    export default {
        methods:{
            logout(){
                if(window.confirm('Do you wish to logout?'))
                axios.post('/logout').then(response => {
                    common.set_user_auth();
                    this.$router.push({path : '/app'});
                }).catch(error => {
                    console.log(error);
                })
            }
        }
    }
</script>

<style scoped>
#logout_btn{
    width: 10%;
}
</style>
