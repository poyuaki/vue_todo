<template>
    <div class="start-message">
        <h1 class="start-title">ようこそ</h1>
        <button
            @click="sendLogin()"
            type="button"
            class="btn btn-success pl-3 pr-3 ml-3 login-button"
        >
            始める
        </button>
    </div>
</template>

<script>
let isLogin = false;

let request = new Request("./php/getSession.php")
fetch(request)
.then(response => {return response.json()})
.then(data=>{
    isLogin = data.canLogin
})

let checkLogin = () => {return isLogin}

export default {
    data(){
        return{
            isLogin: isLogin
        }
    },
    methods:{
        sendLogin(){
            this.$emit('is-login',checkLogin())
        }
    }
}
</script>

<style scoped>
.start-title{
    text-align: center;
    color: #333;
}

.start-message{
    text-align: center;
}
</style>