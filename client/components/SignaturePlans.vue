<template>
  <div class="signature-plan">
    <div class="top">
      <div class="close-button" @click="$emit('closed')">X</div>
    </div>
    <div class="content">
      <h5 class="message">
        Sorry, you don’t have access to this feature just yet,
        <br />choose a plan:
      </h5>
      <div class="signature-plan-item" @click="subscribe('one_time')">
        <div>
          <h4 class="signature-plan-item-header">One Time</h4>
          <div class="image" style="background-image: url('/png/signature-form (2).png')"></div>
        </div>
        <div>
          <span class="signature-plan-item-text">
            Pay a sum of
            <span class="price">N200</span> for a single signing
          </span>
          <div class="button"></div>
        </div>
      </div>
      <div class="signature-plan-item" @click="subscribe('monthly')">
        <div>
          <h4 class="signature-plan-item-header">Monthly</h4>
          <div class="image" style="background-image: url('/png/signature-form (3).png')"></div>
        </div>
        <div>
          <span class="signature-plan-item-text">
            Pay a sum of
            <span class="price">N4,000</span> monthly for monthly signing
          </span>
          <div class="button"></div>
        </div>
      </div>
      <div class="signature-plan-item">
        <div>
          <h4 class="signature-plan-item-header" @click="subscribe('yearly')">Yearly</h4>
          <div class="image" style="background-image: url('/png/signature-form (1).png')"></div>
        </div>
        <div>
          <span class="signature-plan-item-text">
            Pay a sum of
            <span class="price">N40,000</span> for a annual signing
          </span>
          <div class="button"></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    subscribe(val) {
      if (!val) return;
      let planId = "";

      if (val == "one_time") planId = "single_document_signature";
      if (val == "monthly") planId = "monthly_document_signature";
      if (val == "yearly") planId = "annual_document_signature";

      if (!planId) return;

      const data = {
        plan: planId,
        redirect_url: process.env.CLIENT_BASE_URL + this.$nuxt.$route.fullPath
      };

      let attempt = this.$store.dispatch(
        "signature_priviledge/payForPriviledge",
        data
      );
      attempt.then(data => {
        let payment_link = data.payment_link;
        window.open(payment_link, "_self");
      });
    }
  }
};
</script>

<style scoped>
.signature-plan {
  display: flex;
  flex-direction: column;
  width: 100%;
  --signature-plan-top-height: 150px;
  overflow: hidden;
  border-radius: 10px;
  background: white;
}

.signature-plan .top {
  height: var(--signature-plan-top-height);
  background: white;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}
.signature-plan .top::before {
  content: "";
  background-image: url("/png/bell.png");
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center center;
  height: calc(var(--signature-plan-top-height) * 0.5);
  width: 100%;
}

.signature-plan .top::after {
  content: "";
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  height: 45%;
  background-image: url("/png/intersect.png");
  background-size: cover;
}

.signature-plan .top .close-button {
  height: 1.5rem;
  width: 1.5rem;
  cursor: pointer;
  position: absolute;
  top: 5px;
  right: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  color: black;
  font-weight: bold;
}

.signature-plan .content {
  padding: 1rem;
}

.signature-plan .content .message {
  color: #3a2c51;
}

.signature-plan-item {
  background: #f5f5f5;
  border-radius: 3px;
  padding: 0.5rem;
  margin-bottom: 1rem;
  cursor: pointer;
}

.signature-plan-item:hover {
  background: #e2e2e2;
}

.signature-plan-item:last-child {
  margin-bottom: 0;
}

.signature-plan-item > div {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.signature-plan-item-header {
  margin-top: 0.5rem;
  margin-bottom: 0.5rem;
  color: #141b5b;
  font-weight: normal;
}

.signature-plan-item-text {
  color: #141b5b;
  font-size: 0.8rem;
  font-style: normal;
  font-weight: normal;
}

.signature-plan-item-text .price {
  color: #0084ff;
  font-weight: lighter;
}

.signature-plan-item .button {
  height: 1rem;
  width: 1rem;
  padding: 0.2rem;
  background-origin: content-box;
  border-radius: 3px;
  border: solid 0.8px #0084ff;
  background-image: url("/png/blue-caret-right.png");
  background-position: center center;
  background-repeat: no-repeat;
  background-size: contain;
}

.signature-plan-item .image {
  height: 1.2rem;
  width: 1.2rem;
  border-radius: 3px;
  background-position: center center;
  background-size: cover;
  background-repeat: no-repeat;
}
</style>