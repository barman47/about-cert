<template>
  <div class="admin-navbar">
    <div class="user-details-group">
      <div class="user-display-photo" :style="{backgroundImage: 'url(\''+ '/test video.png'+'\')'}"></div>
      <div class="user-text-details">
        <div class="user-name">Solaru Olusegun</div>
        <div class="user-email">segunsolaru@gmail.com</div>
      </div>
    </div>
    <div class="navbar-navigation-group">
      <div class="navbar-navigation" :class="{active : $nuxt.$route.fullPath == '/admin'}" @click="goToPage('/admin')">
        <div class="image" :style="{backgroundImage: 'url(\'/png/pie-chart'+($nuxt.$route.fullPath == '/admin' ? '-active' : '')+'.png\')'}"></div>
      </div>
      <div class="navbar-navigation" :class="{active : $nuxt.$route.fullPath == '/admin/requests'}" @click="goToPage('/admin/requests')">
        <div class="image" :style="{backgroundImage: 'url(\'/png/tap'+($nuxt.$route.fullPath == '/admin/requests' ? '-active' : '')+'.png\')'}"></div>
      </div>
      <div class="navbar-navigation">
        <div class="image" style="background-image: url('/png/admin-ham.png')"></div>
      </div>
      <div class="navbar-navigation">
        <div class="image" style="background-image: url('/png/post-add.png')"></div>
      </div>
    </div>
    <div class="extra-navbar-group">
      <form class="navigation-form">
        <div class="navigation-form-group">
          <input type="text" id="navigation-form-input" class="input" placeholder="Search here">
          <label class="image" for="navigation-form-input"></label>
        </div>
      </form>

      <div class="others" style="background-image: url('/png/plus.png')">
      </div>
      <div class="others bell" style="background-image: url('/png/bell-icon.png')">
        <span class="count">23</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    goToPage(path){
      if(this.$nuxt.$route.fullPath == path)
        return
      this.$nuxt.$router.push(path)
    },
  }
}
</script>

<style scoped>
  .admin-navbar{
    /* background: #1F0051; */
    background: #1b083a;

    box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);

    height: 100%;
    margin: 0;
    color: white;
    padding-left: 1rem;
    padding-right: 1rem;
    font-size: 0.8rem;

    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .admin-navbar div{
    margin : 0;
  }

  .user-details-group {
    display: flex;
    height: 95%;
    align-items: center;
  }

  .user-text-details {
    padding-left: 0.6rem;
  }

  .user-text-details .user-name{
    font-style: normal;
    font-weight: 500;
    font-size: 0.8rem;
    padding-bottom: 0.2rem;
  }

  .user-text-details .user-email{
    font-style: normal;
    font-weight: 300;
    color: #ADADAD;
    font-size: 0.5rem;
  }

  .user-display-photo{
    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;
    clip-path: circle();
    height: 80%;
    width: 40px;
    background-origin: content-box;
  }

  .navbar-navigation-group{
    display: flex;
    align-items: center;
    height: 100%;
  }

  .navbar-navigation-group .navbar-navigation{
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    height: 100%;
    width: 3rem;
  }

  .navbar-navigation-group .navbar-navigation.active{
    background: #35008C;
  }

  .navbar-navigation-group .navbar-navigation .image{
    width: 100%;
    height: 45%;
    background-position: center center;
    background-size: contain;
    background-repeat: no-repeat;
  }

  .extra-navbar-group{
    display: flex;
    align-items: center;
    justify-content: space-between;
    --navigation-form-input-height: 25px;
    --navigation-form-input-background: #555286;
  }

  .navigation-form-group{
    display: flex;
    align-items: center;

    color: #F0F0F0;
  }

  .navigation-form-group .input{
    height: var(--navigation-form-input-height);
    background: var(--navigation-form-input-background);
    border:none;
    border-top-left-radius: 8px;
    border-bottom-left-radius: 8px;
    color: #F0F0F0;
    font-size: 0.65rem;
    padding-left: 0.5rem;
    padding-right: 0.5rem;
    width: 10rem;
  }

  .navigation-form-group .input::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
    color: #F0F0F0;
    opacity: 1; /* Firefox */
  }

  .navigation-form-group .input:-ms-input-placeholder { /* Internet Explorer 10-11 */
    color: #F0F0F0;
  }

  .navigation-form-group .input::-ms-input-placeholder { /* Microsoft Edge */
    color: #F0F0F0;
  }

  .navigation-form-group .image{
    width: var(--navigation-form-input-height);
    height: var(--navigation-form-input-height);
    background-color: var(--navigation-form-input-background);
    border-top-right-radius: 8px;
    border-bottom-right-radius: 8px;
    background-image: url("/png/admin-nav-search.png");
    background-repeat: no-repeat;
    background-size: contain;
    background-position: center center;
    padding: 0.3rem;
    background-origin: content-box;
  }

  .extra-navbar-group .others{
    width: var(--navigation-form-input-height);
    height: var(--navigation-form-input-height);
    background-color: var(--navigation-form-input-background);

    border-radius: 8px;
    margin-left: 0.5rem;

    background-repeat: no-repeat;
    background-size: contain;
    background-position: center center;
    padding: 0.4rem;
    background-origin: content-box;

    cursor: pointer;
  }
  .extra-navbar-group .others.bell{
    position: relative;
  }

  .extra-navbar-group .others.bell .count{
    position: absolute;
    top: -2px;
    right: -2px;
    background: #25FF01;
    padding: 0.1rem;
    font-size: 0.4rem;
    border-radius: 50%;
    color: #484141;
  }
</style>
