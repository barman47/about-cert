<template>
  <div id="authenticated-layout-content">
    <div id="profile-strip-container">
      <ProfileStripOthers />
    </div>
    <div id="profile-other-content">
      <div>
        <profile-nav-others></profile-nav-others>
      </div>
      <div id="profile-other-container">
        <div class="edit-profile">
          <div class="column1">
            <div class="description-card">
              <h4 class="description-title">Job Title</h4>
              <div class="description-item" v-if="jobTitle">
                <h5 class="description-item-header">{{jobTitle}}</h5>
              </div>
            </div>

            <!-- Academic Detail -->
            <div class="description-card">
              <h4 class="description-title">Education</h4>
              <div class="description-item" v-for="acad in academicDetails" :key="acad.id">
                <h5 class="description-item-header">{{acad.institution}}</h5>
                <div class="date-container">
                  <div class="date">
                    <div class="date-text">{{getDay(acad.start_date)}}</div>
                    <div class="date-divider">/</div>
                    <div class="date-text">{{getYear(acad.start_date)}}</div>
                  </div>
                  <div style="color: #9b9b9b">&ndash;</div>
                  <div class="date" v-if="acad.end_date != 'present'">
                    <div class="date-text">{{getDay(acad.end_date)}}</div>
                    <div class="date-divider">/</div>
                    <div class="date-text">{{getYear(acad.end_date)}}</div>
                  </div>
                  <div class="date" v-else-if="acad.end_date == 'present'">
                    <div class="date-text">{{acad.end_date}}</div>
                  </div>
                </div>
                <div class="description-item-address">{{acad.location}}</div>
              </div>
            </div>

            <!-- Project Details -->
            <div class="description-card">
              <h4 class="description-title">Personal Projects</h4>

              <div class="description-item" v-for="project in projects" :key="project.id">
                <h5 class="description-item-header">{{project.title}}</h5>
                <div class="date-container">
                  <div class="date">
                    <div class="date-text">{{getDay(project.start_date)}}</div>
                    <div class="date-divider">/</div>
                    <div class="date-text">{{getYear(project.start_date)}}</div>
                  </div>
                  <div style="color: #9b9b9b">&ndash;</div>
                  <div class="date" v-if="project.end_date != 'present'">
                    <div class="date-text">{{getDay(project.end_date)}}</div>
                    <div class="date-divider">/</div>
                    <div class="date-text">{{getYear(project.end_date)}}</div>
                  </div>
                  <div class="date" v-else-if="project.end_date == 'present'">
                    <div class="date-text">{{project.end_date}}</div>
                  </div>
                </div>
                <div class="description-item-address">{{project.description}}</div>
              </div>
            </div>
            <!-- Skills -->
            <div class="description-card">
              <h4 class="description-title">Skills</h4>
              <div
                class="display-flex align-items-center skill-header"
                v-if="skills && skills.length > 0"
              >
                <div class="skill-left">Skill</div>
                <div class="skill-right">Proficiency</div>
              </div>

              <div class="skills-container" v-for="skill in skills" :key="skill.name">
                <div class="skill-left display-flex">
                  <button class="skill-button">{{skill.name}}</button>
                </div>
                <div class="skill-right display-flex align-items-center">
                  <progress class="skill-progress-bar" id="file" max="5" :value="skill.proficiency"></progress>
                </div>
              </div>
            </div>
          </div>
          <div class="column2">
            <!-- Work Experiences -->
            <div class="description-card">
              <h4 class="description-title">Work Experience</h4>

              <div class="description-item" v-for="work in works" :key="work.id">
                <h5 class="description-item-header">{{work.company_name}}</h5>
                <div class="description-item-address">{{work.position}}</div>
                <div class="date-container">
                  <div class="date">
                    <div class="date-text">{{getDay(work.start_date)}}</div>
                    <div class="date-divider">/</div>
                    <div class="date-text">{{getYear(work.start_date)}}</div>
                  </div>
                  <div style="color: #9b9b9b">&ndash;</div>
                  <div class="date" v-if="work.end_date != 'present'">
                    <div class="date-text">{{getDay(work.end_date)}}</div>
                    <div class="date-divider">/</div>
                    <div class="date-text">{{getYear(work.end_date)}}</div>
                  </div>
                  <div class="date" v-else-if="work.end_date == 'present'">
                    <div class="date-text">{{work.end_date}}</div>
                  </div>
                </div>

                <div class="description-item-address">{{work.work_description}}</div>
              </div>
            </div>

            <!-- Interests -->
            <div class="description-card">
              <h4 class="description-title">
                Interests
                <div class="add" @click="addInterestDetails()"></div>
              </h4>
              <div class="interest-button-container">
                <div
                  class="interest-button"
                  v-for="interest in interests"
                  :key="interest.name"
                >{{interest.name}}</div>
                <div class="interest-button" v-if="addingInterestDetails">
                  <input
                    id="interest-name-input"
                    type="text"
                    class="description-input sm"
                    placeholder="Add Interest"
                    style="display: inline-block"
                    list="interest-list"
                    @keyup.enter="commitInterestDetails()"
                  />
                </div>
              </div>
            </div>

            <!-- Languages -->
            <div class="description-card">
              <h4 class="description-title">
                Languages
                <div class="add" @click="addLanguageDetails()"></div>
              </h4>
              <div
                class="display-flex align-items-center skill-header"
                v-if="languages && languages.length > 0"
              >
                <div class="skill-left">Language</div>
                <div class="skill-right">Proficiency</div>
              </div>

              <div class="skills-container" v-for="language in languages" :key="language.name">
                <div class="skill-left display-flex">
                  <button class="skill-button">{{language.name}}</button>
                </div>
                <div class="skill-right display-flex align-items-center">
                  <progress
                    class="skill-progress-bar"
                    id="file"
                    max="5"
                    :value="language.pivot.proficiency"
                  ></progress>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ProfileNavOthers from "~/components/ProfileNavOthers";
import ProfileStripOthers from "~/components/ProfileStripOthers.vue";

export default {
  layout: "profile-others",
  components: {
    ProfileStripOthers,
    ProfileNavOthers
  },
  validate({ params }) {
    return !!params.id;
  },
  async fetch({ store, params, redirect, error }) {
    const id = params.id;

    if (!store.state.other_users.list.some(el => el.id == id)) {
      await store.dispatch("other_users/getUser", { id }).catch(err => {
        if (err.response && err.response.status == 404)
          error({ statusCode: 404, message: "User not found" });
      });
    }

    if (!store.state.other_users.list.find(el => el.id == id).profile_data) {
      await store.dispatch("other_users/getProfileData", { id }).catch(err => {
        error({ statusCode: 404, message: "User data not found" });
      });
    }
  },
  computed: {
    user(){
      return this.$store.state.other_users.list.find(
        el => el.id == this.$nuxt.$route.params.id
      )
    },
    details() {
      return this.user.profile_data;
    },
    jobTitle() {
      return this.details.job_title;
    },
    academicDetails() {
      return this.details.academic_details || [];
    },
    projects() {
      return this.details.projects || [];
    },
    works() {
      return this.details.work_experiences || [];
    },
    allInterests() {
      return this.$store.state.profile.allInterests || [];
    },
    interests() {
      return this.details.interests || [];
    },
    skills() {
      return this.details.skills || [];
    },
    languages() {
      return this.details.languages || [];
    },

  },
  methods: {
    getDay(val) {
      return new Date(val).getDate();
    },
    getYear(val) {
      return new Date(val).getFullYear();
    },
  }
};
</script>

<style scoped>
.edit-profile {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-gap: 2rem;
  padding: 1rem;
}

.column1,
.column2 {
  display: flex;
  flex-direction: column;
}

.description-card {
  background: white;
  border-radius: 5px;
  padding: 1rem;
  box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.25);
  margin-bottom: 1rem;
}

.description-card .description-item {
  margin-bottom: 1rem;
}

.description-card .description-item:last-child {
  margin-bottom: 0;
}

.description-title {
  text-transform: uppercase;
  color: #0084ff;
  font-weight: normal;
  font-style: normal;
  font-size: 1.2rem;
  margin-top: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.date-container {
  display: flex;
  align-items: center;
  margin-top: 10px;
}

.date {
  flex: 1;
  margin-left: 2rem;
  display: flex;
  align-items: center;
}

.date-container .date:first-child {
  margin-left: 0;
}

.description-item-header {
  margin-bottom: 0.1rem;
  font-size: 1.1rem;
  color: #505050;
  text-transform: capitalize;
}
.description-item .date-container {
  margin-top: 0.2rem;
  margin-bottom: 0.5rem;
}

.description-item .date {
  flex: none;
  margin-right: 0.3rem;
  margin-left: 0.3rem;
}

.date .date-text {
  /* height: 30px; */
  /* width: 100%; */
  /* width: 40%; */
  border: none;
  font-size: 0.7rem;
  text-transform: uppercase;
  color: #747474;
  /* margin-left: 1rem; */
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  font-style: italic;
}

.description-item-address {
  color: #505050;
  font-size: 0.8rem;
  font-style: italic;
}

.date .date-divider {
  width: 10%;
  color: #949494;
  font-size: 1.2rem;
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
}

.date-present {
  display: flex;
  align-items: center;
  font-size: 0.8rem;
  margin-top: 10px;
  margin-bottom: 10px;
}

#job-title-input {
  width: 100%;
  height: 30px;
  padding-left: 1rem;
  padding-right: 1rem;
  font-size: 0.8rem;
  color: #747474;
  border: 1px solid #c4c4c4;
}

.interest-button-container {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  align-items: center;
}

.interest-button {
  color: #263238;
  font-size: 0.8rem;
  padding-left: 1rem;
  padding-right: 1rem;
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
  margin-right: 10px;
  border: 2px solid #9b9b9b;
  border-radius: 5px;
  margin-top: 0.5rem;
}
input:focus,
select:focus {
  outline: none;
}

.progress-button {
  cursor: progress;
  background: #eee;
  color: #9b9b9b;
}

.skills-container {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.skill-header * {
  font-style: italic;
  color: #263238;
  font-size: 0.8rem;
  margin-bottom: 1rem;
}

.skill-left {
  flex: 1;
  margin-right: 0.5rem;
  max-width: 25%;
}

.skill-right {
  flex: 3;
}

.skill-button {
  border: none;
  /* flex: 1; */
  width: 100%;
  background: #0084ff;
  color: white;
  font-size: 0.8rem;
  min-height: 25px;
  border-radius: 3px;
  text-transform: capitalize;

  overflow: hidden;

  white-space: pre-wrap;
  text-overflow: ellipsis;
}

.skill-progress-bar[value] {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  border: 1px solid #c4c4c4;
  height: 10px;
  border-radius: 5px;

  color: #0084ff;
  background: white;
  width: 90%;
}

.skill-progress-bar[value]::-moz-progress-bar {
  background: #0084ff;
  border-radius: 5px;
}

.skill-progress-bar[value]::-webkit-progress-bar {
  background: white;
  border-radius: 5px;
}

.skill-progress-bar[value]::-webkit-progress-value {
  background: #0084ff;
  border-radius: 5px;
  position: relative;
}

.skill-progress-bar[value]::-webkit-progress-value::before {
  content: "";
  width: 20px;
  height: 20px;
  border-radius: 50%;
  right: 0;
  /* top: calc(50% - 10px); */
  top: 0;
  background: #0084ff;
  position: absolute;
}

@media (max-width: 991.98px) {
  .edit-profile {
    grid-template-columns: 1fr;
    grid-gap: 1rem;
  }
}
</style>