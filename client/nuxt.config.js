// import path from 'path'
// import fs from 'fs'

require('dotenv').config()

export default {
  mode: 'universal',
  env: {
    VUE_APP_GOOGLE_MAPS_API_KEY: process.env.VUE_APP_GOOGLE_MAPS_API_KEY,
  },

  /*
  ** Headers of the page
  */
  server: {
    port: 3000, // default: 3000
    host: '0.0.0.0', // default: localhost
   //  https: {
	  //   key: fs.readFileSync(path.resolve(__dirname, 'server.key')),
	  //   cert: fs.readFileSync(path.resolve(__dirname, 'server.crt'))
  	// }
  },
  head: {
    title: process.env.npm_package_name || '',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: process.env.npm_package_description || '' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ],
    script: [
      // {src: process.env.WEB_SOCKET_BASE_URL.replace(/\/+$/, '') + "/socket.io/socket.io.js"},
      // {src: process.env.WEB_SOCKET_BASE_URL.replace(/\/+$/, '') + "/easyrtc/easyrtc.js"},
    ]
  },
  /*
  ** Customize the progress-bar color
  */
  loading: {
    color: '#0084ff',
    height: '3px',
    continuous: true,
  },
  /*
  ** Global CSS
  */
  css: [
    {src: "~assets/css/style.css", lang: "css"}
  ],
  /*
  ** Plugins to load before mounting the App
  */
  plugins: [
    '~/plugins/url-to-file.js',
    '~/plugins/download.js',
    '@/plugins/google-maps',
    "~/plugins/initiate_laravel_echo.client.js",
    "~/plugins/windowresizeevent.client.js"
  ],
  /*
  ** Nuxt.js dev-modules
  */
  buildModules: [
    [
      '@nuxtjs/dotenv',
      {}
    ],
  ],
  /*
  ** Nuxt.js modules
  */
  modules: [
    '@nuxtjs/axios',
    '@nuxtjs/auth',
    
    "~/io"
  ],
  /*
  ** Build configuration
  */
  build: {
    /*
    ** You can extend webpack config here
    */
   extractCSS: true,
    extend (config, ctx) {
    }
  },

  axios: {
    // baseURL : "http://aboutcert.test"
    // baseURL: "http://192.168.43.235/aboutcert/public/"
    baseURL: process.env.BACKEND_BASE_URL || "http://localhost",
    headers: {
      'Access-Control-Allow-Origin': '*',
    },
    // proxyHeaders: true,
    // proxy: true
  },

  router: {
    middleware: ["initiate_alerts", "laravel_echo_middleware"]
  },
  auth: {
    redirect: {
      login: '/login',
      logout: '/',
      callback: '/login',
      home: '/updates'
    },
    strategies: {
      password_grant: {
        _scheme: "local",
        endpoints: {
          login: {
            url: '/api/login',
            method: 'post',
            propertyName: 'access_token'
          },
          logout: { url: '/api/logout', method: 'post' },
          user: { url: '/api/auth/user', method: 'get', propertyName: 'user' }
        },
        // tokenRequired: true,
        // tokenType: 'bearer'
      }
    }
  }
}
