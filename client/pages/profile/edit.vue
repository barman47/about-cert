<template>
  <div class="edit-profile">
    <div class="column1">
      <div class="description-card">
        <h4 class="description-title">
          Job Title
          <div class="edit" @click="editJobTitle()"></div>
        </h4>
        <div class="description-item" v-if="jobTitle">
          <h5 class="description-item-header">{{jobTitle}}</h5>
        </div>
        <input
          ref="jobTitle"
          v-if="edittingJobTitle"
          list="job-titles"
          class="description-input"
          placeholder="job title"
          @keyup.enter="commitJobTitle()"
        />
      </div>
      <!-- Academic Detail | Education -->
      <div class="description-card">
        <h4 class="description-title">
          Education
          <div @click="addEducationDetails()" class="add"></div>
        </h4>
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
        <div ref="educationForm" v-show="addingEducationDetails">
          <input
            type="text"
            id="education-school-name-input"
            class="description-input"
            placeholder="School Name"
          />
          <div class="date-container">
            <div class="date">
              <input
                type="number"
                ref="educationStartDate"
                id="education-start-date"
                class="date-input"
                placeholder="Start Date"
              />
            </div>
            <div class="date">
              <input
                type="number"
                ref="educationEndDate"
                id="education-end-date"
                class="date-input"
                placeholder="End Date"
              />
            </div>
          </div>

          <div class="date-present">
            <input id="education-radio-button" type="checkbox" /> Present
          </div>

          <input
            type="text"
            class="description-input sm"
            id="education-location-input"
            placeholder="city, country"
          />
          <small class="error-message-display">{{educationErrorMessage}}</small>
          <div class="save-button-container">
            <button class="cancel-button-sm" @click="abortAddEducationDetails()">Cancel</button>
            <button
              ref="educationSaveButton"
              class="save-button-sm"
              @click="commitEducationDetails()"
            >save</button>
          </div>
        </div>
      </div>

      <!-- Project Details -->
      <div class="description-card">
        <h4 class="description-title">
          Personal Projects
          <div @click="addProjectDetails()" class="add"></div>
        </h4>

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
        <div ref="projectForm" v-show="addingProjectDetails">
          <input
            type="text"
            class="description-input"
            id="project-title-input"
            placeholder="Project Title"
          />
          <div class="date-container">
            <div class="date">
              <input
                type="number"
                ref="projectStartDate"
                id="project-start-date"
                class="date-input"
                placeholder="Start date"
              />
            </div>

            <div class="date">
              <input
                type="number"
                ref="projectEndDate"
                id="project-end-date"
                class="date-input"
                placeholder="End Date"
              />
            </div>
          </div>

          <div class="date-present">
            <input id="project-radio-button" type="checkbox" /> Present
          </div>

          <input
            type="text"
            id="project-description-input"
            class="description-input sm"
            placeholder="Achivement Description"
          />

          <small class="error-message-display">{{projectErrorMessage}}</small>
          <div class="save-button-container">
            <button class="cancel-button-sm" @click="abortAddProjectDetails()">Cancel</button>
            <button
              ref="projectSaveButton"
              class="save-button-sm"
              @click="commitProjectDetails()"
            >save</button>
          </div>
        </div>
      </div>

      <!-- Certificate Details -->
      <div class="description-card">
        <h4 class="description-title">
          Certifications
          <div @click="addCertificateDetails()" class="add"></div>
        </h4>

        <div class="description-item" v-for="certificate in certificates" :key="certificate.id">
          <h5 class="description-item-header">{{certificate.title}}</h5>
          <div class="date-container">
            <div class="date">
              <div class="date-text">{{getDay(certificate.date)}}</div>
              <div class="date-divider">/</div>
              <div class="date-text">{{getYear(certificate.date)}}</div>
            </div>
          </div>
          <div
            class="description-item-address"
            v-if="certificate.description"
          >{{certificate.description}}</div>
          <div class="display-flex justify-content-end align-items-center">
            <button
              class="certificate-show-file-button"
              @click.prevent="openCertificateModal(certificate.id)"
            >View document</button>
          </div>
          <modal
            :ref="'certificate-modal-' + certificate.id"
            :id="'certificate-modal-' + certificate.id"
          >
            <template v-slot:header>{{certificate.title}}</template>
            <div>
              <img
                class="certificate-modal-image"
                :id="'certificate-modal-image-' + certificate.id"
              />
            </div>
          </modal>
        </div>
        <div ref="certificateForm" v-show="addingCertificateDetails">
          <input
            type="text"
            class="description-input"
            id="certificate-title-input"
            placeholder="Certificate Title"
          />
          <div class="date-container">
            <div class="date">
              <input
                type="number"
                ref="certificationDate"
                id="certificate-date"
                class="date-input"
                placeholder="Date Awarded"
              />
            </div>
          </div>
          <input
            type="text"
            id="certificate-description-input"
            class="description-input sm"
            placeholder="Certification Description"
          />
          <input
            accept="image/*"
            type="file"
            class="description-input"
            style="height: auto; margin-top: 0.5rem;  border-bottom: none; font-size: 0.9rem;"
            name
            id="certificate-file-input"
          />
          <small
            style="margin-bottom: 0.5rem; font-size: 0.7rem; color: #ff7800;; "
          >Only jpeg, jpg and png files are allowed</small>
          <div>
            <small class="error-message-display">{{certificateErrorMessage}}</small>
          </div>
          <div class="save-button-container">
            <button class="cancel-button-sm" @click="abortAddCertificateDetails()">Cancel</button>
            <button
              ref="certificateSaveButton"
              class="save-button-sm"
              @click="commitCertificateDetails()"
            >save</button>
          </div>
        </div>
      </div>
    </div>
    <div class="column2">
      <!-- Skills -->
      <div class="description-card">
        <h4 class="description-title">
          Skills
          <div class="add" @click="addSkillDetails()"></div>
        </h4>
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
        <div v-if="addingSkillDetails">
          <input
            id="skill-name-input"
            type="text"
            class="description-input"
            placeholder="Enter your skill"
          />
          <select name id="skill-proficiency-input" class="description-input">
            <option value>--proficiency--</option>
            <option value="1">Fundamental Awareness (basic knowledge)</option>
            <option value="2">Novice (limited experience)</option>
            <option value="3">Intermediate (practical application)</option>
            <option value="4">Advanced (applied theory)</option>
            <option value="5">Expert (recognized authority)</option>
          </select>
          <div style="margin-top: 1rem;">
            <div class="save-button-container">
              <small class="error-message-display">{{skillErrorMessage}}</small>
              <br />
              <button class="cancel-button-sm" @click="abortAddSkillDetails()">Cancel</button>
              <button
                ref="skillSaveButton"
                class="save-button-sm"
                @click="commitSkillDetails()"
              >save</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Work Experiences -->
      <div class="description-card">
        <h4 class="description-title">
          Work Experience
          <div @click="addWorkDetails()" class="add"></div>
        </h4>

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
        <div ref="workForm" v-show="addingWorkDetails">
          <input
            type="text"
            class="description-input"
            id="work-company-name-input"
            placeholder="Company Name"
          />
          <div class="date-container">
            <div class="date">
              <input
                type="number"
                ref="workStartDate"
                id="work-start-date"
                class="date-input"
                placeholder="Start Date"
              />
            </div>

            <div class="date">
              <input
                type="number"
                ref="workEndDate"
                id="work-end-date"
                class="date-input"
                placeholder="End Date"
              />
            </div>
          </div>

          <div class="date-present">
            <input id="work-radio-button" type="checkbox" /> Present
          </div>

          <input
            type="text"
            id="work-position-input"
            class="description-input sm"
            placeholder="Position Held"
            list="job-titles"
          />
          <input
            type="text"
            id="work-achievement-input"
            class="description-input sm"
            placeholder="Achivements"
          />
          <input
            type="text"
            id="work-description-input"
            class="description-input sm"
            placeholder="Company Description"
          />

          <small class="error-message-display">{{workErrorMessage}}</small>
          <div class="save-button-container">
            <button class="cancel-button-sm" @click="abortAddWorkDetails()">Cancel</button>
            <button ref="workSaveButton" class="save-button-sm" @click="commitWorkDetails()">save</button>
          </div>
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
          <div v-if="addingInterestDetails" style="margin-top: 1rem;">
            <div class="save-button-container">
              <small class="error-message-display">{{interestErrorMessage}}</small>
              <br />
              <button class="cancel-button-sm" @click="abortAddInterestDetails()">Cancel</button>
            </div>
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
        <div v-if="addingLanguageDetails">
          <select id="language-name-input" type="text" class="description-input">
            <option value>--select language--</option>
            <option :value="language" v-for="language in allLanguages" :key="language">{{language}}</option>
          </select>
          <select name id="language-proficiency-input" class="description-input">
            <option value>--proficiency--</option>
            <option value="1">Elementary Proficiency</option>
            <option value="2">Limited Working Proficiency</option>
            <option value="3">Professional Working Proficiency</option>
            <option value="4">Full Professional Proficiency</option>
            <option value="5">Native / Bilingual Proficiency</option>
          </select>
          <div style="margin-top: 1rem;">
            <div class="save-button-container">
              <small class="error-message-display">{{languageErrorMessage}}</small>
              <br />
              <button class="cancel-button-sm" @click="abortAddLanguageDetails()">Cancel</button>
              <button
                ref="languageSaveButton"
                class="save-button-sm"
                @click="commitLanguageDetails()"
              >save</button>
            </div>
          </div>
        </div>
      </div>

      <!-- <div class="save-button-container">
                <button class="save-button">Save</button>
      </div>-->

      <!-- Data Lists -->
      <datalist id="job-titles">
        <option v-for="(job, i) in allJobtitles" :key="i" :value="job" />
      </datalist>

      <datalist id="interest-list">
        <option v-for="(interest, i) in allInterests" :key="i" :value="interest" />
      </datalist>
    </div>
  </div>
</template>

<script>
import ProfileNav from "~/components/ProfileNav";
import Modal from "~/components/Modal";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

export default {
  async fetch({ store, $axios }) {
    if (store.state.profile.allInterests.length <= 0) {
      $axios.get("/api/interests/get-all").then(response => {
        store.commit("profile/getAllInterests", response.data);
      });
    }

    if (store.state.profile.allLanguages.length <= 0) {
      $axios.get("/api/languages/get-all").then(response => {
        store.commit("profile/getAllLanguages", response.data);
      });
    }

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
  components: {
    ProfileNav,
    Modal
  },
  layout: "profile",

  data() {
    return {
      addingEducationDetails: false,
      addingProjectDetails: false,
      addingCertificateDetails: false,
      addingWorkDetails: false,
      addingInterestDetails: false,
      addingSkillDetails: false,
      addingLanguageDetails: false,
      edittingJobTitle: false,
      educationErrorMessage: "",
      projectErrorMessage: "",
      certificateErrorMessage: "",
      workErrorMessage: "",
      interestErrorMessage: "",
      languageErrorMessage: ""
    };
  },
  computed: {
    details() {
      return this.$store.state.profile.details;
    },
    jobTitle() {
      return this.details.job_title;
    },
    academicDetails() {
      return this.details.academic_details;
    },
    projects() {
      return this.details.projects;
    },
    certificates() {
      return this.details.certificates;
    },
    works() {
      return this.details.work_experiences;
    },
    allInterests() {
      return this.$store.state.profile.allInterests;
    },
    interests() {
      return this.details.interests;
    },
    skills() {
      return this.details.skills;
    },
    allLanguages() {
      return this.$store.state.profile.allLanguages;
    },
    allJobtitles() {
      return this.$store.state.profile.allJobtitles;
    },
    languages() {
      return this.details.languages;
    }
  },
  methods: {
    setupDatePicker(dateElements) {
      for (let dateElement of dateElements) {
        flatpickr(dateElement, {
          enableTime: false,
          dateFormat: "Y-m-d",
          // altFormat: "F j, Y at H:i",
          disableMobile: true
          // inline: true,
          // altInput: true,
        });
      }
    },
    $(id) {
      return document.getElementById(id);
    },
    getDay(val) {
      return new Date(val).getDate();
    },
    getYear(val) {
      return new Date(val).getFullYear();
    },

    // Job Title
    editJobTitle() {
      this.edittingJobTitle = true;
    },
    commitJobTitle() {
      this.$refs.jobTitle.setAttribute("disabled", true);
      let attempt = this.$store.dispatch("profile/editJobtitle", {
        job_title: this.$refs.jobTitle.value
      });

      attempt.finally(() => {
        this.$refs.jobTitle.removeAttribute("disabled");
        this.edittingJobTitle = false;
      });
    },

    // Education
    addEducationDetails() {
      this.addingEducationDetails = true;
      let refs = this.$refs;
      const dateElements = [refs.educationStartDate, refs.educationEndDate];
      this.setupDatePicker(dateElements);
    },
    abortAddEducationDetails() {
      this.addingEducationDetails = false;
      let refs = this.$refs;
      refs.educationStartDate.value = "";
      refs.educationEndDate.value = "";
      this.$("education-school-name-input").value = "";
      this.$("education-location-input").value = "";
      this.$("education-radio-button").checked = false;
    },
    commitEducationDetails() {
      this.educationErrorMessage = "";

      const institution = this.$("education-school-name-input").value;

      if (!institution)
        return (this.educationErrorMessage =
          "Error: School Name Field Required");

      const start_date = this.$refs.educationStartDate.value;

      if (!start_date)
        return (this.educationErrorMessage =
          "Error: Start Date Field Required");

      const end_date = this.$("education-radio-button").checked
        ? "present"
        : this.$refs.educationStartDate.value;

      if (!end_date) {
        return (this.educationErrorMessage =
          "Error: End Date Field Required or present checkbox");
      }

      const location = this.$("education-location-input").value;

      if (!this.$("education-location-input").value)
        return (this.educationErrorMessage = "Error: Address Field Required");

      const data = {
        institution: institution,
        start_date: start_date,
        end_date: end_date,
        location: location
      };

      this.$refs.educationSaveButton.setAttribute("disabled", "true");
      this.$refs.educationSaveButton.classList.add("progress-button");

      let attempt = this.$store.dispatch("profile/createEducationDetail", data);
      self = this;
      attempt
        .then(() => {
          self.educationErrorMessage = "";
          self.abortAddEducationDetails();
        })
        .catch(err => {
          self.educationErrorMessage = err.message;
        })
        .finally(() => {
          self.$refs.educationSaveButton.removeAttribute("disabled");
          self.$refs.educationSaveButton.classList.remove("progress-button");
        });
    },

    // Project
    addProjectDetails() {
      this.addingProjectDetails = true;
      let refs = this.$refs;
      const dateElements = [refs.projectStartDate, refs.projectEndDate];
      this.setupDatePicker(dateElements);
    },
    abortAddProjectDetails() {
      this.addingProjectDetails = false;
      this.$refs.projectStartDate.value = "";
      this.$refs.projectEndDate.value = "";
      this.$("project-title-input").value = "";
      this.$("project-description-input").value = "";
      this.$("project-radio-button").checked = false;
    },
    commitProjectDetails() {
      this.projectErrorMessage = "";

      const title = this.$("project-title-input").value;

      if (!title)
        return (this.projectErrorMessage = "Error: Title Field Required");

      const start_date = this.$refs.projectStartDate.value;

      if (!start_date)
        return (this.projectErrorMessage = "Error: Start Date Field Required");

      const end_date = this.$("project-radio-button").checked
        ? "present"
        : this.$refs.projectEndDate.value;

      if (!end_date) {
        return (this.projectErrorMessage = "Error: End Date Field Required");
      }

      const description = this.$("project-description-input").value;

      if (!description)
        return (this.projectErrorMessage = "Error: Description Field Required");

      const data = {
        title: title,
        start_date: start_date,
        end_date: end_date,
        description: description
      };

      this.$refs.projectSaveButton.setAttribute("disabled", "true");
      this.$refs.projectSaveButton.classList.add("progress-button");

      let attempt = this.$store.dispatch("profile/createProject", data);
      self = this;
      attempt
        .then(() => {
          self.projectErrorMessage = "";
          self.abortAddProjectDetails();
        })
        .catch(err => {
          self.projectErrorMessage = err.message;
        })
        .finally(() => {
          self.$refs.projectSaveButton.removeAttribute("disabled");
          self.$refs.projectSaveButton.classList.remove("progress-button");
        });
    },

    // Certificate
    addCertificateDetails() {
      this.addingCertificateDetails = true;
      this.setupDatePicker([this.$refs.certificationDate]);
    },
    abortAddCertificateDetails() {
      this.addingCertificateDetails = false;
      this.$refs.certificationDate.value = "";
      this.$("certificate-title-input").value = "";
      this.$("certificate-description-input").value = "";
      this.$("certificate-file-input").value = "";
    },
    commitCertificateDetails() {
      this.certificateErrorMessage = "";

      const title = this.$("certificate-title-input").value;

      if (!title)
        return (this.certificateErrorMessage = "Error: Title Field Required");

      const date = this.$refs.certificationDate.value;

      if (!date)
        return (this.certificateErrorMessage = "Error: Date Field Required");

      const description = this.$("certificate-description-input").value;
      const files = this.$("certificate-file-input").files;

      if (files.length < 1) {
        return (this.certificateErrorMessage =
          "Error: The file field is required");
      }

      const data = {
        title: title,
        date: date,
        description: description
      };

      let formData = new FormData();

      for (let key in data) {
        formData.append(key, data[key]);
      }

      formData.append("file", files[0]);

      this.$refs.certificateSaveButton.setAttribute("disabled", "true");
      this.$refs.certificateSaveButton.classList.add("progress-button");

      let attempt = this.$store.dispatch(
        "profile/createCertificateDetail",
        formData
      );
      self = this;
      attempt
        .then(() => {
          self.certificateErrorMessage = "";
          self.abortAddCertificateDetails();
        })
        .catch(err => {
          self.certificateErrorMessage = err.message;
        })
        .finally(() => {
          self.$refs.certificateSaveButton.removeAttribute("disabled");
          self.$refs.certificateSaveButton.classList.remove("progress-button");
        });
    },
    openCertificateModal(id) {
      let self = this;
      let attempt = this.$store.dispatch("profile/fetchCertificateDocument", {
        id: id
      });

      attempt
        .then(response => {
          let debid = "certificate-modal-image-" + id;
          let img = self.$("certificate-modal-image-" + id);

          let reader = new FileReader();

          reader.onload = event => {
            img.src = event.target.result;
          };

          reader.onerror = event => {
            console.log(
              "File could not be read! Code " + event.target.error.code
            );
          };
          reader.readAsDataURL(response);
        })
        .catch(err => {
          console.log(err);
        });

      this.$refs["certificate-modal-" + id][0].openModal();
    },

    // Work Experiences
    addWorkDetails() {
      this.addingWorkDetails = true;
      this.setupDatePicker([this.$refs.workStartDate, this.$refs.workEndDate]);
    },
    abortAddWorkDetails() {
      this.addingWorkDetails = false;
      this.$refs.workStartDate.value = "";
      this.$refs.workEndDate.value = "";
      this.$("work-company-name-input").value = "";
      this.$("work-radio-button").checked = false;
      this.$("work-description-input").value = ""
      this.$("work-position-input").value = ""
      this.$("work-achievement-input").value = ""
    },
    commitWorkDetails() {
      this.workErrorMessage = "";

      const company_name = this.$("work-company-name-input").value;

      if (!company_name)
        return (this.workErrorMessage = "Error: Company Name Field Required");

      const start_date = this.$refs.workStartDate.value;

      if (!start_date)
        return (this.workErrorMessage = "Error: Start Date Field Required");

      const end_date = this.$("work-radio-button").checked
        ? "present"
        : this.$refs.workEndDate.value;

      if (!end_date)
        return (this.workErrorMessage = "Error: Start Date Field Required");

      const description = this.$("work-description-input").value;

      if (!description)
        return (this.workErrorMessage = "Error: Description Field Required");

      const position = this.$("work-position-input").value;

      if (!position)
        return (this.workErrorMessage = "Error: Position Held Field Required");

      const achievements = this.$("work-achievement-input").value;

      const data = {
        company_name: company_name,
        start_date: start_date,
        end_date: end_date,
        description: description,
        achievements: achievements,
        position: position
      };

      this.$refs.workSaveButton.setAttribute("disabled", "true");
      this.$refs.workSaveButton.classList.add("progress-button");

      let attempt = this.$store.dispatch("profile/createWorkExperience", data);
      self = this;
      attempt
        .then(() => {
          self.workErrorMessage = "";
          self.abortAddWorkDetails();
        })
        .catch(err => {
          self.workErrorMessage = err.message;
        })
        .finally(() => {
          self.$refs.workSaveButton.removeAttribute("disabled");
          self.$refs.workSaveButton.classList.remove("progress-button");
        });
    },

    // Interests
    addInterestDetails() {
      this.addingInterestDetails = true;
    },
    abortAddInterestDetails() {
      this.addingInterestDetails = false;
    },
    commitInterestDetails() {
      this.interestErrorMessage = "";

      const name = this.$("interest-name-input").value;

      if (!name)
        return (this.interestErrorMessage =
          "Error: Interest Name Field Required");

      this.$("interest-name-input").value = "";

      const data = {
        name: name
      };

      this.$("interest-name-input").setAttribute("disabled", "true");
      this.$("interest-name-input").classList.add("progress-button");

      let attempt = this.$store.dispatch("profile/createInterest", data);
      self = this;
      attempt
        .then(() => {
          self.interestErrorMessage = "";
          self.abortAddInterestDetails();
        })
        .catch(err => {
          self.interestErrorMessage = err.message;
        })
        .finally(() => {
          self.$("interest-name-input").removeAttribute("disabled");
          self.$("interest-name-input").classList.remove("progress-button");
        });
    },

    // Skills
    addSkillDetails() {
      this.addingSkillDetails = true;
    },
    abortAddSkillDetails() {
      this.addingSkillDetails = false;
    },
    commitSkillDetails() {
      this.skillErrorMessage = "";

      const name = this.$("skill-name-input").value;

      if (!name)
        return (this.skillErrorMessage = "Error: Skill Name Field Required");

      const proficiency = this.$("skill-proficiency-input").value;

      if (!proficiency)
        return (this.skillErrorMessage = "Error: Proficiency Field Required");

      this.$("skill-name-input").value = "";
      this.$("skill-proficiency-input").value = "";

      const data = {
        name: name,
        proficiency: proficiency
      };

      this.$refs.skillSaveButton.setAttribute("disabled", "true");
      this.$refs.skillSaveButton.classList.add("progress-button");

      let attempt = this.$store.dispatch("profile/createSkill", data);
      self = this;
      attempt
        .then(() => {
          self.skillErrorMessage = "";
          self.abortAddSkillDetails();
        })
        .catch(err => {
          self.skillErrorMessage = err.message;
        })
        .finally(() => {
          self.$refs.skillSaveButton.removeAttribute("disabled");
          self.$refs.skillSaveButton.classList.remove("progress-button");
        });
    },

    // Language
    addLanguageDetails() {
      this.addingLanguageDetails = true;
    },
    abortAddLanguageDetails() {
      this.addingLanguageDetails = false;
    },
    commitLanguageDetails() {
      this.languageErrorMessage = "";

      const name = this.$("language-name-input").value;

      if (!name)
        return (this.languageErrorMessage =
          "Error: Language Name Field Required");

      const proficiency = this.$("language-proficiency-input").value;

      if (!proficiency)
        return (this.languageErrorMessage =
          "Error: Proficiency Field Required");

      this.$("language-name-input").value = "";
      this.$("language-proficiency-input").value = "";

      const data = {
        name: name,
        proficiency: proficiency
      };

      this.$refs.languageSaveButton.setAttribute("disabled", "true");
      this.$refs.languageSaveButton.classList.add("progress-button");

      let attempt = this.$store.dispatch("profile/createLanguage", data);
      self = this;
      attempt
        .then(() => {
          self.languageErrorMessage = "";
          self.abortAddLanguageDetails();
        })
        .catch(err => {
          self.languageErrorMessage = err.message;
        })
        .finally(() => {
          self.$refs.languageSaveButton.removeAttribute("disabled");
          self.$refs.languageSaveButton.classList.remove("progress-button");
        });
    }
  },

  mounted() {}
};
</script>

<style scoped>
input::placeholder {
  color: #999;
}
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

.description-title .add {
  background-image: url("/png/add.png");
  height: 1.5rem;
  width: 1.5rem;
  background-size: contain;
  background-position: center center;
  background-repeat: no-repeat;
  cursor: pointer;
}

.description-title .edit {
  background-image: url("/png/edit-details.png");
  height: 1.5rem;
  width: 1.5rem;
  background-size: contain;
  background-position: center center;
  background-repeat: no-repeat;
  cursor: pointer;
}

.description-input {
  height: 40px;
  width: 100%;
  border: none;
  border-bottom: 1px solid #c4c4c4;
  font-size: 1rem;
  /* text-transform: capitalize; */
  color: rgba(0, 0, 0, 0.9);
}

.description-input.sm {
  height: 30px;
  border-bottom-style: dashed;
  font-size: 0.8rem;
  text-transform: inherit;
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

.date .date-input {
  height: 30px;
  width: 100%;
  /* width: 40%; */
  border: none;
  border-bottom: 1px dashed #c4c4c4;
  font-size: 0.8rem;
  text-transform: capitalize;
  color: rgba(0, 0, 0, 0.9);
  /* margin-left: 1rem; */
  text-align: center;
}

#certificate-date {
  text-align: unset;
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

.date .date-input:focus {
  outline: none;
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

#job-title-input:focus {
  outline: none;
}

input:focus,
select:focus {
  outline: none;
}

/* assssssssssssssssssssssssssssssssssssss */

.save-button-container {
  display: flex;
  justify-content: flex-end;
}
.save-button {
  border: none;
  background: #0084ff;
  color: white;
  padding: 0.7rem 1.5rem 0.7rem 1.5rem;
  cursor: pointer;
}

.save-button-sm {
  border: none;
  background: #0084ff;
  color: white;
  margin: 0.5rem;
  font-size: 0.9rem;
  border-radius: 3px;
  cursor: pointer;
}

.cancel-button-sm {
  border: none;
  background: #eee;
  color: #9b9b9b;
  margin: 0.5rem;
  font-size: 0.9rem;
  border-radius: 3px;
  cursor: pointer;
}

.progress-button {
  cursor: progress;
  background: #eee;
  color: #9b9b9b;
}

.error-message-display {
  color: red;
  font-size: 0.7rem;
}

.certificate-show-file-button {
  background: #2c3a64d6;
  cursor: pointer;
  border: none;
  color: #e2e4f2;
  font-size: 0.7rem;
  padding: 0.1rem 0.5rem 0.1rem 0.5rem;
  border-radius: 2px;
}

.certificate-modal-image {
  max-height: calc(var(--viewport-height, 100vh) - 50px);
  max-width: calc(100% - 1rem);
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
@media (max-width: 767.98px) {
  
}

@media (max-width: 575.98px) {
  .edit-profile {
    padding: 0;
  }
}
</style>
