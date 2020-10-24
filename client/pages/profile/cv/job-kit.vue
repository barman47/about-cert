<template>
  <div id="job-kit">
    <div>
      <div
        class="job-box display-flex justfy-content-center align-items-center"
        :class="{active: active == 'basic'}"
        @click="changeTab('basic')"
      >
        <div>
          <div class="box-head">
            <span class="ham"></span> &nbsp; Basic
          </div>
          <div class="box-middle">
            <span class="currency">NGN</span> &nbsp;
            <span class="price">10k</span>
          </div>
          <div class="box-bottom">CV in 7 days</div>
        </div>
      </div>
      <div
        class="job-box display-flex justfy-content-center align-items-center"
        :class="{active: active == 'standard'}"
        @click="changeTab('standard')"
      >
        <div>
          <div class="box-head">
            <span class="ham"></span> &nbsp; Standard
          </div>
          <div class="box-middle">
            <span class="currency">NGN</span> &nbsp;
            <span class="price">25k</span>
          </div>
          <div class="box-bottom">CV in 5 days</div>
        </div>
      </div>
      <div
        class="job-box display-flex justfy-content-center align-items-center"
        :class="{active: active == 'premium'}"
        @click="changeTab('premium')"
      >
        <div>
          <div class="box-head">
            <span class="ham"></span> &nbsp; Premium
          </div>
          <div class="box-middle">
            <span class="currency">NGN</span> &nbsp;
            <span class="price">40k</span>
          </div>
          <div class="box-bottom">CV in 2 days</div>
        </div>
      </div>
    </div>
    <div class="main-box">
      <!-- Basic -->
      <div v-show="active == 'basic'">
        <div class="option-list">
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">Professionally written CV tailored to your needs and work.</p>
          </div>
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">CV developed in 7 working days</p>
          </div>
          <div class="option option-extra">
            <span class="option-tick"></span>
            <p class="option-text">1 revision of CV</p>
            <p
              class="option-extra-text"
            >You can give feedback on the work done for re-adjusments once</p>
          </div>
        </div>
      </div>
      <!-- Standard -->
      <div v-show="active == 'standard'">
        <div class="option-list">
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">Professionally written CV tailored to your needs and work.</p>
          </div>
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">CV developed in 5 working days</p>
          </div>
          <div class="option option-extra">
            <span class="option-tick"></span>
            <p class="option-text">3 revision of CV</p>
            <p
              class="option-extra-text"
            >You can give feedback on the work done for re-adjusments 3 times</p>
          </div>
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">Cover letter</p>
          </div>
        </div>
      </div>
      <!-- Premium -->
      <div v-show="active == 'premium'">
        <div class="option-list">
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">Professionally written CV tailored to your needs and work.</p>
          </div>
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">CV developed in 2 working days</p>
          </div>
          <div class="option option-extra">
            <span class="option-tick"></span>
            <p class="option-text">5 revision of CV</p>
            <p
              class="option-extra-text"
            >You can give feedback on the work done for re-adjusments 5 times</p>
          </div>
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">Cover letter</p>
          </div>
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">AboutCert personality test</p>
          </div>
          <div class="option">
            <span class="option-tick"></span>
            <p class="option-text">AboutCert recommendation and a cover letter [CERTIFIED ABOUTCERT]</p>
          </div>
        </div>
      </div>
      <div class="main-box-space-filler">
        <div class="success-message" v-if="successMessage">
          <div class="text">{{successMessage}}</div>
          <span class="close" @click="successMessage = undefined">&times;</span>
        </div>
      </div>
      <!-- Form -->
      <div>
        <form @submit.prevent="submitForm()">
          <div class="form-group">
            <label for class="label">Job Title</label>
            <input v-model="jobTitle" required type="text" list="job-titles" class="form-input" />
          </div>

          <div class="form-group">
            <label for class="label">Anything you’d like to tell us?</label>
            <textarea v-model="others" name id rows="5" class="form-input"></textarea>
          </div>

          <div class="submit-button-container">
            <button id="submit-button">
              <span class="image"></span> Request/Make Payment
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Data Lists -->
    <datalist id="job-titles">
      <option v-for="(job, i) in allJobtitles" :key="i" :value="job" />
    </datalist>
  </div>
</template>

<script>
export default {
  components: {},
  layout: "profile",
  async fetch({ store, $axios }) {
    if (store.state.profile.allJobtitles.length <= 0) {
      $axios.get("/api/job-titles/get-all").then(response => {
        store.commit("profile/getAllJobTitles", response.data);
      });
    }

    if (!store.state.profile.fetched)
      await $axios.get("/api/profile").then(response => {
        store.commit("profile/fetch", response.data);
      });
  },
  data() {
    return {
      active: "basic",
      jobTitle: undefined,
      others: undefined,
      processing: false,
      successMessage: undefined
    };
  },

  computed: {
    allJobtitles() {
      return this.$store.state.profile.allJobtitles;
    }
  },

  mounted() {
    if (this.$nuxt.$route.query.success == "success")
      this.successMessage =
        "The Request For A Tailored CV Has Been Successfully Made";
    setTimeout(() => {
      this.successMessage = undefined;
    }, 9000);
  },

  methods: {
    changeTab(val) {
      this.active = val;
    },
    submitForm() {
      if (this.processing == true) return;

      this.successMessage = undefined;

      const data = {
        redirect_url:
          process.env.CLIENT_BASE_URL +
          this.$nuxt.$route.fullPath +
          "?success=success",
        type: this.active,
        job_title: this.jobTitle,
        others: this.others
      };

      this.processing = true;

      let attempt = this.$store.dispatch("cv/jobKitRequest", data);

      attempt
        .then(paymentLink => {
          window.location = paymentLink;
          // window.open(paymentLink);
          // window.open(paymentLink, '_blank');
        })
        .finally(() => {
          this.processing = false;
        });
    }
  }
};
</script>

<style scoped>
#job-kit {
  display: grid;
  grid-template-columns: 2fr 5fr;
  height: 100%;

  border: 1px solid #b2b2b2;
  border-radius: 11px;
  background: white;
}

#job-kit > div {
  padding: 0.5rem;
}

#job-kit > div:first-child {
  background: unset;
  padding: 0;
  display: grid;
  border-right: 1px solid #b2b2b2;
  grid-template-rows: 1fr 1fr 1fr;
}

#job-kit > div:first-child > div {
  border-bottom: 1px solid #b2b2b2;
}

#job-kit > div:first-child > div:last-child {
  border-bottom: none;
}

.job-box {
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

.job-box * {
  font-family: "Poppins", sans-serif;
  color: #797979;
}

.job-box > div {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.box-middle,
.box-head,
.box-bottom {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.2rem;
}

.box-middle {
  align-items: flex-start;
}

.box-head {
  font-size: 0.9rem;
  border-bottom: 0.5rem;
}

.box-head .ham {
  height: 0.8rem;
  width: 2rem;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  background-image: url("/png/job-ham.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center right;
}

.box-middle .price {
  font-size: 2.3rem;
  color: #a2a2a2;
  font-weight: bold;
}

.box-middle .currency {
  font-size: 0.8rem;
  color: #a1a1a1;
}

.box-bottom {
  font-size: 0.8rem;
  color: #27debf;
}

.job-box.active {
  border-right: 3px solid #52afe7;
}

.job-box.active .box-head {
  color: #0084ff;
}

.job-box.active .box-head .ham {
  background-image: url("/png/job-ham-active.png");
}

/* Main content  */
.main-box {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  overflow-y: auto;
}
.main-box * {
  color: #3a2c51;
}

.main-box-space-filler {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.success-message {
  background: rgba(39, 222, 191, 0.7);
  border-radius: 5px;
  padding: 0.5rem;
  margin: 0.5rem;
  display: inline-flex;
  align-items: flex-start;
  justify-content: space-between;
  flex-direction: row;
}

.success-message * {
  margin: 0;
}

.success-message .text {
  color: white;
  font-size: 0.7rem;
}

.success-message .close {
  color: white;
  font-size: 1rem;
  height: 100%;
  width: 2rem;
  display: flex;
  justify-content: flex-end;
  cursor: pointer;
}

.option * {
  margin: 0;
  font-style: normal;
  font-family: "Poppins", sans-serif;
}

.option {
  display: grid;
  grid-template-columns: 20px 1fr;
  column-gap: 0.5rem;
  margin-bottom: 0.3rem;
}

.option.option-extra {
  grid-template-columns: 20px 2fr 4fr;
}

.option-tick {
  height: 100%;
  background-image: url("/png/option-tick.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: top right;
  padding: 0.2rem;
  background-origin: content-box;
}

.option-text {
  font-size: 0.8rem;
}

.option-extra-text {
  font-size: 0.7rem;
  color: #3a2c51;
  padding-left: 1rem;

  border-left: 2px solid #3a2c51;
}

/* Form  */

.form * {
  font-size: 0.9rem;
}

.form-group {
  margin-bottom: 1rem;
}

.label {
  margin-bottom: 0.5rem;
}

.form-input {
  background: rgba(196, 196, 196, 0.3);
  border: 1px solid rgba(165, 165, 165, 0.8);
  border-radius: 3px;
}

input.form-input {
  height: 25px;
  font-size: 0.8rem;
}

.submit-button-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

#submit-button {
  background: #0084ff;
  color: white;
  border-radius: 30px;
  padding: 0.4rem;
  padding-left: 1rem;
  padding-right: 1rem;
  border: none;
  font-size: 0.7rem;
}

#submit-button .image {
  background-image: url("/png/give-money.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center center;
  background-origin: content-box;
  /* padding: 0.5rem; */

  display: inline-flex;
  align-items: center;
  justify-content: flex-start;

  width: 1.5rem;
  height: 0.8rem;
}

@media (max-width: 575.98px) {
  .box-middle .currency{
    font-size: 0.7rem;

  }

  .box-middle .price{
    font-size: 1.7rem;
  }

  .box-bottom{
    font-size: 0.7rem;
  }

  .box-head{
    font-size: 0.7rem;
  }

  .box-head .ham{
    width: 1rem;
  }
}
</style>
