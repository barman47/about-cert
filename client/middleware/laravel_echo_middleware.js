export default async ({$axios, $auth, store}) => {
    if(!process.server && !window.Echo){
        store.dispatch("laravel_echo/initializeLaravelEcho")
	}
}