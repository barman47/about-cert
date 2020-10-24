<template>
  <div id="login">
    <div id="wink-container">
      <img src="~/assets/png/waving-hand 1.png" alt />
    </div>
    <div class="login-header">
      <h2 class="head">Welcome Back</h2>
      <p>Smart ass, change of password successful</p>
    </div>

    <form action class="form" id="login-form" @submit.prevent="login()">
      <div class="form-group">
        <input-label inputId="email">Email</input-label>
        <input-field
          autocomplete="email"
          ref="email"
          type="text"
          placeholder="email@johnhdoe.com"
          name="email"
          id="email"
          leftImage="/png/email.png"
          :required="true"
        />
      </div>
      <div class="form-group">
        <input-label inputId>Password</input-label>
        <input-field
          autocomplete="new-password"
          ref="password"
          type="password"
          leftImage="/png/password-lock.png"
          name="password"
          id
          placeholder="Enter your password"
          :required="true"
        />
      </div>
      <div class="form-group terms">
        <input type="checkbox" value="1" ref="stayLoggedInCheckbox"/>
        <span class="display-flex align-items-start justify-content-space-between">
          Stay logged in
          <a class="dummy-align" style="justify-content:flex-end" href="#">Forgot password?</a>
        </span>
      </div>
      <div id="login-button-grouping">
        <div style="display:flex; justify-content: flex-end; margin-top: 20px;">
          <button type="submit" class="btn" id="login-button" ref="loginButton">Login</button>
        </div>
        <div class="error" v-if="errorMessage">{{errorMessage}}</div>
        <div class="sign-in-up-nav-container">
          <sign-in-up-nav />
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import InputField from "~/components/InputField";
import SelectField from "~/components/SelectField";
import InputLabel from "~/components/InputLabel";
import SignInUpNav from "~/components/SignInUpNav";

export default {
  components: {
    InputField,
    InputLabel,
    SignInUpNav,
    SelectField
  },
  layout: "register-login",
  data() {
    return {
      errorMessage: undefined
    };
  },
  methods: {
    login() {
      let loginButton = this.$refs.loginButton;

      loginButton.setAttribute("disabled", "true");
      loginButton.classList.add("progress");

      this.errorMessage = "";
      let attempt = this.$store.dispatch("login", {
        email: this.$refs.email.value(),
        password: this.$refs.password.value()
      });
      let self = this;
      attempt
        .then(() => {
            // debugger

            if(!self.$refs.stayLoggedInCheckbox.checked){
                // localStorage.setItem("auth._token.password_grant", "")
            }
            this.$nuxt.$router.push("/")
        })
        .catch(err => {
          self.errorMessage = err.data.message;
        })
        .finally(() => {
          loginButton.removeAttribute("disabled", "true");
          loginButton.classList.remove("progress");
        });
    }
  }
};
</script>

<style scoped>
#login-button {
  width: 100%;
  background: #efffff;
  border: 1px solid #0084ff;
  color: #0084ff;
  cursor: pointer;
}

#login-button.progress {
  cursor: progress;
  background: #eee;
  color: #9b9b9b;
  border-color: #9b9b9b;
}
#login-form {
  display: flex;
  flex-direction: column;
  flex: 1;
}

#login-button-grouping {
  display: flex;
  display: flex;
  flex-direction: column;
  width: 100%;
  justify-content: space-between;
  flex: 1;
}

.dummy-align {
  display: flex;
  align-items: center;
}

.error {
  background: black;
  color: white;
  font-size: 0.8rem;
  border-radius: 5px;
  padding: 0.5rem;
  margin-top: 1rem;
  margin-bottom: 0.3rem;
}
</style>
