<template>
  <div id="successful-login-container">
    <div id="top-vector"></div>
    <div id="bottom-vector"></div>
    <div class="content-container">
      <div>
        <h3 class="header">Congratulations!</h3>
        <p class="greetings">You just got yourself an important space on AboutCert</p>
        <div class="people-image"></div>
        <div class="info">
          <p>Make sure you verify your email on your profile page to avoid your account from being deleted within 24hrs</p>
          <button @click="goToLogin()">Proceed</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  middleware: "guest",
  layout: "empty",
  methods: {
    goToLogin() {
      this.$nuxt.$router.push("/login");
    }
  }
};
</script>

<style scoped>
#successful-login-container {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;

  padding: 1rem;
  background: linear-gradient(
    162.19deg,
    #1129bb 4.99%,
    rgba(0, 119, 183, 0.88) 87.99%
  );
  color: white;
}

#successful-login-container * {
  font-style: Poppins;
}

#top-vector,
#bottom-vector {
  position: absolute;
  height: 40%;
  width: 80%;
  background-size: contain;
  background-repeat: no-repeat;
}

#top-vector {
  top: -1rem;
  left: -1rem;
  background-image: url("/png/successful-signup/top.png");
  background-position: left top;
  z-index: 1;
}

#bottom-vector {
  bottom: -1rem;
  right: -1rem;
  background-image: url("/png/successful-signup/bottom.png");
  background-position: right bottom;
}

.content-container {
  height: 100%;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.content-container > div:first-child {
  height: 100%;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.header {
  margin-bottom: 0.2rem;
  font-size: 2rem;
}

.greetings {
  margin: 0;
  text-align: center;
  max-width: 300px;
  font-size: 0.9rem;
}

.people-image {
  display: flex;
  width: 80%;
  background-image: url("/png/successful-signup/people.png");
  background-repeat: no-repeat;
  background-position: top center;
  background-size: contain;
  flex: 1;
}

.info {
  color: #ffd2a8;
  position: fixed;
  padding: 1rem;
  left: 2rem;
  bottom: 2rem;
  max-width: 400px;
  font-size: 0.8rem;
  z-index: 20;
}

.info button {
  background: #ffffff;
  border-radius: 80px;
  border: none;
  outline: none;
  padding: 0.7rem;
  padding-left: 1rem;
  padding-right: 1rem;
  color: #1c75bf;

  font-family: Montserrat;
  font-style: normal;
  font-weight: normal;
  font-size: 1rem;
}

button:active {
  outline: none;
}

@media (max-width: 1199.98px) {
  .info {
    max-width: 300px;
  }

  .info button {
    padding: 0.5rem;
    padding-left: 1rem;
    padding-right: 1rem;
  }
}

@media (max-width: 991.98px) {
  .info {
    max-width: 350px;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
  }
}

@media (max-width: 575.98px) {
  .info {
    max-width: 60%px;
    left: unset;
    right: unset;
  }

  .people-image {
    width: 100%;
  }
}
</style>