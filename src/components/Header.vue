<template>
    <header>
      <transition name="vheader">
        <img v-bind:src="userImg" class="logo" v-show="canViewHeader">
      </transition>
        <div class="header">
            <h1 class="header-title">Todo</h1>
            <transition name="vheader">
              <h3 class="header-sub" id="header-sub" v-show="canViewHeader">{{headerMessage}}</h3>
            </transition>
        </div>
    </header>
</template>

<script>
export default {
  props: {
    headerMessage: {
      type: String,
      defalut: 'hello,vue.js'
    },
    userImg: {
      type: String,
      defalut: './assets/logo.png'
    },
    canViewHeader:{
      type: Boolean
    }
  },
  data(){
    return{
      canViewHeader: this.canViewHeader
    }
  }
}
</script>

<style>
.header{
    text-align: center
}

.header-title{
    font-size: 4em;
    margin:0;
    color: #42b983
}

.header-sub{
    font-size: 1.5em;
    margin-top: 0;
    color: #4f5959
}

.logo{
    z-index: -1;
    position: absolute;
    left: 70%;
    transform: translateX(-70%);
    opacity: 0.3;
    border-radius: 50%;
    margin-top: 10px;
}

.vheader-enter-active, .vheader-leave-active{
    transition: all .5s
}

.vheader-enter, .vheader-leave-to{
    opacity: 0;
    transform: translateY(30px);
}
</style>
