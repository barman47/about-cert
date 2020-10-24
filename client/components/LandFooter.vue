<template>
    <section id="footer-container">
      <div id="footer">
        <div class="" >
          <h3 style="margin: 0.2rem;">Take a tour it's</h3>
          <h3 style="margin: 0.2rem; margin-bottom: 2rem;"><span style="color: #00FFA9;">free</span></h3>
          <button class="start-up-now-button">Start Up Now</button>
        </div>
        <div class="features-list-column">
          <div class="features-list">
            <h5 class="header">Features</h5>
            <ul class="features-list-ul" style="padding-inline-start: 0;">
              <li>Create Events</li>
              <li>Create CV</li>
              <li>Sign Documents</li>
              <li>Create Documents</li>
            </ul>
          </div>
        </div>
        <div class="features-list-column">
          <div class="features-list">
            <h5 class="header">Quick Links</h5>
            <ul class="features-list-ul" style="padding-inline-start: 0;">
              <li>Blog</li>
            </ul>
          </div>
<!--
          <form action="" class="form">
            <div class="form-group">
              <label for="" class="label">Subscribe to our newsletter</label>
            </div>
          </form> -->
        </div>
      </div>
      <div class="footer-footer">
        <small> &copy; AboutCert</small>
      </div>
    </section>
</template>

<script>
export default {

}
</script>

<style scoped>

#footer-container{
  background: #00052e;
  color: white;
  padding: 0.5rem;
}

#footer{
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  padding: 0.5rem;
}

.footer-footer small{
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6rem;
    color: #969696;
}

.start-up-now-button{
  color: white;
  background: #00FFA9;
  border-radius: 50px;
  font-size: 0.8rem;
  border: none;
  padding: 0.5rem;
  padding-right: 1rem;
  padding-left: 1rem;
  margin-right: 1rem;
  margin-left: 1rem;
  width: calc(100% - 2rem);
}

.features-list-column{
  display: flex;
  align-items: flex-start;
  justify-content: center;
}

.features-list{
  display: inline-block;
}

.features-list .header{
  font-size: 0.9rem;
  margin-top: 0.2rem;
  margin-bottom: 0.2rem;
}

.features-list .features-list-ul{
    list-style: none;
    font-size: 0.8rem;
}

.features-list .features-list-ul li{
    font-size: 0.6rem;
    color: #969696;
    height: 1rem;
    display: flex;
    align-items: center;
}

@media (max-width: 767.98px) {
    #footer-container {
        grid-template-rows: 1fr;
    }
}

@media (max-width: 575.98px) {
  .start-up-now-button{
    margin: 0;
    font-size: 0.6rem;
    /*white-space: nowrap;*/
    padding: 0.5rem;
  }
}

@media (max-width: 575.98px){
  
}

</style>
