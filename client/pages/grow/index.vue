<template>
  <div style="margin-bottom: 30px">
    <div class="home-navigation-desktop">
      <home-navigation />
    </div>
    <div style="margin-bottom: 1rem;">
      <div class="category-selector-button">
        <span>Category</span> &nbsp; &nbsp;
        <img src="/png/Group 125.png" alt />
      </div>
    </div>

    <div class="grow-card-container">
      <grow-card v-for="course in [...courses, ...courses]" :key="course.id" :course="course"></grow-card>
    </div>
  </div>
</template>

<script>
import HomeNavigation from "~/components/HomeNavigation";
import GrowCard from "~/components/GrowCard";
export default {
  scrollToTop: true,
  layout: "authenticated",
  components: {
    HomeNavigation,
    GrowCard
  },
  data() {
    return {
      courses: [
        {
          id: "1",
          category: "Programming",
          category_color:
            "linear-gradient(to bottom,#eb6dff 0%,rgba(176, 50, 253, 0.55) 100%)",
          title: "Andela Grow With Google"
        },
        {
          id: "2",
          category: "Business",
          category_color:
            "linear-gradient(to bottom,#fff96d 0%,rgba(253, 123, 50, 0.55) 100%)",
          title: "Financing Options for small Businesses"
        },
        {
          id: "3",
          category: "Other",
          category_color:
            "linear-gradient(to bottom,#6dfff6 0%,rgba(50, 106, 253, 0.55) 100%)",
          title: "Habits of Great Relationships"
        },
        {
          id: "4",
          category: "Business",
          category_color:
            "linear-gradient(to bottom,#fff96d 0%,rgba(253, 123, 50, 0.55) 100%)",
          title: "Language Hacking podcast LIVE"
        }
      ]
    };
  }
};
</script>

<style scoped>
.category-selector-button {
  background: white;
  display: inline-flex;
  align-items: center;
  padding: 0.5rem 0.8rem 0.5rem 0.8rem;

  justify-content: space-between;
  border-radius: 5px;
  color: #9b9b9b;
  font-size: 0.8rem;
}

.grow-card-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  column-gap: 1rem;
  row-gap: 2rem;
}

/* // Large devices (desktops, less than 1200px) */
@media (max-width: 1199.98px) {
}

/* // Medium devices (tablets, less than 992px) */
@media (max-width: 991.98px) {
  .grow-card-container {
    grid-template-columns: repeat(3, 1fr);
  }
}

/* // Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
  .grow-card-container {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* // Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
  .grow-card-container {
    grid-template-columns: 1fr;
  }
}
</style>