<template>
  <div class="grow-card">
    <div class="category-box" :style="{background: course.category_color}">
      <span>{{course.category}}</span>
    </div>
    <div>
      <h4 class="title">{{course.title}}</h4>
    </div>
    <div class="footer">
      <span>Something</span>
      <button class="view-course-button">View Course</button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    course: {
      required: true,
      type: Object
    }
  }
};
</script>

<style scoped>
.grow-card {
  width: 100%;
  padding: 0.5rem;
  background: white;
  border-radius: 10px;
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: column;
}

.grow-card .category-box {
  padding: 1rem;
  border-radius: 5px;

  height: 10rem;
  background: white;

  display: flex;
  align-items: center;
  justify-content: center;
  text-transform: uppercase;

  font-weight: 600;
  color: white;
  word-wrap: break-word;
}

.grow-card .category-box span {
  font-weight: 600;
}

.grow-card .title {
  font-size: 0.8rem;
  cursor: pointer;
}

.grow-card .footer {
  flex: 1;
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  font-size: 0.7rem;

  margin-bottom: 0.5rem;
}

.grow-card .view-course-button {
  background: white;
  border-radius: 5px;
  font-size: 0.6rem;
  color: #0084ff;
  height: 20px;
  border: 1px solid #0084ff;
}

@media (max-width: 575.98px) {
  .grow-card .category-box {
    height: 12rem;
  }
}
</style>