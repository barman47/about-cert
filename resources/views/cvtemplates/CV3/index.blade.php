<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Resume/CV 3</title>
  <style>
    @import url("https://fonts.googleapis.com/css?family=Montserrat:400,500,700&display=swap");

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      list-style: none;
      font-family: "Montserrat", sans-serif;
    }

    body {
      background: #585c68;
      font-size: 14px;
      line-height: 22px;
      color: #555555;
    }

    .bold {
      font-weight: 700;
      font-size: 20px;
      text-transform: uppercase;
    }

    .semi-bold {
      font-weight: 500;
      font-size: 16px;
    }

    .resume {
      width: 800px;
      height: auto;
      display: flex;
      margin: 0 auto;
    }

    .resume .resume_left {
      width: 380px;
      background: #fff;
      padding-top: 100px;
      position: relative;
    }

    .resume .resume_left .resume_profile {
      width: 100%;
      height: 280px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .resume .resume_left .resume_profile img {
      width: 85%;
      height: auto;
      border-radius: 50%;
      border: 2px solid #111;
    }

    .resume .resume_left .resume_content {
      padding: 0 25px;
    }

    .resume .title {
      margin-bottom: 20px;
      width: 100%;
    }

    .resume .resume_left .bold {
      color: #111;
    }

    .resume .resume_left .regular {
      color: #111;
    }

    .resume .resume_item {
      padding: 25px 0;
    }

    .resume .resume_left .resume_item:last-child,
    .resume .resume_right .resume_item:last-child {
      border-bottom: 0px;
    }

    .resume .resume_left ul li {
      display: flex;
      margin-bottom: 10px;
      align-items: center;
    }

    .resume .resume_left ul li .icon {
      width: 35px;
      height: 35px;
      background: #fff;
      color: #111;
      border-radius: 50%;
      margin-right: 15px;
      font-size: 16px;
      position: relative;
    }

    .resume .icon i,
    .resume .resume_right .resume_hobby ul li i {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
    }

    .resume .resume_left ul li .data {
      color: #111;
    }

    .resume .resume_right .resume_skills ul {
      display: flex;
      flex-wrap: wrap;
    }

    .resume .resume_right .resume_skills ul li {
      display: flex;
      margin-bottom: 10px;
      color: #111;
      justify-content: space-between;
      align-items: center;
      width: 50%;
    }

    .resume .resume_right .resume_skills ul li .skill_name {
      width: 25%;
    }

    .resume .resume_right .resume_skills ul li .skill_progress {
      width: 60%;
      margin: 0 5px;
      height: 12px;
      border-radius: 10px;
      background: #585c68;
      position: relative;
    }

    .resume .resume_right .resume_skills ul li .skill_per {
      width: 15%;
    }

    .resume .resume_right .resume_skills ul li .skill_progress span {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      background: #121b38;
      border-radius: inherit;
    }

    .resume .resume_left .resume_social .semi-bold {
      color: #fff;
      margin-bottom: 3px;
    }

    .resume .resume_right {
      width: 420px;
      background: #fff;
      padding: 25px;
      position: relative;
      padding-top: 170px;
      position: relative;
    }

    .resume .resume_right::before {
      content: '';
      background-color: #3F51B5;
      position: absolute;
      left: 50%;
      top: 0;
      transform: translateX(-50%);
      width: 80%;
      height: 170px;
    }

    .resume .resume_right .bold {
      color: #111;
    }

    .resume .resume_right .resume_work ul,
    .resume .resume_right .resume_education ul {
      padding-left: 40px;
      overflow: hidden;
    }

    .resume .resume_right ul li {
      position: relative;
    }

    .resume .resume_right ul li .date {
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 15px;
    }

    .resume .resume_right ul li .info {
      margin-bottom: 20px;
    }

    .resume .resume_right ul li:last-child .info {
      margin-bottom: 0;
    }

    .resume .resume_right .resume_work ul li:before,
    .resume .resume_right .resume_education ul li:before {
      content: "";
      position: absolute;
      top: 5px;
      left: -25px;
      width: 6px;
      height: 6px;
      border-radius: 50%;
      border: 2px solid #111;
    }

    .resume .resume_right .resume_work ul li:after,
    .resume .resume_right .resume_education ul li:after {
      content: "";
      position: absolute;
      top: 14px;
      left: -21px;
      width: 2px;
      height: 115px;
      background: #111;
    }

    .resume .resume_right .resume_hobby ul {
      display: flex;
      justify-content: space-between;
    }

    .resume .resume_right .resume_hobby ul li {
      width: 80px;
      height: 80px;
      border: 2px solid #111;
      border-radius: 50%;
      position: relative;
      color: #111;
    }

    .resume .resume_right .resume_hobby ul li i {
      font-size: 30px;
    }

    .resume .resume_right .resume_hobby ul li:before {
      content: "";
      position: absolute;
      top: 40px;
      right: -52px;
      width: 50px;
      height: 2px;
      background: #111;
    }

    .resume .resume_right .resume_hobby ul li:last-child:before {
      display: none;
    }


    .resume .resume_left .resume_bio ul li {
      display: block;
      padding-left: 10px;
    }

    .resume_name {
      height: 200px;
      display: flex;
      align-items: center;
      margin-top: 50px;
    }

    .resume_name h2.first {
      margin-bottom: 48px;
      margin-top: 30px;
      font-size: 45px;
    }

    .resume_name h2.last {
      margin-bottom: 48px;
      font-size: 90px;
    }

    .resume_name div.position {
      width: 100%;
      text-align: right;
      font-size: 18px;
      position: relative;
    }

    .resume_name div.position p {
      background-color: #fff;
      z-index: 5;
    }

    .resume_name div.position::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 2px;
      width: 36%;
      height: 2px;
      background: #3F51B5;
    }

    .resume_name p {
      font-size: 22px;
    }

    .semi-bold {
      font-size: 16px;
      font-weight: 500;
      color: #111;
    }

    .semi-title {
      font-size: 16px;
      font-weight: 500;
      margin-bottom: 20px;
      padding-left: 10px;
    }

    .resume_skillz ul {
      display: flex;
      flex-wrap: wrap;
    }

    .resume_skillz ul li {
      padding: 5px 10px;
      background-color: #121b38;
      color: #fff;
      margin: 5px;
      border-radius: 15px;
    }

    .resume_project ul li {
      display: inline-flex;
    }

    .resume_project ul li .date {
      width: 30%;
    }

    .resume_project ul li .info {
      width: 70%;
    }

    .resume_app_logo {
      position: absolute;
      bottom: 10px;
      right: 10px;
    }

    .resume_interests ul {
      display: inline-flex;
      flex-wrap: wrap;
      padding-left: 10px;
    }

    .resume_interests ul li {
      padding: 0px 10px;
      position: relative;
    }

    .resume_interests ul li::after {
      content: "";
      position: absolute;
      top: 0px;
      right: 0px;
      width: 2px;
      height: 100%;
      background: #6f6f6f;
    }

    .resume_language ul {
      padding-left: 10px;
    }

    svg.container {
      display: block;
      margin: 0;
      height: 50px;
      width: 60px;
    }

    .resume_interests ul li:last-of-type::after {
      content: none;
    }

    .progress {
      fill: #fff;
      stroke: #121b38;
      stroke-width: 3px;
      stroke-linecap: round;
      transform-origin: center;
    }

  </style>

</head>
<body>
<!-- partial:index.partial.html -->
<script>
window.FontAwesomeKitConfig = {"asyncLoading":{"enabled":true},"autoA11y":{"enabled":true},"baseUrl":"https://kit-free.fontawesome.com","license":"free","method":"css","minify":{"enabled":true},"v4shim":{"enabled":false},"version":"latest"};
!function(){!function(){if(!(void 0===window.Element||"classList"in document.documentElement)){var e,t,n,i=Array.prototype,o=i.push,a=i.splice,s=i.join;r.prototype={add:function(e){this.contains(e)||(o.call(this,e),this.el.className=this.toString())},contains:function(e){return-1!=this.el.className.indexOf(e)},item:function(e){return this[e]||null},remove:function(e){if(this.contains(e)){for(var t=0;t<this.length&&this[t]!=e;t++);a.call(this,t,1),this.el.className=this.toString()}},toString:function(){return s.call(this," ")},toggle:function(e){return this.contains(e)?this.remove(e):this.add(e),this.contains(e)}},window.DOMTokenList=r,e=Element.prototype,t="classList",n=function(){return new r(this)},Object.defineProperty?Object.defineProperty(e,t,{get:n}):e.__defineGetter__(t,n)}function r(e){for(var t=(this.el=e).className.replace(/^\s+|\s+$/g,"").split(/\s+/),n=0;n<t.length;n++)o.call(this,t[n])}}();function f(e){var t,n,i,o;prefixesArray=e||["fa"],prefixesSelectorString="."+Array.prototype.join.call(e,",."),t=document.querySelectorAll(prefixesSelectorString),Array.prototype.forEach.call(t,function(e){n=e.getAttribute("title"),e.setAttribute("aria-hidden","true"),i=!e.nextElementSibling||!e.nextElementSibling.classList.contains("sr-only"),n&&i&&((o=document.createElement("span")).innerHTML=n,o.classList.add("sr-only"),e.parentNode.insertBefore(o,e.nextSibling))})}var e,t,u=function(e){var t=document.createElement("link");t.href=e,t.media="all",t.rel="stylesheet",document.getElementsByTagName("head")[0].appendChild(t)},m=function(e){!function(e,t,n){var i,o=window.document,a=o.createElement("link");if(t)i=t;else{var s=(o.body||o.getElementsByTagName("head")[0]).childNodes;i=s[s.length-1]}var r=o.styleSheets;a.rel="stylesheet",a.href=e,a.media="only x",function e(t){if(o.body)return t();setTimeout(function(){e(t)})}(function(){i.parentNode.insertBefore(a,t?i:i.nextSibling)});var l=function(e){for(var t=a.href,n=r.length;n--;)if(r[n].href===t)return e();setTimeout(function(){l(e)})};function c(){a.addEventListener&&a.removeEventListener("load",c),a.media=n||"all"}a.addEventListener&&a.addEventListener("load",c),(a.onloadcssdefined=l)(c)}(e)},n=function(e,t){var n=t&&void 0!==t.autoFetchSvg?t.autoFetchSvg:void 0,i=t&&void 0!==t.async?t.async:void 0,o=t&&void 0!==t.autoA11y?t.autoA11y:void 0,a=document.createElement("script"),s=document.scripts[0];a.src=e,void 0!==o&&a.setAttribute("data-auto-a11y",o?"true":"false"),n&&(a.setAttributeNode(document.createAttribute("data-auto-fetch-svg")),a.setAttribute("data-fetch-svg-from",t.fetchSvgFrom)),i&&a.setAttributeNode(document.createAttribute("defer")),s.parentNode.appendChild(a)};function h(e,t){var n=t&&t.shim?e.license+"-v4-shims":e.license,i=t&&t.minify?".min":"";return e.baseUrl+"/releases/"+("latest"===e.version?"latest":"v".concat(e.version))+"/"+e.method+"/"+n+i+"."+e.method}try{if(window.FontAwesomeKitConfig){var i=window.FontAwesomeKitConfig;"js"===i.method&&(t={async:(e=i).asyncLoading.enabled,autoA11y:e.autoA11y.enabled},"pro"===e.license&&(t.autoFetchSvg=!0,t.fetchSvgFrom=e.baseUrl+"/releases/"+("latest"===e.version?"latest":"v".concat(e.version))+"/svgs"),e.v4shim.enabled&&n(h(e,{shim:!0,minify:e.minify.enabled})),n(h(e,{minify:e.minify.enabled}),t)),"css"===i.method&&function(e){var t,n,i,o,a,s,r,l,c=f.bind(f,["fa","fab","fas","far","fal"]);e.autoA11y.enabled&&(n=c,o=[],a=document,s=a.documentElement.doScroll,r="DOMContentLoaded",(l=(s?/^loaded|^c/:/^loaded|^i|^c/).test(a.readyState))||a.addEventListener(r,i=function(){for(a.removeEventListener(r,i),l=1;i=o.shift();)i()}),l?setTimeout(n,0):o.push(n),t=c,"undefined"!=typeof MutationObserver&&new MutationObserver(t).observe(document,{childList:!0,subtree:!0})),e.v4shim.enabled&&(e.asyncLoading.enabled?m(h(e,{shim:!0,minify:e.minify.enabled})):u(h(e,{shim:!0,minify:e.minify.enabled})));var d=h(e,{minify:e.minify.enabled});e.asyncLoading.enabled?m(d):u(d)}(i)}}catch(e){}}();
</script>

<div class="resume">
    <div class="resume_right">
        <div class="resume_item resume_about resume_name">
            <div class="title">
               <h2 class="first">{{$records->user->firstName}}</h2>
               <h2 class="last">{{$records->user->lastName}}</h2>
               <div class="position">
                 <p>{{$records->user->job_title}}</p>
               </div>
             </div>
        </div>
        <div class="resume_item resume_work">
            <div class="title">
               <p class="bold">Work Experience</p>
             </div>
            <ul>
              @foreach ($records->workExperiences as $experience)
                <li>
                  @php
                      $startYear = \Carbon\Carbon::create($experience->start_date)->format("Y");
                      $endYear = strtolower($experience->end_date) == "present" ? "present" : \Carbon\Carbon::create($experience->end_date)->format("Y");
                  @endphp
                  <p class="semi-bold">{{$experience->company_name}} - {{"$startYear-$endYear"}}</p>
                  <p class="semi-bold">{{$experience->position}}</p>
                  <p>{{$experience->work_description}}</p>
                </li>
              @endforeach
            </ul>
        </div>
        <div class="resume_item resume_education resume_project">
          <div class="title">
               <p class="bold">PERSONAL PROJECT</p>
             </div>
              <ul>
                @foreach ($records->projects as $project)
                  <li>
                      <div class="date semi-bold">{{$project->title}}</div> 
                      <div class="info">
                        <p>{{$project->description}}</p>
                      </div>
                  </li>
                @endforeach
            </ul>
        </div>

        <div class="resume_item resume_education resume_project">
            <div class="title">
                 <p class="bold">CERTIFICATION</p>
               </div>
                <ul>
                  @foreach ($records->certificates as $certificate)
                    <li>
                        <div class="date semi-bold">{{$certificate->title}}</div> 
                        <div class="info">
                          <p>{{$certificate->description}}</p>
                        </div>
                    </li>
                  @endforeach
              </ul>
          </div>

    </div>

   <div class="resume_left">
     <div class="resume_content">

       <div class="resume_item resume_info resume_bio">
          <ul>
             <li>
                 <p class="semi-bold">Email</p>
                 <p>{{$records->user->email}}</p>
             </li>
             @if($records->user->entity && $records->user->entity->phone_number && $records->user->entity->phone_number != "")
              <li>
                  <p class="semi-bold">Phone</p>
                  <p>{{$records->user->entity->phone_number}}</p>
              </li>
            @endif
            @if($records->user->address && $records->user->address != "")
              <li>
                <p class="semi-bold">Address</p>
                <p>{{$records->user->address}}</p>
              </li>
            @endif
          <li>
            <p class="semi-bold">Date Of Birth</p>
            <p>January 20th 1992</p>
        </li>
        <li>
          <p class="semi-bold">Nationality</p>
          <p>Nigerian - American</p>
      </li>
             
          </ul>
      </div>

      <div class="resume_item resume_info resume_bio">
          <p class="semi-title">EDUCATION</p>
          <ul>
             <li>
                <p>Caristas University</p>
                <p>Bsc Economics</p>
                 <p class="semi-bold">2009 - 2013</p>
             </li>
             <li>
                <p>Metropolitan Film</p>
                <p>School</p>
                 <p class="semi-bold">2013 - 2014</p>
             </li>
             
          </ul>
      </div>

      <div class="resume_item resume_info resume_skillz">
          <p class="semi-title semi-bold">SKILL</p>
          <ul>
            @foreach ($records->skills as $skill)
              <li>
                {{$skill->name}}
              </li>
            @endforeach
          </ul>
      </div>

      <div class="resume_item resume_info resume_interests">
        <p class="semi-title semi-bold">INTEREST</p>
        <ul>
          @foreach($records->interests as $interest)
            <li>{{$interest->name}}</li>
          @endforeach
        </ul>
      </div>

      <div class="resume_item resume_info resume_language">
        <p class="semi-title semi-bold">LANGUAGE</p>
        <ul>
          @foreach ($records->languages as $language)
            <li>
              <svg class="container">
                  <g id="" style="
                  position: absolute;
                  top: 0px;
                  left: 0px;
                  transform: translate(-65px, -75px);
              " >
                    <circle class="progress" id="one" cx="100" cy="100" r="10px"/>
                  </g>
              </svg>
              {{$language->name}}
            </li>
          @endforeach
        </ul>
      </div>

     </div>
     <img class="resume_app_logo" src="{{asset('/about-cert.png')}}">     
  </div>

  
</div>
<!-- partial -->
  
</body>
</html>
