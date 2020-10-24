<template>
  <div id="events-index">
    <div class="home-navigation-desktop">
      <home-navigation />
    </div>
    <div
      v-if="events.length == 0"
      id="no-event"
    >You haven’t posted any event. I hope you’re not shy (‘Nobody’s watching you”... Lol)</div>
    <div class="events-card-container">
      <events-card v-for="event in events" :key="event.id" :event="event" />
    </div>

    <modal ref="modal1" id="events-modal-1" @submitted="displayModal('modal2')">
      <template v-slot:header>Add Event</template>
      <div class="section">
        <span class="section-header" data-required>Category</span>
        <div class="category-button-container">
          <button
            class="category-button"
            ref="categoryButtons"
            @click.prevent="categoryButtonClicked()"
            :class="{active : i == 0}"
            v-for="(category, i) in categories"
            :key="category"
          >{{category}}</button>
        </div>
      </div>
      <div class="section">
        <span class="section-header" data-required>Event Name</span>
        <input
          required
          type="text"
          ref="eventName"
          class="section-input"
          placeholder="Enter the event name"
        />
      </div>
      <div class="section">
        <span class="section-header" data-required>Location</span>
        <input required type="text" ref="eventAddress" class="section-input" placeholder="Address" />
        <input required type="text" ref="eventCity" class="section-input" placeholder="City" />
        <div class="grid-1-1">
          <input type="text" ref="eventState" class="section-input" placeholder="State" />
          <select required name id ref="eventCountry" class="section-input">
            <option value>Country</option>
            <option
              :value="country.name"
              v-for="country in countries"
              :key="country.name"
            >{{country.name}}</option>
          </select>
        </div>
      </div>
      <template v-slot:footer>
        <div class="footer-button-container">
          <button class="footer-button cancel" @click.prevent="closeModal('modal1')">Cancel</button>
          <button class="footer-button next" type="submit">Next</button>
        </div>
      </template>
    </modal>

    <modal ref="modal2" id="events-modal-2" @submitted="createEvent()">
      <template v-slot:header>Add Event</template>

      <div class="section" style="border: none">
        <div class="section-header" data-required>Date and Time</div>
        <div class="section" style="margin: 0;" id="event-time-from-date-picker-container">
          <input ref="eventTimeFromDatePicker" required class="section-input" />
        </div>
      </div>

      <div class="section" style="border: none">
        <div class="section-header" data-required>Upload Media</div>
        <div class="section" style="margin: 0;">
          <div class="media-upload-group">
            <div>
              <label for="image-upload" class="visible-input-for-hidden">
                <div class="visible-input-for-hidden-image image-upload" v-if="!imagePreviewURL"></div>
                <img
                  class="visible-input-for-hidden-image preview-image"
                  v-else
                  :src="imagePreviewURL"
                />
              </label>
              <input
                type="file"
                accept="image/*"
                @change="updatePreviewImage()"
                ref="imageUploadInput"
                id="image-upload"
                class="hidden-input"
              />
            </div>

            <div v-if="false">
              <label for="video-upload" class="visible-input-for-hidden">
                <div class="visible-input-for-hidden-image video-upload"></div>
              </label>
              <input type="file" id="video-upload" class="hidden-input" />
            </div>
          </div>
          <div
            v-if="false"
            class="section media-info"
          >Videos uploaded should not be more than 30 seconds</div>
        </div>
      </div>

      <div class="section" style="border: none">
        <div class="section-header" data-required>Description</div>
        <textarea
          ref="eventDescription"
          required
          name
          id
          rows="10"
          class="section-input text-area"
          placeholder="Give a brief description about this event"
        ></textarea>
      </div>

      <template v-slot:footer>
        <div class="footer-button-container">
          <button class="footer-button cancel" @click.prevent="closeModal('modal2')">Cancel</button>
          <button
            class="footer-button next"
            type="submit"
            id="publish-button"
            ref="publishButton"
          >Publish</button>
        </div>
      </template>
    </modal>

    <div id="floating-buttons">
      <floating-button
        image="/png/hamburger 2.png"
        @clicked="openCreateLiveEventModal()"
      >Go Live</floating-button>
      <floating-button image="/png/like 1.png" @clicked="displayModal('modal1')">Add Event</floating-button>
    </div>

    <CreateLiveEventModal ref="createLiveEventModal" />
  </div>
</template>

<script>
import HomeNavigation from "~/components/HomeNavigation";
import EventsCard from "~/components/EventsCard";
import FloatingButton from "~/components/FloatingButton";
import Modal from "~/components/Modal";
import CreateLiveEventModal from "~/components/CreateLiveEventModal";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

export default {
  scrollToTop: true,
  components: {
    HomeNavigation,
    EventsCard,
    FloatingButton,
    Modal,
    CreateLiveEventModal
  },
  layout: "authenticated",
  async fetch(context) {
    if (!context.store.state.updates.eventsPaginationData.total)
      await context.$axios
        .get("/api/events")
        .then(response => {
          context.store.commit("updates/addAllEvents", response.data);
          // context.store.dispatch("updates/listenToPostEvents")
        })
        .catch(err => context.redirect("/updates"));
  },
  data() {
    return {
      modalRefs: ["modal1", "modal2"],
      categories: [
        "Technology",
        "Agriculture",
        "Entertainment",
        "Sport",
        "Other"
      ],
      months: [
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
      ],
      countries: [],
      imagePreviewURL: undefined
    };
  },
  mounted() {
    this.fetchCountries();
    this.setupDatePicker();
  },
  computed: {
    events() {
      return this.$store.state.updates.list.filter(
        el => el.user.id == this.$auth.user.id
      );
    },
    currentYear() {
      return new Date().getFullYear();
    }
    // imagePreviewURL(){
    //   let imageUploadInput = this.$refs.imageUploadInput
    //   try{
    //     return URL.createObjectURL(imageUploadInput.files[0])

    //   }catch(e){
    //     return undefined
    //   }

    // }
  },
  methods: {
    $(id) {
      return document.getElementById(id);
    },
    fetchCountries() {
      this.$axios
        .get("/api/countries/get")
        .then(response => response.data)
        .then(data => {
          this.countries = data;
        })
        .catch(err => {
          throw err;
        });
    },
    displayModal(ref) {
      for (let x of this.modalRefs)
        if (this.$refs[x]) this.$refs[x].closeModal();
      this.$refs[ref].openModal();
    },
    closeModal(ref) {
      this.$refs[ref].closeModal();
    },
    categoryButtonClicked() {
      const buttons = this.$refs.categoryButtons;

      for (let button of buttons) button.classList.remove("active");

      event.target.classList.add("active");
    },
    setupDatePicker() {
      let dateElement = this.$refs.eventTimeFromDatePicker;
      flatpickr(dateElement, {
        enableTime: true,
        dateFormat: "Y-m-d H:i"
        // altFormat: "F j, Y at H:i",
        // disableMobile: true
        // inline: true,
        // altInput: true,
      });
    },
    createEvent() {
      let refs = this.$refs;
      let category = undefined;

      if (!refs.publishButton.classList.contains("publish-button-loading"))
        refs.publishButton.classList.add("publish-button-loading");

      if (!refs.publishButton.hasAttribute("disabled"))
        refs.publishButton.setAttribute("disabled", true);

      for (let button of refs.categoryButtons)
        if (button.classList.contains("active")) {
          category = button.textContent;
          break;
        }

      let data = {
        content: refs.eventDescription.value,
        title: refs.eventName.value,
        location:
          refs.eventAddress.value +
          ", " +
          refs.eventCity.value +
          ", " +
          refs.eventState.value +
          ", " +
          refs.eventCountry.value +
          ".",
        time: this.$refs.eventTimeFromDatePicker.value,
        category: category
      };

      let formData = new FormData();

      for (let key in data) {
        formData.append(key, data[key]);
      }

      let imageUploadInput = this.$refs.imageUploadInput;

      if (imageUploadInput.files.length > 0)
        formData.append("img", imageUploadInput.files[0]);

      let attempt = this.$store.dispatch("updates/create", formData);
      let self = this;

      attempt.finally(() => {
        let list = [
          "eventDescription",
          "eventName",
          "eventAddress",
          "eventCity",
          "eventState",
          "eventCountry",
          "eventYear",
          "eventMonth",
          "eventDay",
          "eventHour",
          "eventMinute"
        ];

        for (let l of list) if (refs[l]) refs[l].value = "";
        refs.categoryButtons[0].click();
        refs.publishButton.removeAttribute("disabled");
        refs.publishButton.classList.remove("publish-button-loading");

        self.closeModal("modal2");
      });
    },
    updatePreviewImage() {
      try {
        this.imagePreviewURL = URL.createObjectURL(
          this.$refs.imageUploadInput.files[0]
        );
      } catch (e) {
        this.imagePreviewURL = undefined;
      }
    },
    openCreateLiveEventModal(){
      this.$refs.createLiveEventModal.openModal()
    }
  }
  // watch:{
  //   "$refs.imageUploadInput" : function(oldVal, newVal) {
  //     console.log([oldVal.files.length, length.files.length])
  //   }
  // }
};
</script>

<style scoped>
#no-event {
  width: 60%;
  font-size: 2rem;
  line-height: 2.5rem;
  color: rgba(72, 72, 72, 0.26);
}

.dark-theme #no-event {
  color: rgba(206, 206, 206, 0.26);
}

.events-card-container {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
}

#floating-buttons {
  position: fixed;
  right: 3rem;
  bottom: 2rem;
  display: grid;
  grid-template-columns: 1fr;
  row-gap: 10px;
  z-index: 100;
}

.section {
  margin: 1rem;
  margin-left: 0;
  margin-right: 0;
  padding: 1rem;
  border: 1px solid #e6e6e6;
  border-radius: 5px;
}
.section-header {
  margin-bottom: 1rem;
  display: block;
}

.section-header[data-required]::after {
  content: " *";
  color: red;
  /* margin-bottom: 1rem; */
}

.section-input {
  width: 100%;
  border: 1.2px solid #dddddd;
  height: 37px;
  border-radius: 2px;
  font-size: 0.8rem;
  color: #464646;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
  text-indent: 10px;
  margin-bottom: 1rem;
  border-radius: 2px;
}

.section-input:focus {
  border: 1.2px solid #aaa;
  outline: none;
}

.section-input.text-area {
  height: 100%;
  padding: 1rem;
  border-radius: 5px;
}

.flatpickr-input {
  -webkit-appearance: none;
}

.category-button {
  height: 30px;
  margin: 0.5rem;
  margin-left: 0;
  margin-right: 0.5rem;
  padding-left: 1rem;
  padding-right: 1rem;
  color: #9b9b9b;
  background: #fff;
  border-radius: 15px;
  border: #9b9b9b 1px solid;
}

.category-button.active {
  color: white;
  background: #0084ff;
  border: none;
}

.category-button:active,
.category-button:focus {
  outline: none;
}

.category-button:focus {
  border-color: #464646;
}

.category-button.active:focus {
  border: 1.5px #0062dd solid;
}

.category-button:last-child {
  margin-right: 0;
}

.footer-button {
  height: 37px;
  border-radius: 5px;
  padding-left: 1rem;
  padding-right: 1rem;
  border: none;
  font-size: 1.1rem;
  cursor: pointer;
}
.footer-button-container {
  padding-right: 1rem;
  padding-left: 1rem;
}

.footer-button.cancel {
  background: transparent;
  color: black;
}

.footer-button.next {
  background: #0084ff;
  color: white;
}

.hidden-input {
  display: none;
}

.media-upload-group {
  display: flex;
  justify-content: space-around;
}

.visible-input-for-hidden {
  cursor: pointer;
  height: 150px;
  width: 150px;
  border: 1.5px solid #ddd;
  border-radius: 5px;
  position: relative;

  display: flex;
  justify-content: center;
  align-items: center;
}

.visible-input-for-hidden::before {
  content: "";
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  bottom: 0.5rem;
  left: 0.5rem;
  border: 1.3px #ddd dashed;
  border-radius: 5px;
  /* Horizontal */
  /* background-image: linear-gradient(to right, black 33%, white 0%);
        background-position: bottom;
        background-size: 3px 1px;
        background-repeat: repeat-x;

        /* Vertical */
  /* background-image: linear-gradient(black 33%, rgba(255, 255, 255, 0) 0%) repeat-y right 1px 3px; */
  /* background-position: right;
        background-size: 1px 3px;
        background-repeat: repeat-y; */
}

.visible-input-for-hidden-image {
  height: 50px;
  width: 50px;
  background-repeat: no-repeat;
  background-position: center center;
  background-size: contain;
}

.visible-input-for-hidden-image.video-upload {
  background-image: url("/png/triangle right.png");
}

.visible-input-for-hidden-image.image-upload {
  background-image: url("/png/photo camera.png");
}

.visible-input-for-hidden-image.preview-image {
  height: unset;
  width: unset;
  max-width: calc(100% - 1rem);
  max-height: calc(100% - 1rem);
  margin: 0.5rem;
}

.media-info {
  color: #ffce00;
  border-color: #ffce00;
  font-size: 0.8rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

#publish-button.publish-button-loading {
  cursor: progress;
  background: #95b4d0;
}

@media (max-width: 1199.98px) {
  .events-card-container {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
  }
}

@media (max-width: 991.98px) {
}
@media (max-width: 767.98px) {
  .events-card-container {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 575.98px) {
  #floating-buttons {
    bottom: calc(2rem + 50px);
    right: 1rem;
  }
}
</style>
