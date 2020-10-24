<template>
  <div id="login">
    <div id="wink-container">
      <img src="~/assets/png/wink.png" alt />
    </div>
    <div class="login-header">
      <h2 class="head">Create an account</h2>
      <p>There are no strangers here, only friends you’re yet to meet</p>
    </div>

    <form action class="form" @submit.prevent="register()">
      <div class="form-group">
        <input-label inputId="name">Name</input-label>
        <input-field
          autocomplete="name"
          ref="name"
          type="text"
          placeholder="Enter Full Name"
          name="name"
          id="name"
          :required="true"
        />
        <div class="error" v-if="errors && errors.name">
          <ul>
            <li v-for="(error, i) in errors.name" :key="i">{{error}}</li>
          </ul>
        </div>
      </div>
      <div class="form-group">
        <input-label inputId="email">Email</input-label>
        <input-field
          autocommplete="email"
          ref="email"
          type="email"
          placeholder="email@johnhdoe.com"
          name="email"
          id="email"
          leftImage="/png/email.png"
          :required="true"
        />
        <div class="error" v-if="errors && errors.email">
          <ul>
            <li v-for="(error, i) in errors.email" :key="i">{{error}}</li>
          </ul>
        </div>
      </div>
      <div class="form-group grid-3-1">
        <div>
          <input-label inputId>Mobile Number</input-label>
          <div class="grid-1-2">
            <select-field
              ref="phone_number_head"
              type="text"
              name
              id="country-code"
              leftImage="/png/nigeria 1.png"
              :customStyle="{'background-size' : '18px 18px', 'text-indent' : '2px'}"
              :required="true"
            >
              <option value="+234">+234</option>
            </select-field>
            <input-field
              type="number"
              ref="phone_number_tail"
              placeholder
              name
              id
              :required="true"
            />
          </div>
        </div>
        <div>
          <input-label inputId="gender">Gender</input-label>
          <select-field type="text" name id="gender" :required="true" ref="gender">
            <option value></option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Others">Others</option>
          </select-field>
        </div>
      </div>
      <div class="form-group">
        <input-label inputId="password">Password</input-label>
        <input-field
          autocomplete="new-password"
          leftImage="/png/password-lock.png"
          type="password"
          name
          id="password"
          placeholder="Enter your password"
          :required="true"
          ref="password"
        />
        <div class="error" v-if="errors && errors.password">
          <ul>
            <li v-for="(error, i) in errors.password" :key="i">{{error}}</li>
          </ul>
        </div>
      </div>
      <div class="form-group">
        <input-label inputId="password_confirmation">Confirm Password</input-label>
        <input-field
          autocomplete
          leftImage="/png/password-lock.png"
          type="password"
          id="password_confirmation"
          placeholder="Re-enter your password"
          :required="true"
          ref="password_confirmation"
        />
        <div class="error" v-if="errors && errors.passwordConfirmation">
          <ul>
            <li v-for="(error, i) in errors.passwordConfirmation" :key="i">{{error}}</li>
          </ul>
        </div>
      </div>
      <div class="form-group terms">
        <input type="checkbox" value="1" required />
        <span>
          Yes, I agree to the AboutCert
          <a href="#">terms of use</a>
        </span>
      </div>
      <div style="display:flex; justify-content: flex-end; margin-top: 10px;">
        <button type="submit" class="btn" style="cursor: pointer">Next ></button>
      </div>
    </form>
    <div class="sign-in-up-nav-container">
      <sign-in-up-nav />
    </div>
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
      errors: {}
    };
  },
  methods: {
    register() {
      let refs = this.$refs;
      let data = {
        name: refs.name.value(),
        email: refs.email.value(),
        mobile_number:
          refs.phone_number_head.value() + refs.phone_number_tail.value(),
        password: refs.password.value(),
        password_confirmation: refs.password_confirmation.value(),
        type: "person",
        gender: refs.gender.value()
      };

      if (data.password != data.password_confirmation) {
        this.errors = {
            passwordConfirmation: [
                this.errors.passwordConfirmation ? "Password fields do not still match" : "Password fields do not match"
            ]
        }
        return;
      }

      this.errors = {};

      let attempt = this.$store.dispatch("register", data);
      let self = this;

      attempt
        .then(() => self.$nuxt.$router.push("/successful-signup"))
        .catch(err => {
          if (err.status == 406) {
            this.errors = err.data.errors;
          }
        });
    }
  }
};
</script>

<style scoped>
#gender {
  text-indent: 5px;
}

.error {
  background: black;
  color: white;
  padding: 0.2rem;
  border-radius: 5px;
  font-size: 0.7rem;
  margin-top: 0.2rem;
}

.error ul {
  list-style-type: hebrew;
  padding-inline-start: 1.5rem;
}
</style>
