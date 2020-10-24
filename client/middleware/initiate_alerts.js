export default async ({$axios, $auth, store}) => {
    if($auth.loggedIn){
        try{
            store.dispatch("alerts/getInitAlerts")
        }catch(e){}
    }
}