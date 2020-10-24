<template>
	<div class="team">
		<div class="team-container">
			<div v-for="(member, index) in teamMembers" :key="member.name" class="team-member">
				<div class="content" :style="{backgroundImage: 'url(\''+ member.display_photo +'\')'}">
					<div class="overlay" :style="{background: member.backgroundOverlay}"></div>
					<div class="text-details">
						<span class="name">{{member.name}}</span>
						<span class="position">{{member.position.short}}</span>
					</div>
					<div class="team-member-expand-button" @click="showMember(index)"></div>
				</div>
			</div>
		</div>
		<div class="team-member-details-container" v-if="memberInViewIndex != undefined">
			<div class="team-member-details">
				<div class="close-button" @click="hideMember()">X</div>
				<div class="left" :style="{backgroundImage: 'url(\''+ memberInView.display_photo +'\')'}">
					<div class="social-group">
						<div class="twitter"></div>
						<div class="instagram"></div>
						<div class="linkedin"></div>
					</div>
				</div>
				<div class="right">
					<h2 class="name">{{memberInView.name}}</h2>
					<h6 class="position">{{memberInView.position.full}} ({{memberInView.position.short}})</h6>
					<p class="text">{{memberInView.quote}}</p>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data(){
			return {
				teamMembers: [
		          {
		            name: "David Adegoke",
		            position: {
		            	short: "C.E.O.",
		            	full: "Chief Executive Officer"
		            },
		            display_photo: "/team-members/adegoke.jpg",
		            backgroundOverlay: "rgba(96, 0, 255, 0.2)",
		            quote: "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo"
		          },
		          {
		            name: "Francis Ajadioni",
		            position: {
		            	short: "H.R.M.",
		            	full: "Human Resource Manager"
		            },
		            display_photo: "/team-members/francis.jpg",
		            backgroundOverlay: "rgba(255, 0, 0, 0.2)",
		            quote: "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo"
		          },
		          {
		            name: "David Oyinbo",
		            position: {
		            	short: "T.O.",
		            	full: "Technical Officer"
		            },
		            display_photo: "/team-members/oyinbo.jpg",
		            backgroundOverlay: "rgba(255, 68, 191, 0.2)",
		            quote: "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo"
		          },
		          {
		            name: "Raymond Oloyede",
		            position: {
		            	short: "G.D.",
		            	full: "Graphics Designer"
		            },
		            display_photo: "/team-members/rex.jpg",
		            backgroundOverlay: "rgba(255, 199, 1, 0.35)",
		            quote: "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo"
		          },
		          {
		            name: "Segun Solaru",
		            position: {
		            	short: "UX.D.",
		            	full: "UX Designer"
		            },
		            display_photo: "/team-members/segun.jpg",
		            backgroundOverlay: "rgba(199, 76, 77, 0.3)",
		            quote: "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo"
		          },
		          {
		            name: "Christiana Eduru",
		            position: {
		            	short: "B.A.",
		            	full: "Business Analyst"
		            },
		            display_photo: "/team-members/oyinbo.jpg",
		            backgroundOverlay: "rgba(0, 163, 255, 0.3)",
		            quote: "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo"
		          },
		        ],
		        memberInViewIndex: undefined
			}
		},
		computed: {
			memberInView(){
				if(this.memberInViewIndex === undefined)
					return undefined
				return this.teamMembers[this.memberInViewIndex]
			}
		},
		methods: {
			showMember(index){
				this.memberInViewIndex = index
			},
			hideMember(){
				this.memberInViewIndex = undefined
			}
		}
	}
</script>

<style  scoped>
	.team{
		width: 100%;
		overflow: hidden;
		max-width: 100vw;
		padding: 1rem;
		padding-bottom: 2rem;
		position: relative;

		--team-member-content-height : 15rem;
		--team-member-expand-button-size: 3.5rem;
	}
	.team-container{
		display: grid;
		grid-template-columns: repeat(3, 1fr);
		grid-column-gap: 1rem;
		grid-row-gap: 2rem;
	}

	.team-member{
		display: grid;
		align-items: center;
		justify-content: center;
	}

	.team-member .content{
		height: var(--team-member-content-height);
		width: var(--team-member-content-height);
		border-radius: 50%;
		background-position: center center;
		background-repeat: no-repeat;
		background-size: cover;
		position: relative;
	}

	.team-member .content .overlay{
		position: absolute;
		top: 0;
		right: 0;
		bottom: 0;
		left:0;
		background: rgba(255, 0, 0, 0.35);
		border-radius: 50%;
	}

	.team-member .text-details{
		display: flex;
		flex-direction: column;
		text-align: center;
		align-items: center;
		justify-content: flex-end;
		padding-bottom: 2rem;
		height: 100%;
		color: white;

		z-index: 2;
	    position: absolute;
	    top: 0;
	    right: 0;
	    bottom: 0;
	    left: 0;
	}

	.team-member .text-details * {
		font-family: Poppins;
		font-style: normal;
	}

	.team-member .text-details .position{
		color: #27DEBF;
	}

	.team-member-expand-button{
		position: absolute;
		z-index: 3;
		bottom:10px;
		right: 10px;
		background: white;
		height: var(--team-member-expand-button-size);
		width: var(--team-member-expand-button-size);
		border-radius: 50%;
		cursor: pointer;
		background-image: url("/png/team-expand.png");
		padding: calc(0.35 * var(--team-member-expand-button-size));
		/*background-size: calc(0.3 * var(--team-member-expand-button-size)) calc(0.3 * var(--team-member-expand-button-size));*/
		background-origin: content-box;
		background-repeat: no-repeat;
		background-size: contain;
		background-position: center center;
	}

	.team-member-details-container{
		position: absolute;
		z-index: 10;
		background: rgba(0,0,0, 0.7);
		top: 0;
		right: 0;
		bottom: 0;
		left: 0;

		display: flex;
		align-items: center;
		justify-content: center;
	}

	.team-member-details{
		width: 60%;
		min-height: 80%;
		color: white;

		display: grid;
		grid-template-columns: 2fr 3fr;

		background: #555286;
		position: relative;
	}

	.team-member-details * {
		font-family: Poppins;
		font-style: normal;
	}

	.team-member-details .close-button{
		--close-button-size: 3rem;
		position: absolute;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1rem;
		height: var(--close-button-size);
		width: var(--close-button-size);
		z-index: 15;
		border-radius: 50%;

		top: calc(-1 * (var(--close-button-size) / 2));
		right: calc(-1 * (var(--close-button-size) / 2));
		background: rgba(0,0,0,0.6);

		cursor: pointer;
	}

	.team-member-details .left{
		/*background-image: url("/team-members/adegoke.jpg");*/
		background-position: center 0;
		background-size: cover;
		background-repeat: no-repeat;
		position: relative;

		display: flex;
		align-items: flex-end;
		justify-content: center;
	}

	.team-member-details .left::before{
		content: "";
		position: absolute;
		right: 0;
		left: 0;
		top: 0;
		bottom: 0;
		background: rgba(0, 0, 0, 0.4);
	}

	.team-member-details .left .social-group{
		display: flex;
		align-items: center;
		justify-content: space-around;

		margin-bottom: 1rem;
		margin-top: 1rem;

		--social-group-icon-size: 2rem;

		width: 100%;
		position: absolute;
		left: 0;
		bottom:0;
		z-index: 15;
	}

	.team-member-details .left .social-group > div {
		height: var(--social-group-icon-size);
		width: var(--social-group-icon-size);
		padding: 0.4rem;
		border-radius: 50%;

		background-color: white;
		background-position: center center;
		background-size: contain;
		background-repeat: no-repeat;
		background-origin: content-box;

		cursor: pointer;
	}

	.team-member-details .left .social-group > div.twitter{
		background-image: url("/png/twitter 1.png");
	}

	.team-member-details .left .social-group > div.instagram{
		background-image: url("/png/instagram.png");
	}

	.team-member-details .left .social-group > div.linkedin{
		background-image: url("/png/linkedin.png");
	}

	.team-member-details .right{
		padding: 1rem;
	}

	.team-member-details .right .name{
		display: inline-block;
		margin-top: 2rem;
		margin-bottom: 0.3rem;
		position: relative;
	}

	.team-member-details .right .name::after{
		content: '';
		position: absolute;
		height: 100%;
		width: 2rem;
		top: -10px;
		right: calc(0- 2rem -10px);
		background-image: url("/png/team-members-icon.png");
		background-size: contain;
		background-repeat: no-repeat;
		background-position: center center;
	}

	.team-member-details .right .position{
		margin-top: 0.3rem;
		margin-bottom: 0.3rem;
	}

	.team-member-details .right .text {
		color: #9B9B9B;
		font-size: 0.7rem;
	}

	@media (max-width: 1199.98px) { 

	}

	@media (max-width: 991.98px) {
		.team{
			--team-member-content-height : 12rem;
			--team-member-expand-button-size: 3rem;
		}

		.team-member-details{
			width: 70%;
		}
	}

	@media (max-width: 767.98px) {
		.team-container{
			grid-template-columns: repeat(2, 1fr);
		}

		.team-member-details{
			width: 80%;
		}
	}

	@media (max-width: 575.98px) {
		.team{
			--team-member-content-height : 8rem;
			--team-member-expand-button-size: 2rem;
		}


		.team-member-expand-button{
			bottom:7px;
			right: 7px;
		}

		.team-member .text-details{
			padding-bottom: 1rem;
			font-size: 0.8rem;
		}

		.team-member-details{
			width: 90%;
		}
	}
</style>