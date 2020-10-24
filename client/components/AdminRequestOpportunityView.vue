<template>
  <div class="admin-request-profile-view">
      <h4 class="header">Reported Opportunity</h4>
      <div class="admin-manage-opportunity-container">
        <ManageOpportunitiesCard :opportunity="opportunity"></ManageOpportunitiesCard>
      </div>
      
  </div>
</template>

<script>
    import ManageOpportunitiesCard from "~/components/ManageOpportunitiesCard.vue"

    export default {
        data(){
            return {
                opportunity: {
                    id : "1232-234-1234-23",
                    likes_count: 20,
                    liked: 1,
                    title: "Opportunity Title",
                    content: "Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos repellendus mollitia, deserunt expedita corrupti laboriosam beatae pariatur perferendis quo culpa, nam repellat hic ullam vel ea tempore veniam et. Deleniti praesentium vitae expedita natus necessitatibus, consectetur odit vero, voluptas quod et quidem earum repellat facilis, velit eligendi nam voluptate esse deserunt. Numquam ex veritatis iure illo velit, enim quis distinctio impedit laudantium hic culpa, dolorum aut vitae mollitia nesciunt inventore ab quibusdam amet perferendis harum et? Ex omnis repudiandae eos, voluptatem quod et dolore quibusdam! Deleniti quia sit iusto dolore ratione voluptatem quaerat ab debitis at, saepe aliquam aut veniam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos repellendus mollitia, deserunt expedita corrupti laboriosam beatae pariatur perferendis quo culpa, nam repellat hic ullam vel ea tempore veniam et. Deleniti praesentium vitae expedita natus necessitatibus, consectetur odit vero, voluptas quod et quidem earum repellat facilis, velit eligendi nam voluptate esse deserunt. Numquam ex veritatis iure illo velit, enim quis distinctio impedit laudantium hic culpa, dolorum aut vitae mollitia nesciunt inventore ab quibusdam amet perferendis harum et? Ex omnis repudiandae eos, voluptatem quod et dolore quibusdam! Deleniti quia sit iusto dolore ratione voluptatem quaerat ab debitis at, saepe aliquam aut veniam. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos repellendus mollitia, deserunt expedita corrupti laboriosam beatae pariatur perferendis quo culpa, nam repellat hic ullam vel ea tempore veniam et. Deleniti praesentium vitae expedita natus necessitatibus, consectetur odit vero, voluptas quod et quidem earum repellat facilis, velit eligendi nam voluptate esse deserunt. Numquam ex veritatis iure illo velit, enim quis distinctio impedit laudantium hic culpa, dolorum aut vitae mollitia nesciunt inventore ab quibusdam amet perferendis harum et? Ex omnis repudiandae eos, voluptatem quod et dolore quibusdam! Deleniti quia sit iusto dolore ratione voluptatem quaerat ab debitis at, saepe aliquam aut veniam.",
                    link: "google.com"
                }
            }
        },
        components: {
            ManageOpportunitiesCard
        }
    }
</script>

<style>
    .admin-request-profile-view{
        height: 100%;
        width: 100%;

        max-width: 100%;
        overflow: hidden;
        /* overflow-y:auto; */

        background: white;
        border-radius: 10px;
        overflow: hidden;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        color: black;
    }

    .admin-request-profile-view > .header{
        color: #3A2C51;
        font-size: 1rem;
        font-weight: bolder;
    }

    .admin-manage-opportunity-container{
        width: 70%;
    }
</style>